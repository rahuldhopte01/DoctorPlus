<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Under Maintenance - {{ $setting->business_name }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('images/upload/' . $setting->company_favicon) }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .container {
            text-align: center;
            padding: 40px 20px;
            max-width: 600px;
        }
        .logo img {
            max-height: 80px;
            margin-bottom: 30px;
            filter: brightness(0) invert(1);
        }
        .icon {
            font-size: 80px;
            margin-bottom: 20px;
            display: block;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            font-weight: 700;
        }
        p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.7;
            margin-bottom: 30px;
        }
        .badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.4);
            border-radius: 50px;
            padding: 10px 30px;
            font-size: 0.95rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        @if($setting->company_white_logo)
        <div class="logo">
            <img src="{{ url('images/upload/' . $setting->company_white_logo) }}" alt="{{ $setting->business_name }}">
        </div>
        @endif

        <span class="icon">🔧</span>
        <h1>Under Maintenance</h1>
        <p>
            {{ $setting->maintenance_message ?: 'We are currently performing scheduled maintenance. We\'ll be back online shortly. Thank you for your patience.' }}
        </p>
        <div class="badge">{{ $setting->business_name }}</div>
    </div>
</body>
</html>
