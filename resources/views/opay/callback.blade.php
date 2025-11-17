<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Payment callback</title></head>
<body style="font-family:Arial;background:#071627;color:#dff;padding:28px">
    <div style="max-width:800px;margin:0 auto;background:rgba(255,255,255,0.02);padding:18px;border-radius:12px">
        <h2>Payment callback received</h2>
        <pre style="white-space:pre-wrap">{{ json_encode($payload, JSON_PRETTY_PRINT) }}</pre>
        <p>Implement verification and fulfillment logic in <code>TicketPaymentController::callback</code>.</p>
    </div>
</body>
</html>
