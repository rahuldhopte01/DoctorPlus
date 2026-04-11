<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Ihr Bestätigungscode – dr.fuxx</title>
</head>
<body style="margin:0;padding:0;background-color:#f8f7ff;font-family:'Helvetica Neue',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8f7ff;padding:40px 0;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;max-width:600px;box-shadow:0 10px 30px rgba(123,66,246,0.1);">

        <!-- Header -->
        <tr>
          <td style="background-color:#7b42f6;padding:40px 48px;text-align:center;">
            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
              <tr>
                <td style="vertical-align:middle;">
                  <img src="{{ asset('images/logo-white.png') }}" alt="dr.fuxx" style="max-width:160px;height:auto;display:block;margin:0 auto;" />
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Main Content -->
        <tr>
          <td style="padding:48px 48px 32px;">

            <div style="text-align:center;margin-bottom:28px;">
              <div style="display:inline-block;background-color:#f3eeff;border:2px solid #7b42f6;border-radius:50%;width:72px;height:72px;line-height:72px;text-align:center;">
                <span style="font-size:30px;">🔐</span>
              </div>
            </div>

            <h1 style="color:#1a1a1a;font-size:26px;font-weight:800;text-align:center;margin:0 0 8px;">Identität verifizieren</h1>
            <p style="color:#7b42f6;font-size:13px;text-align:center;margin:0 0 32px;letter-spacing:2px;text-transform:uppercase;font-weight:700;">Einmalpasswort (OTP)</p>

            <p style="color:#4a4a4a;font-size:15px;line-height:1.7;margin:0 0 20px;">
              Sehr geehrte(r) <strong style="color:#1a1a1a;">{{ $customer_name }}</strong>,
            </p>
            <p style="color:#4a4a4a;font-size:15px;line-height:1.7;margin:0 0 32px;">
              Verwenden Sie das untenstehende Einmalpasswort, um Ihre Verifizierung abzuschließen. Dieser Code ist nur für <strong style="color:#7b42f6;">{{ $otp_expiry }} Minuten</strong> gültig. Geben Sie diesen Code niemals an Dritte weiter.
            </p>

            <!-- OTP Box -->
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
              <tr>
                <td style="text-align:center;">
                  <div style="display:inline-block;background-color:#f8f7ff;border:2px dashed #7b42f6;border-radius:12px;padding:28px 48px;">
                    <p style="color:#666666;font-size:11px;letter-spacing:3px;text-transform:uppercase;margin:0 0 12px;font-weight:700;">Ihr Code</p>
                    <p style="color:#7b42f6;font-family:'Courier New',monospace;font-size:44px;font-weight:800;letter-spacing:10px;margin:0;line-height:1;">{{ $otp_code }}</p>
                    <p style="color:#999999;font-size:11px;margin:12px 0 0;">Gültig für {{ $otp_expiry }} Minuten</p>
                  </div>
                </td>
              </tr>
            </table>

            <!-- Warning Box -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fffaf0;border:1px solid #feebc8;border-radius:12px;margin-bottom:32px;">
              <tr>
                <td style="padding:16px 20px;">
                  <table cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="vertical-align:top;padding-right:12px;font-size:18px;">⚠️</td>
                      <td>
                        <p style="color:#c05621;font-size:13px;font-weight:700;margin:0 0 4px;">Sicherheitshinweis</p>
                        <p style="color:#dd6b20;font-size:12px;line-height:1.6;margin:0;">dr.fuxx wird Sie niemals per Telefon oder E-Mail nach Ihrem OTP fragen. Geben Sie diesen Code niemals an Dritte weiter, auch nicht an unser Support-Team.</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <p style="color:#6b7280;font-size:13px;line-height:1.6;text-align:center;margin:0;">
              Sie haben dies nicht angefordert? Sie können diese E-Mail ignorieren oder <a href="{{ $contact_url }}" style="color:#7b42f6;text-decoration:none;font-weight:600;">unseren Support kontaktieren</a>.
            </p>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background-color:#f9fafb;padding:32px 48px;border-top:1px solid #e5e7eb;text-align:center;">
            <p style="color:#9ca3af;font-size:11px;margin:0;">&copy; {{ $year }} dr.fuxx. Alle Rechte vorbehalten. &nbsp;|&nbsp; <a href="{{ $privacy_url }}" style="color:#7b42f6;text-decoration:none;">Datenschutz</a> &nbsp;|&nbsp; <a href="{{ $contact_url }}" style="color:#7b42f6;text-decoration:none;">Kontakt</a></p>
            <p style="color:#9ca3af;font-size:10px;margin:12px 0 0;line-height:1.6;">dr.fuxx GmbH &middot; Berlin, Deutschland</p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
