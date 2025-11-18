const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const QRCode = require('qrcode');
const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');

const PORT = process.env.PORT || 3000;
const SECRET = process.env.WA_GATEWAY_SECRET || process.env.WHATSAPP_API_KEY || '';

const app = express();
app.use(cors());
app.use(bodyParser.json({ limit: '5mb' }));

// Puppeteer/Chromium launch options. In containers you normally need the
// no-sandbox flags; an explicit executable path can be supplied with
// PUPPETEER_EXECUTABLE_PATH if you use a custom Chromium build.
const puppeteerOpts = {
    headless: process.env.PUPPETEER_HEADLESS !== 'false',
    dumpio: true, // stream Chromium stdout/stderr to Node process (useful in containers)
    defaultViewport: null,
    ignoreHTTPSErrors: true,
    args: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-dev-shm-usage',
        '--disable-accelerated-2d-canvas',
        '--no-first-run',
        '--no-zygote',
        '--disable-gpu',
        '--disable-features=site-per-process',
        '--disable-background-timer-throttling',
        '--disable-renderer-backgrounding',
        '--disable-ipc-flooding-protection'
    ]
};
if (process.env.PUPPETEER_EXECUTABLE_PATH) puppeteerOpts.executablePath = process.env.PUPPETEER_EXECUTABLE_PATH;

const client = new Client({
    authStrategy: new LocalAuth({ clientId: 'wa-gateway' }),
    puppeteer: puppeteerOpts
});

// store latest QR data URL so we can serve it on a webpage
let latestQrDataUrl = null;
let clientReady = false;
let clientAuthenticated = false;
let lastSendResult = null;
let lastSendError = null;

client.on('qr', (qr) => {
    console.log('QR code received. Scan it with your WhatsApp mobile app (open WhatsApp -> Settings -> Linked devices).');
    qrcode.generate(qr, { small: true });

    // also create a browser-friendly data URL (PNG) so we can display it at /qr
    QRCode.toDataURL(qr, { errorCorrectionLevel: 'H' }, (err, url) => {
        if (!err) {
            latestQrDataUrl = url;
            console.log('QR data URL generated and available at /qr');
        } else {
            console.error('Failed to generate QR data URL', err);
        }
    });
});

client.on('ready', () => {
    clientReady = true;
    console.log('WhatsApp client is ready.');
});

client.on('authenticated', () => {
    clientAuthenticated = true;
    console.log('Authenticated. Session saved.');
});

client.on('auth_failure', msg => {
    clientAuthenticated = false;
    clientReady = false;
    console.error('Authentication failure', msg);
});

client.initialize();

// Helper to wait until clientReady is true, with timeout
const waitForClientReady = async (timeoutMs = 30000) => {
    const start = Date.now();
    while (!clientReady) {
        if (Date.now() - start > timeoutMs) throw new Error('Timeout waiting for WhatsApp client ready');
        await new Promise(r => setTimeout(r, 500));
    }
};

process.on('unhandledRejection', (reason) => {
    console.error('Unhandled Rejection at:', reason && reason.stack ? reason.stack : reason);
});

// Simple send endpoint. Expects Authorization: Bearer <secret>
app.post('/send', async (req, res) => {
    try {
        const auth = (req.headers['authorization'] || '');
        if (SECRET) {
            if (!auth || !auth.toLowerCase().startsWith('bearer ')) {
                return res.status(401).json({ error: 'Missing authorization' });
            }
            const token = auth.slice(7).trim();
            if (token !== SECRET) return res.status(403).json({ error: 'Invalid secret' });
        }

        const { to, message } = req.body || {};
        if (!to || !message) return res.status(400).json({ error: 'Missing to or message' });

        // normalize phone: keep digits only; if local 0 prefixed number assume NG (234)
        let phone = (to || '').toString().replace(/[^0-9]/g, '');
        if (!phone) return res.status(400).json({ error: 'Invalid to phone number' });
        if (phone.length >= 8 && phone[0] === '0') phone = '234' + phone.slice(1);
        const numberId = phone.includes('@') ? phone : (phone + '@c.us');

        if (!clientReady) {
            const msg = 'WhatsApp client not ready';
            console.error(msg);
            lastSendError = msg;
            return res.status(503).json({ error: msg });
        }

        // Attempt send, retry once on session-closed errors
        try {
            const sendRes = await client.sendMessage(numberId, message);
            lastSendResult = { ok: true, id: sendRes.id ? sendRes.id._serialized : null, to: phone, message: message, at: new Date().toISOString() };
            console.log('Send success', lastSendResult);
            return res.json(lastSendResult);
        } catch (e) {
            const errMsg = e && e.message ? e.message : String(e);
            console.error('Send error', e && e.stack ? e.stack : errMsg);
            lastSendError = errMsg;

            if (/session closed|Session closed|Session not found|Protocol error/i.test(errMsg)) {
                console.warn('Detected session-closed error — attempting to restart client and retry once');
                try {
                    try { await client.destroy(); } catch (destroyErr) { console.warn('Error during client.destroy()', destroyErr && destroyErr.message ? destroyErr.message : destroyErr); }
                    client.initialize();
                    await waitForClientReady(30000);
                    const retryRes = await client.sendMessage(numberId, message);
                    lastSendResult = { ok: true, id: retryRes.id ? retryRes.id._serialized : null, to: phone, message: message, at: new Date().toISOString(), retried: true };
                    console.log('Send retry success', lastSendResult);
                    return res.json(lastSendResult);
                } catch (retryErr) {
                    console.error('Retry failed', retryErr && retryErr.stack ? retryErr.stack : retryErr);
                    lastSendError = retryErr && retryErr.message ? retryErr.message : String(retryErr);
                }
            }

            const debug = process.env.APP_DEBUG === 'true' || process.env.APP_DEBUG === '1';
            const payload = { error: 'Send failed', detail: lastSendError };
            if (debug) payload.stack = e && e.stack ? e.stack : null;
            return res.status(500).json(payload);
        }
    } catch (e) {
        console.error('Send error', e && e.message ? e.message : e);
        lastSendError = e && e.message ? e.message : String(e);
        return res.status(500).json({ error: 'Send failed', detail: lastSendError });
    }
});

app.get('/', (req, res) => res.send('wa-gateway running'));

// status endpoint to help debug readiness and last send result
app.get('/status', (req, res) => {
    return res.json({ ok: true, ready: clientReady, authenticated: clientAuthenticated, lastSendResult, lastSendError });
});

// simple QR page so you can scan the code from a browser
app.get('/qr', (req, res) => {
    if (!latestQrDataUrl) {
        return res.send(`
            <html><head><title>wa-gateway — QR</title>
            <meta name="viewport" content="width=device-width,initial-scale=1"/>
            <style>body{background:#071022;color:#cfeff6;font-family:Inter,system-ui,Arial;padding:24px} .card{max-width:520px;margin:40px auto;padding:20px;border-radius:12px;background:rgba(255,255,255,0.03);box-shadow:0 6px 18px rgba(2,6,23,0.6)}</style>
            </head><body><div class="card"><h2>wa-gateway</h2><p>No QR available right now. Start the gateway and wait for the QR to appear in the terminal (or re-generate by removing the local session).</p></div></body></html>
        `);
    }

    return res.send(`
        <html>
        <head>
            <title>wa-gateway — Scan QR</title>
            <meta name="viewport" content="width=device-width,initial-scale=1"/>
            <style>body{background:#031124;color:#d7fbff;font-family:Inter,system-ui,Arial;padding:18px} .card{max-width:560px;margin:28px auto;padding:18px;border-radius:12px;background:linear-gradient(180deg,rgba(255,255,255,0.02),rgba(255,255,255,0.01));box-shadow:0 8px 30px rgba(2,6,23,0.7);text-align:center} img{width:260px;height:260px}</style>
        </head>
        <body>
            <div class="card">
                <h2 style="margin-bottom:6px">wa-gateway — link WhatsApp</h2>
                <p style="margin-top:0;margin-bottom:12px;color:#bdeff6">Open WhatsApp → Settings → Linked devices → Link a device, then scan this QR.</p>
                <img src="${latestQrDataUrl}" alt="WhatsApp QR code" />
                <p style="margin-top:12px;color:#9fe9f6;font-size:13px">If the QR expires, restart the gateway or delete the local session folder to re-generate.</p>
            </div>
        </body>
        </html>
    `);
});

app.listen(PORT, () => console.log(`wa-gateway listening on http://localhost:${PORT}`));
