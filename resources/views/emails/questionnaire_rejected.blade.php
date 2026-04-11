<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Fragebogen nicht genehmigt – dr.fuxx</title>
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

        <!-- Alert Banner -->
        <tr>
          <td style="background-color:#fff5f5;padding:32px 48px;text-align:center;border-bottom:1px solid #feb2b2;">
            <div style="display:inline-block;background-color:#ffffff;border:2px solid #e53e3e;border-radius:50%;width:72px;height:72px;line-height:72px;text-align:center;margin-bottom:16px;">
              <span style="font-size:32px;">❌</span>
            </div>
            <h1 style="color:#9b2c2c;font-size:26px;font-weight:800;margin:0 0 8px;">Nicht genehmigt</h1>
            <p style="color:#e53e3e;font-size:13px;margin:0;letter-spacing:1px;text-transform:uppercase;font-weight:700;">Ihre Anfrage konnte nicht bestätigt werden</p>
          </td>
        </tr>

        <!-- Main Content -->
        <tr>
          <td style="padding:40px 48px 32px;">
            <p style="color:#4a4a4a;font-size:15px;line-height:1.7;margin:0 0 20px;">
              Hallo <strong style="color:#1a1a1a;">{{ $customer_name }}</strong>,
            </p>
            <p style="color:#4a4a4a;font-size:15px;line-height:1.7;margin:0 0 20px;">
              Vielen Dank für das Einreichen Ihres Fragebogens. Nach einer eingehenden Prüfung konnte Dr. <strong style="color:#1a1a1a;">{{ $doctor_name }}</strong> Ihre Einsendung zum jetzigen Zeitpunkt leider nicht genehmigen. @if(!empty($rejection_reason))Bitte lesen Sie sich das Feedback des Arztes sorgfältig durch.@endif
            </p>
            <p style="color:#4a4a4a;font-size:15px;line-height:1.7;margin:0 0 32px;">
              Wir verstehen, dass dies enttäuschend sein kann. Bitte wissen Sie, dass diese Entscheidung im Hinblick auf Ihr Wohlbefinden getroffen wurde. Sie können gerne eine neue Anfrage stellen, nachdem die genannten Punkte berücksichtigt wurden.
            </p>

            @if(!empty($rejection_reason))
            <!-- Doctor's Feedback Box -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;margin-bottom:24px;">
              <tr>
                <td style="padding:24px 28px;">
                  <table cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
                    <tr>
                      <td style="vertical-align:middle;padding-right:12px;">
                        <div style="width:40px;height:40px;background:#fff5f5;border:1px solid #e53e3e;border-radius:50%;line-height:40px;text-align:center;">
                          <span style="font-size:18px;">👨‍⚕️</span>
                        </div>
                      </td>
                      <td style="vertical-align:middle;">
                        <p style="color:#e53e3e;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin:0 0 2px;">Feedback des Arztes</p>
                        <p style="color:#6b7280;font-size:12px;margin:0;">Dr. {{ $doctor_name }} &nbsp;·&nbsp; {{ $review_date }}</p>
                      </td>
                    </tr>
                  </table>
                  <p style="color:#4a4a4a;font-size:14px;line-height:1.7;margin:0;font-style:italic;border-left:3px solid #e53e3e;padding-left:16px;">
                    "{{ $rejection_reason }}"
                  </p>
                </td>
              </tr>
            </table>
            @endif

            <!-- Review Summary -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;margin-bottom:32px;">
              <tr>
                <td style="padding:20px 28px;">
                  <p style="color:#6b7280;font-size:11px;letter-spacing:2px;text-transform:uppercase;margin:0 0 14px;font-weight:700;">Zusammenfassung der Prüfung</p>
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;">Referenz-ID</td>
                      <td style="color:#7b42f6;font-size:13px;font-family:'Courier New',monospace;padding:8px 0;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:700;">{{ $submission_id }}</td>
                    </tr>
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;">Geprüft von</td>
                      <td style="color:#1a1a1a;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:600;">Dr. {{ $doctor_name }}</td>
                    </tr>
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;">Prüfungsdatum</td>
                      <td style="color:#1a1a1a;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:600;">{{ $review_date }}</td>
                    </tr>
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:8px 0;">Status</td>
                      <td style="padding:8px 0;text-align:right;">
                        <span style="background-color:#fff5f5;color:#c53030;font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;letter-spacing:1px;">ABGELEHNT</span>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- CTA Button -->
            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
              <tr>
                <td style="background-color:#7b42f6;border-radius:50px;text-align:center;">
                  <a href="{{ url('/patient/dashboard') }}" style="display:inline-block;padding:16px 48px;color:#ffffff;font-size:14px;font-weight:800;text-decoration:none;letter-spacing:1px;text-transform:uppercase;">Zum Dashboard</a>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background-color:#f9fafb;padding:32px 48px;border-top:1px solid #e5e7eb;text-align:center;">
            <p style="color:#6b7280;font-size:12px;margin:0 0 8px;">Wir sind für Sie da. Kontaktieren Sie uns unter <a href="mailto:{{ $support_email }}" style="color:#7b42f6;text-decoration:none;font-weight:600;">{{ $support_email }}</a></p>
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
