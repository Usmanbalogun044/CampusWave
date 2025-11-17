wa-gateway — local WhatsApp gateway

This small service uses `whatsapp-web.js` to let you send WhatsApp messages programmatically from your personal WhatsApp account (via WhatsApp Web).

WARNING & notes
- This uses WhatsApp Web and a user session — it is not the official WhatsApp Business API. Running it may violate WhatsApp terms for programmatic messaging; use at your own risk.
- The session is stored locally using `whatsapp-web.js` `LocalAuth`. Scan the QR code once to link your WhatsApp account.
- Keep the service running where it can be reached by your Laravel app (local or server). For production you should use an official API provider.

Quick start

1. Install dependencies

```bash
cd wa-gateway
npm install
```

2. Start the gateway

```bash
WA_GATEWAY_SECRET=your_secret_here node index.js
```

- The terminal will print a QR code. Open WhatsApp -> Settings -> Linked Devices -> Link a device and scan the QR.
- After linking, the gateway prints `WhatsApp client is ready`.

Alternatively, you can open the QR in your browser at `http://localhost:3000/qr` (the gateway generates a browser-friendly PNG data URL automatically). This is useful if you prefer scanning from a browser instead of the terminal.

3. Configure your Laravel app

In your Laravel `.env` set:

```
WHATSAPP_API_URL=http://localhost:3000/send
WHATSAPP_API_KEY=your_secret_here
```

The existing app (`PurchaseController::adminAccept`) will POST to `WHATSAPP_API_URL` with the `Authorization: Bearer <WHATSAPP_API_KEY>` header.

Example request body sent by Laravel (JSON):

```
{
  "to": "2348012345678",
  "message": "Your payment is confirmed"
}
```

4. Keep the gateway running while you want automated WhatsApp notifications to work.

If you want help deploying this gateway or making it secure and persistent (service, Docker, reverse proxy), tell me how you'd like to run it and I can add a `Dockerfile` + systemd/unit or PM2 config.
