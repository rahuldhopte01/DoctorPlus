@php $setting = \App\Models\Setting::first(); @endphp
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Ihr neues Passwort – {{ $setting->business_name ?? 'dr.fuxx' }}</title>
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
                  <img src="{{ ($setting && $setting->company_white_logo) ? $setting->companyWhite : asset('images/logo-white.png') }}" alt="{{ $setting->business_name ?? 'dr.fuxx' }}" style="max-width:160px;height:auto;display:block;margin:0 auto;" />
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Main Content -->
        <tr>
          <td style="padding:48px 48px 32px;">

            <!-- Key Icon -->
            <div style="text-align:center;margin-bottom:28px;">
              <div style="display:inline-block;background-color:#f3eeff;border:2px solid #7b42f6;border-radius:50%;width:72px;height:72px;line-height:72px;text-align:center;">
                <span style="font-size:30px;">🔑</span>
              </div>
            </div>

            <h1 style="color:#1a1a1a;font-size:26px;font-weight:800;text-align:center;margin:0 0 8px;">Ihr neues Passwort</h1>
            <p style="color:#7b42f6;font-size:13px;text-align:center;margin:0 0 32px;letter-spacing:2px;text-transform:uppercase;font-weight:700;">Passwort vergessen Anfrage</p>

            <p style="color:#4a4a4a;font-size:15px;line-height:1.7;margin:0 0 20px;">
              Hallo <strong style="color:#1a1a1a;">{{ $customerName }}</strong>,
            </p>
            <p style="color:#4a4a4a;font-size:15px;line-height:1.7;margin:0 0 32px;">
              Sie haben ein neues Passwort angefordert. Hier ist Ihr neues temporäres Passwort. Sie können sich damit unten direkt anmelden.
            </p>

            <!-- New Password Box -->
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
              <tr>
                <td style="text-align:center;">
                  <div style="display:inline-block;background-color:#f8f7ff;border:2px dashed #7b42f6;border-radius:12px;padding:28px 48px;min-width:260px;">
                    <p style="color:#666666;font-size:11px;letter-spacing:3px;text-transform:uppercase;margin:0 0 12px;font-weight:700;">Ihr neues Passwort</p>
                    <p style="color:#7b42f6;font-family:'Courier New',monospace;font-size:26px;font-weight:800;letter-spacing:4px;margin:0;line-height:1;word-break:break-all;">{{ $newPassword }}</p>
                    <p style="color:#999999;font-size:11px;margin:14px 0 0;">Gesendet am {{ $changeDate }} um {{ $changeTime }}</p>
                  </div>
                </td>
              </tr>
            </table>

            <!-- Security Tip -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fff4;border:1px solid #c6f6d5;border-radius:12px;margin-bottom:24px;">
              <tr>
                <td style="padding:18px 22px;">
                  <table cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="vertical-align:top;padding-right:12px;font-size:18px;">💡</td>
                      <td>
                        <p style="color:#2f855a;font-size:13px;font-weight:700;margin:0 0 4px;">Sicherheitstipp</p>
                        <p style="color:#38a169;font-size:12px;line-height:1.6;margin:0;">Wir empfehlen, dieses Passwort sofort nach der Anmeldung in ein persönliches Passwort zu ändern, das Sie sich gut merken können.</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- Warning Box -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fffaf0;border:1px solid #feebc8;border-radius:12px;margin-bottom:32px;">
              <tr>
                <td style="padding:18px 22px;">
                  <table cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="vertical-align:top;padding-right:12px;font-size:18px;">⚠️</td>
                      <td>
                        <p style="color:#c05621;font-size:13px;font-weight:700;margin:0 0 4px;">Nicht von Ihnen angefordert?</p>
                        <p style="color:#dd6b20;font-size:12px;line-height:1.6;margin:0;">Wenn Sie dies nicht angefordert haben, ist Ihr Konto möglicherweise gefährdet. Bitte kontaktieren Sie sofort unser Support-Team unter <a href="mailto:{{ $supportEmail }}" style="color:#7b42f6;text-decoration:none;font-weight:700;">{{ $supportEmail }}</a>.</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- Account Details -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;margin-bottom:32px;">
              <tr>
                <td style="padding:20px 28px;">
                  <p style="color:#6b7280;font-size:11px;letter-spacing:2px;text-transform:uppercase;margin:0 0 14px;font-weight:700;">Konto-Informationen</p>
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;">Konto-E-Mail</td>
                      <td style="color:#1a1a1a;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:600;">{{ $customerEmail }}</td>
                    </tr>
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;">Gesendet am</td>
                      <td style="color:#1a1a1a;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:600;">{{ $changeDate }}</td>
                    </tr>
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:8px 0;">IP-Adresse</td>
                      <td style="color:#1a1a1a;font-size:13px;padding:8px 0;text-align:right;font-family:'Courier New',monospace;">{{ $ipAddress }}</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- CTA Button -->
            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
              <tr>
                <td style="background-color:#7b42f6;border-radius:50px;text-align:center;">
                  <a href="{{ $loginUrl }}" style="display:inline-block;padding:16px 48px;color:#ffffff;font-size:14px;font-weight:800;text-decoration:none;letter-spacing:1px;text-transform:uppercase;">Jetzt einloggen</a>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background-color:#f9fafb;padding:32px 48px;border-top:1px solid #e5e7eb;text-align:center;">
            <p style="color:#6b7280;font-size:12px;margin:0 0 8px;">Benötigen Sie Hilfe? Kontaktieren Sie uns unter <a href="mailto:{{ $supportEmail }}" style="color:#7b42f6;text-decoration:none;font-weight:600;">{{ $supportEmail }}</a></p>
            <p style="color:#9ca3af;font-size:11px;margin:0;">&copy; {{ $year }} dr.fuxx. Alle Rechte vorbehalten. &nbsp;|&nbsp; <a href="{{ $privacyUrl }}" style="color:#7b42f6;text-decoration:none;">Datenschutz</a> &nbsp;|&nbsp; <a href="{{ $contactUrl }}" style="color:#7b42f6;text-decoration:none;">Kontakt</a></p>
            <p style="color:#9ca3af;font-size:10px;margin:12px 0 0;line-height:1.6;">dr.fuxx GmbH &middot; Berlin, Deutschland</p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
