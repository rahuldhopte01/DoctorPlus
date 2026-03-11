<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Your New Password – {{ $appName ?? 'dr.fuxx' }}</title>
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
                  <img src="{{ asset('images/logo-white.png') }}" alt="{{ $appName ?? 'dr.fuxx' }}" style="max-width:180px;height:auto;display:block;margin:0 auto;" />
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

            <!-- Key Icon -->
            <div style="text-align:center;margin-bottom:28px;">
              <div style="display:inline-block;background-color:#1a1a1a;border:2px solid #E87B1E;border-radius:50%;width:72px;height:72px;line-height:72px;text-align:center;">
                <span style="font-size:30px;">🔑</span>
              </div>
            </div>

            <h1 style="color:#ffffff;font-family:'Georgia',serif;font-size:26px;font-weight:700;text-align:center;margin:0 0 8px;">Your New Password</h1>
            <p style="color:#E87B1E;font-family:'Helvetica Neue',Arial,sans-serif;font-size:13px;text-align:center;margin:0 0 32px;letter-spacing:2px;text-transform:uppercase;">Forgot Password Request</p>

            <p style="color:#cccccc;font-family:'Helvetica Neue',Arial,sans-serif;font-size:15px;line-height:1.7;margin:0 0 20px;">
              Hello <strong style="color:#ffffff;">{{ $customerName }}</strong>,
            </p>
            <p style="color:#cccccc;font-family:'Helvetica Neue',Arial,sans-serif;font-size:15px;line-height:1.7;margin:0 0 32px;">
              You requested a password reset. Here is your new temporary password. You can log in using it below.
            </p>

            <!-- New Password Box -->
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
              <tr>
                <td style="text-align:center;">
                  <div style="display:inline-block;background:linear-gradient(135deg,#141414,#1c1c1c);border:2px solid #E87B1E;border-radius:10px;padding:28px 48px;min-width:260px;">
                    <p style="color:#888888;font-family:'Helvetica Neue',Arial,sans-serif;font-size:11px;letter-spacing:3px;text-transform:uppercase;margin:0 0 12px;">Your New Password</p>
                    <p style="color:#E87B1E;font-family:'Courier New',monospace;font-size:26px;font-weight:700;letter-spacing:4px;margin:0;line-height:1;word-break:break-all;">{{ $newPassword }}</p>
                    <p style="color:#555555;font-family:'Helvetica Neue',Arial,sans-serif;font-size:11px;margin:14px 0 0;">Sent on {{ $changeDate }} at {{ $changeTime }}</p>
                  </div>
                </td>
              </tr>
            </table>

            <!-- Security Tip -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0f1a0f;border:1px solid #1e3a1e;border-radius:8px;margin-bottom:24px;">
              <tr>
                <td style="padding:18px 22px;">
                  <table cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="vertical-align:top;padding-right:12px;font-size:18px;">💡</td>
                      <td>
                        <p style="color:#5aaa5a;font-family:'Helvetica Neue',Arial,sans-serif;font-size:13px;font-weight:700;margin:0 0 4px;">Security Tip</p>
                        <p style="color:#4a7a4a;font-family:'Helvetica Neue',Arial,sans-serif;font-size:12px;line-height:1.6;margin:0;">We recommend changing this password to something memorable as soon as you log in. Use a mix of uppercase, lowercase, numbers and symbols for a stronger password.</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- Warning Box -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#1a1000;border:1px solid #3d2800;border-radius:8px;margin-bottom:32px;">
              <tr>
                <td style="padding:18px 22px;">
                  <table cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="vertical-align:top;padding-right:12px;font-size:18px;">⚠️</td>
                      <td>
                        <p style="color:#f0a040;font-family:'Helvetica Neue',Arial,sans-serif;font-size:13px;font-weight:700;margin:0 0 4px;">Didn't request a password reset?</p>
                        <p style="color:#aa7030;font-family:'Helvetica Neue',Arial,sans-serif;font-size:12px;line-height:1.6;margin:0;">If you did not request this, your account may be at risk. Please contact our support team immediately at <a href="mailto:{{ $supportEmail }}" style="color:#E87B1E;text-decoration:none;">{{ $supportEmail }}</a> or reset your password right away.</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- Account Details -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#141414;border:1px solid #2a2a2a;border-radius:8px;margin-bottom:32px;">
              <tr>
                <td style="padding:20px 28px;">
                  <p style="color:#888888;font-family:'Helvetica Neue',Arial,sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;margin:0 0 14px;">Account Information</p>
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="color:#888888;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;border-bottom:1px solid #222;">Account Email</td>
                      <td style="color:#ffffff;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;border-bottom:1px solid #222;text-align:right;">{{ $customerEmail }}</td>
                    </tr>
                    <tr>
                      <td style="color:#888888;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;border-bottom:1px solid #222;">Sent On</td>
                      <td style="color:#ffffff;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;border-bottom:1px solid #222;text-align:right;">{{ $changeDate }}</td>
                    </tr>
                    <tr>
                      <td style="color:#888888;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;">IP Address</td>
                      <td style="color:#ffffff;font-size:13px;font-family:'Courier New',monospace;padding:6px 0;text-align:right;">{{ $ipAddress }}</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- CTA Button -->
            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
              <tr>
                <td style="background:linear-gradient(135deg,#E87B1E,#f5a94e);border-radius:6px;text-align:center;">
                  <a href="{{ $loginUrl }}" style="display:inline-block;padding:14px 40px;color:#000000;font-family:'Helvetica Neue',Arial,sans-serif;font-size:14px;font-weight:700;text-decoration:none;letter-spacing:1px;text-transform:uppercase;">Log In Now</a>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background-color:#0d0d0d;padding:24px 48px;border-top:1px solid #1e1e1e;text-align:center;">
            <p style="color:#555555;font-family:'Helvetica Neue',Arial,sans-serif;font-size:12px;margin:0 0 8px;">Need help? Contact us at <a href="mailto:{{ $supportEmail }}" style="color:#E87B1E;text-decoration:none;">{{ $supportEmail }}</a></p>
            <p style="color:#444444;font-family:'Helvetica Neue',Arial,sans-serif;font-size:11px;margin:0;">© {{ $year }} {{ $appName ?? 'dr.fuxx' }}. All rights reserved. &nbsp;|&nbsp; <a href="{{ $privacyUrl }}" style="color:#E87B1E;text-decoration:none;">Privacy Policy</a> &nbsp;|&nbsp; <a href="{{ $contactUrl }}" style="color:#E87B1E;text-decoration:none;">Contact Us</a></p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
