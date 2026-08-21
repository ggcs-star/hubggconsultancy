<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="8">
    <title>Just a Moment · GG Hub</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; background: #f5f3ff; color: #1e293b; padding: 24px; }
        .card { max-width: 420px; width: 100%; background: #fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(109, 40, 217, .08); border: 1px solid #ede9fe; padding: 40px 32px; text-align: center; }
        .logo { width: 64px; height: 64px; border-radius: 16px; margin: 0 auto 20px; display: block; }
        h1 { font-size: 20px; font-weight: 800; margin: 0 0 8px; }
        p { font-size: 14px; color: #64748b; line-height: 1.6; margin: 0 0 8px; }
        .hint { font-size: 12px; color: #a78bfa; }
        .spinner { width: 28px; height: 28px; border: 3px solid #ede9fe; border-top-color: #6d28d9; border-radius: 50%; margin: 0 auto 20px; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="card">
        <div class="spinner"></div>
        <h1>{{ $heading ?? "We'll Be Right Back" }}</h1>
        <p>{{ $message ?? "We're currently updating GG Hub. This only takes a moment — this page will refresh automatically." }}</p>
        <p class="hint">No action needed — please wait.</p>
    </div>
</body>
</html>
