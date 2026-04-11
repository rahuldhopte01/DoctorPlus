<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Willkommen bei dr.fuxx</title>
</head>
<body style="margin:0;padding:0;background-color:#f8f7ff;font-family:'Helvetica Neue',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8f7ff;padding:40px 0;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;max-width:600px;box-shadow:0 10px 30px rgba(123,66,246,0.1);">

        <!-- Header with Logo -->
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
                <span style="font-size:34px;color:#7b42f6;">✓</span>
              </div>
            </div>

            <h1 style="color:#1a1a1a;font-size:26px;font-weight:800;text-align:center;margin:0 0 8px;">Registrierung erfolgreich!</h1>
            <p style="color:#7b42f6;font-size:14px;text-align:center;margin:0 0 32px;letter-spacing:2px;text-transform:uppercase;font-weight:700;">Willkommen bei dr.fuxx</p>

            <p style="color:#4a4a4a;font-size:15px;line-height:1.7;margin:0 0 20px;">
              Hallo <strong style="color:#1a1a1a;">{{ $customer_name }}</strong>,
            </p>
            <p style="color:#4a4a4a;font-size:15px;line-height:1.7;margin:0 0 20px;">
              Ihr Konto wurde erfolgreich bei <strong style="color:#7b42f6;">dr.fuxx</strong> erstellt. Wir freuen uns, Sie an Bord zu haben und Sie auf Ihrem Weg unterstützen zu dürfen.
            </p>
            <p style="color:#4a4a4a;font-size:15px;line-height:1.7;margin:0 0 32px;">
              Sie können sich nun einloggen und jederzeit auf unsere medizinischen Beratungsleistungen zugreifen.
            </p>

            <!-- Account Summary Box -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;margin-bottom:32px;">
              <tr>
                <td style="padding:24px 28px;">
                  <p style="color:#6b7280;font-size:11px;letter-spacing:2px;text-transform:uppercase;margin:0 0 16px;font-weight:700;">Kontoinformationen</p>
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;">Name</td>
                      <td style="color:#1a1a1a;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:600;">{{ $customer_name }}</td>
                    </tr>
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;">E-Mail</td>
                      <td style="color:#1a1a1a;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:600;">{{ $customer_email }}</td>
                    </tr>
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:8px 0;">Registriert am</td>
                      <td style="color:#1a1a1a;font-size:13px;padding:8px 0;text-align:right;font-weight:600;">{{ $registration_date }}</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- CTA Button -->
            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
              <tr>
                <td style="background-color:#7b42f6;border-radius:50px;text-align:center;">
                  <a href="{{ $login_url }}" style="display:inline-block;padding:16px 48px;color:#ffffff;font-size:14px;font-weight:800;text-decoration:none;letter-spacing:1px;text-transform:uppercase;">Jetzt anmelden</a>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background-color:#f9fafb;padding:32px 48px;border-top:1px solid #e5e7eb;text-align:center;">
            <p style="color:#6b7280;font-size:12px;margin:0 0 8px;">Sollten Sie dieses Konto nicht erstellt haben, kontaktieren Sie uns bitte umgehend.</p>
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
