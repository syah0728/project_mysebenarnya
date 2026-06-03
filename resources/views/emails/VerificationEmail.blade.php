<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f0f4f8;
            color: #1a202c;
            padding: 40px 20px;
        }
        .wrapper {
            max-width: 560px;
            margin: 0 auto;
        }
        .card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            padding: 40px 40px 32px;
            text-align: center;
        }
        .header-icon {
            width: 64px;
            height: 64px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        .header h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        .header p {
            color: rgba(255,255,255,0.75);
            font-size: 14px;
            margin-top: 6px;
        }
        .body {
            padding: 36px 40px;
        }
        .greeting {
            font-size: 16px;
            color: #374151;
            margin-bottom: 12px;
        }
        .message {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.7;
            margin-bottom: 28px;
        }
        .btn-wrap {
            text-align: center;
            margin-bottom: 28px;
        }
        .btn {
            display: inline-block;
            background: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            padding: 14px 36px;
            border-radius: 10px;
            letter-spacing: 0.1px;
        }
        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 24px 0;
        }
        .fallback {
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.6;
        }
        .fallback a {
            color: #3b82f6;
            word-break: break-all;
        }
        .expiry-note {
            display: inline-block;
            background: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
            font-size: 12px;
            padding: 8px 14px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .footer {
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 20px 40px;
            text-align: center;
        }
        .footer p {
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.6;
        }
        .footer strong {
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">

            <!-- Header -->
            <div class="header">
                <div class="header-icon">
                    <!-- Mail icon -->
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5a2.25 2.25 0 00-2.25 2.25m19.5 0l-9.75 7.5-9.75-7.5"/>
                    </svg>
                </div>
                <h1>Verify Your Email Address</h1>
                <p>My Sebenarnya System</p>
            </div>

            <!-- Body -->
            <div class="body">
                <p class="greeting">Hello,</p>
                <p class="message">
                    Thank you for registering with the <strong>My Sebenarnya System</strong>.
                    To complete your registration and activate your account,
                    please verify your email address by clicking the button below.
                </p>

                <span class="expiry-note">
                    ⏱ This link will expire in 60 minutes.
                </span>

                <div class="btn-wrap">
                    <a href="{{ $url }}" class="btn">Verify Email Address</a>
                </div>

                <hr class="divider">

                <p class="fallback">
                    If the button above doesn't work, copy and paste the link below into your browser:<br>
                    <a href="{{ $url }}">{{ $url }}</a>
                </p>

                <hr class="divider">

                <p class="fallback">
                    If you did not create an account, no further action is required.
                    You can safely ignore this email.
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>
                    <strong>MCMC Inquiry System</strong><br>
                    This is an automated message. Please do not reply to this email.
                </p>
            </div>

        </div>
    </div>
</body>
</html>