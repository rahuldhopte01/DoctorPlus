<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Your OTP – {{ $appName ?? 'dr.fuxx' }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:'Georgia',serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:40px 0;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background-color:#0a0a0a;border-radius:12px;overflow:hidden;max-width:600px;">

        <!-- Header -->
        <tr>
          <td style="background-color:#0a0a0a;padding:36px 48px 24px;text-align:center;border-bottom:1px solid #1e1e1e;">
            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
              <tr>
                <td style="vertical-align:middle;">
                  <span style="font-family:'Georgia',serif;font-size:32px;font-weight:700;color:#ffffff;letter-spacing:-1px;">{{ $appName ?? 'dr.fuxx' }}</span>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Orange accent bar -->
        <tr>
          <td style="background:linear-gradient(90deg,#E87B1E,#f5a94e);height:3px;"></td>
        </tr>

        <!-- Main Content -->
        <tr>
          <td style="padding:48px 48px 32px;">

            <div style="text-align:center;margin-bottom:28px;">
              <div style="display:inline-block;background-color:#1a1a1a;border:2px solid #E87B1E;border-radius:50%;width:72px;height:72px;line-height:72px;text-align:center;">
                <span style="font-size:30px;">🔐</span>
              </div>
            </div>

            <h1 style="color:#ffffff;font-family:'Georgia',serif;font-size:26px;font-weight:700;text-align:center;margin:0 0 8px;">Verify Your Identity</h1>
            <p style="color:#E87B1E;font-family:'Helvetica Neue',Arial,sans-serif;font-size:13px;text-align:center;margin:0 0 32px;letter-spacing:2px;text-transform:uppercase;">One-Time Password</p>

            <p style="color:#cccccc;font-family:'Helvetica Neue',Arial,sans-serif;font-size:15px;line-height:1.7;margin:0 0 20px;">
              Dear <strong style="color:#ffffff;">{{ $customer_name }}</strong>,
            </p>
            <p style="color:#cccccc;font-family:'Helvetica Neue',Arial,sans-serif;font-size:15px;line-height:1.7;margin:0 0 32px;">
              Use the one-time password below to complete your verification. This code is valid for <strong style="color:#E87B1E;">{{ $otp_expiry }} minutes</strong> only. Do not share it with anyone.
            </p>

            <!-- OTP Box -->
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
              <tr>
                <td style="text-align:center;">
                  <div style="display:inline-block;background:linear-gradient(135deg,#141414,#1c1c1c);border:2px solid #E87B1E;border-radius:10px;padding:28px 48px;">
                    <p style="color:#888888;font-family:'Helvetica Neue',Arial,sans-serif;font-size:11px;letter-spacing:3px;text-transform:uppercase;margin:0 0 12px;">Your OTP</p>
                    <p style="color:#E87B1E;font-family:'Courier New',monospace;font-size:44px;font-weight:700;letter-spacing:10px;margin:0;line-height:1;">{{ $otp_code }}</p>
                    <p style="color:#555555;font-family:'Helvetica Neue',Arial,sans-serif;font-size:11px;margin:12px 0 0;">Expires in {{ $otp_expiry }} minutes</p>
                  </div>
                </td>
              </tr>
            </table>

            <!-- Warning Box -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#1a1000;border:1px solid #3d2800;border-radius:8px;margin-bottom:32px;">
              <tr>
                <td style="padding:16px 20px;">
                  <table cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="vertical-align:top;padding-right:12px;font-size:18px;">⚠️</td>
                      <td>
                        <p style="color:#f0a040;font-family:'Helvetica Neue',Arial,sans-serif;font-size:13px;font-weight:700;margin:0 0 4px;">Security Notice</p>
                        <p style="color:#aa7030;font-family:'Helvetica Neue',Arial,sans-serif;font-size:12px;line-height:1.6;margin:0;">{{ $appName ?? 'dr.fuxx' }} will never ask for your OTP over phone or email. Never share this code with anyone, including our support team.</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <p style="color:#555555;font-family:'Helvetica Neue',Arial,sans-serif;font-size:13px;line-height:1.6;text-align:center;margin:0;">
              Didn't request this? You can safely ignore this email or <a href="{{ $contact_url }}" style="color:#E87B1E;text-decoration:none;">contact our support</a>.
            </p>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background-color:#0d0d0d;padding:24px 48px;border-top:1px solid #1e1e1e;text-align:center;">
            <p style="color:#444444;font-family:'Helvetica Neue',Arial,sans-serif;font-size:11px;margin:0;">© {{ $year }} {{ $appName ?? 'dr.fuxx' }}. All rights reserved. &nbsp;|&nbsp; <a href="{{ $privacy_url }}" style="color:#E87B1E;text-decoration:none;">Privacy Policy</a> &nbsp;|&nbsp; <a href="{{ $contact_url }}" style="color:#E87B1E;text-decoration:none;">Contact Us</a></p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
