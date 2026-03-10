<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Welcome to {{ $appName ?? config('mail.from.name', 'dr.fuxx') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:'Georgia',serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:40px 0;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background-color:#0a0a0a;border-radius:12px;overflow:hidden;max-width:600px;">

        <!-- Header with Logo -->
        <tr>
          <td style="background-color:#0a0a0a;padding:36px 48px 24px;text-align:center;border-bottom:1px solid #1e1e1e;">
            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
              <tr>
                <td style="vertical-align:middle;">
                  <img src="{{ asset('images/13.png') }}" alt="{{ $appName ?? 'dr.fuxx' }}" style="max-width:180px;height:auto;display:block;margin:0 auto;" />
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
                <span style="font-size:34px;">✓</span>
              </div>
            </div>

            <h1 style="color:#ffffff;font-family:'Georgia',serif;font-size:26px;font-weight:700;text-align:center;margin:0 0 8px;">Registration Successful!</h1>
            <p style="color:#E87B1E;font-family:'Georgia',serif;font-size:14px;text-align:center;margin:0 0 32px;letter-spacing:2px;text-transform:uppercase;">Welcome to {{ $appName ?? 'dr.fuxx' }}</p>

            <p style="color:#cccccc;font-family:'Helvetica Neue',Arial,sans-serif;font-size:15px;line-height:1.7;margin:0 0 20px;">
              Dear <strong style="color:#ffffff;">{{ $customer_name }}</strong>,
            </p>
            <p style="color:#cccccc;font-family:'Helvetica Neue',Arial,sans-serif;font-size:15px;line-height:1.7;margin:0 0 20px;">
              Your account has been successfully created with <strong style="color:#E87B1E;">{{ $appName ?? 'dr.fuxx' }}</strong>. We're thrilled to have you on board and look forward to supporting you on your journey.
            </p>
            <p style="color:#cccccc;font-family:'Helvetica Neue',Arial,sans-serif;font-size:15px;line-height:1.7;margin:0 0 32px;">
              You can now log in and access our expert medical consultation services at any time.
            </p>

            <!-- Account Summary Box -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#141414;border:1px solid #2a2a2a;border-radius:8px;margin-bottom:32px;">
              <tr>
                <td style="padding:24px 28px;">
                  <p style="color:#888888;font-family:'Helvetica Neue',Arial,sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;margin:0 0 16px;">Account Details</p>
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="color:#888888;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;border-bottom:1px solid #222;">Name</td>
                      <td style="color:#ffffff;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;border-bottom:1px solid #222;text-align:right;">{{ $customer_name }}</td>
                    </tr>
                    <tr>
                      <td style="color:#888888;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;border-bottom:1px solid #222;">Email</td>
                      <td style="color:#ffffff;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;border-bottom:1px solid #222;text-align:right;">{{ $customer_email }}</td>
                    </tr>
                    <tr>
                      <td style="color:#888888;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;">Registered On</td>
                      <td style="color:#ffffff;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;text-align:right;">{{ $registration_date }}</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- CTA Button -->
            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
              <tr>
                <td style="background:linear-gradient(135deg,#E87B1E,#f5a94e);border-radius:6px;text-align:center;">
                  <a href="{{ $login_url }}" style="display:inline-block;padding:14px 40px;color:#000000;font-family:'Helvetica Neue',Arial,sans-serif;font-size:14px;font-weight:700;text-decoration:none;letter-spacing:1px;text-transform:uppercase;">Log In to Your Account</a>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background-color:#0d0d0d;padding:24px 48px;border-top:1px solid #1e1e1e;text-align:center;">
            <p style="color:#555555;font-family:'Helvetica Neue',Arial,sans-serif;font-size:12px;margin:0 0 8px;">If you didn't create this account, please contact us immediately.</p>
            <p style="color:#444444;font-family:'Helvetica Neue',Arial,sans-serif;font-size:11px;margin:0;">© {{ $year }} {{ $appName ?? 'dr.fuxx' }}. All rights reserved. &nbsp;|&nbsp; <a href="{{ $privacy_url }}" style="color:#E87B1E;text-decoration:none;">Privacy Policy</a> &nbsp;|&nbsp; <a href="{{ $contact_url }}" style="color:#E87B1E;text-decoration:none;">Contact Us</a></p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
