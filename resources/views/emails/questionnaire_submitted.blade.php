@php $setting = \App\Models\Setting::first(); @endphp
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Fragebogen eingereicht – {{ $setting->business_name ?? 'dr.fuxx' }}</title>
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
                  <img src="{{ ($setting && $setting->company_white_logo) ? $setting->companyWhite : asset('images/logo-white.png') }}" alt="{{ $setting->business_name ?? 'dr.fuxx' }}" style="max-width:160px;height:auto;display:block;margin:0 auto;" />
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
                <span style="font-size:30px;">📋</span>
              </div>
            </div>

            <h1 style="color:#1a1a1a;font-size:26px;font-weight:800;text-align:center;margin:0 0 8px;">Fragebogen eingereicht</h1>
            <p style="color:#7b42f6;font-size:13px;text-align:center;margin:0 0 32px;letter-spacing:2px;text-transform:uppercase;font-weight:700;">Wartet auf ärztliche Prüfung</p>

            <p style="color:#4a4a4a;font-size:15px;line-height:1.7;margin:0 0 20px;">
              Hallo <strong style="color:#1a1a1a;">{{ $customer_name }}</strong>,
            </p>
            <p style="color:#4a4a4a;font-size:15px;line-height:1.7;margin:0 0 32px;">
              Vielen Dank für das Einreichen Ihres medizinischen Fragebogens. Unser Arzt wird Ihre Angaben sorgfältig prüfen und Sie erhalten innerhalb von <strong style="color:#7b42f6;">{{ $review_timeframe }}</strong> eine Rückmeldung. Ihre Gesundheit ist unsere Priorität.
            </p>

            <!-- Submission Summary -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;margin-bottom:24px;">
              <tr>
                <td style="padding:24px 28px;">
                  <p style="color:#6b7280;font-size:11px;letter-spacing:2px;text-transform:uppercase;margin:0 0 16px;font-weight:700;">Angaben zur Einsendung</p>
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;">Referenz-ID</td>
                      <td style="color:#7b42f6;font-size:13px;font-family:'Courier New',monospace;padding:8px 0;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:700;">{{ $submission_id }}</td>
                    </tr>
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;">Eingereicht am</td>
                      <td style="color:#1a1a1a;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:600;">{{ $submission_date }}</td>
                    </tr>
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;">Kategorie</td>
                      <td style="color:#1a1a1a;font-size:13px;padding:8px 0;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:600;">{{ $questionnaire_category }}</td>
                    </tr>
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:8px 0;">Prüfungsdauer</td>
                      <td style="color:#1a1a1a;font-size:13px;padding:8px 0;text-align:right;font-weight:600;">{{ $review_timeframe }}</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- Payment Summary -->
            @if(!empty($base_price) || !empty($total_amount_paid))
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3eeff;border:1px solid #dcd0ff;border-radius:12px;margin-bottom:32px;">
              <tr>
                <td style="padding:24px 28px;">
                  <p style="color:#6b7280;font-size:11px;letter-spacing:2px;text-transform:uppercase;margin:0 0 16px;font-weight:700;">Zahlungsbeleg</p>
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:6px 0;border-bottom:1px solid #e2d9ff;">Beratungsgebühr</td>
                      <td style="color:#1a1a1a;font-size:13px;padding:6px 0;border-bottom:1px solid #e2d9ff;text-align:right;font-weight:600;">{{ $base_price ?? '—' }}</td>
                    </tr>
                    <tr>
                      <td style="color:#6b7280;font-size:13px;padding:6px 0;border-bottom:1px solid #e2d9ff;">Bearbeitungsgebühr</td>
                      <td style="color:#1a1a1a;font-size:13px;padding:6px 0;border-bottom:1px solid #e2d9ff;text-align:right;font-weight:600;">{{ $processing_fee ?? '—' }}</td>
                    </tr>
                    <tr>
                      <td style="color:#7b42f6;font-size:15px;font-weight:800;padding:12px 0 0;">Gesamtbetrag</td>
                      <td style="color:#7b42f6;font-size:18px;font-weight:800;padding:12px 0 0;text-align:right;">{{ $total_amount_paid ?? '—' }}</td>
                    </tr>
                  </table>
                  <p style="color:#9ca3af;font-size:11px;margin:12px 0 0;">Transaktions-ID: <span style="color:#6b7280;font-family:'Courier New',monospace;">{{ $transaction_id ?? '—' }}</span> &nbsp;|&nbsp; {{ $payment_date ?? '' }}</p>
                </td>
              </tr>
            </table>
            @endif

            <!-- Status Timeline -->
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
              <tr>
                <td>
                  <p style="color:#6b7280;font-size:11px;letter-spacing:2px;text-transform:uppercase;margin:0 0 16px;font-weight:700;">Prüfungsstatus</p>
                  <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:4px;">
                    <tr>
                      <td width="32" style="vertical-align:top;text-align:center;">
                        <div style="width:28px;height:28px;background:#7b42f6;border-radius:50%;line-height:28px;text-align:center;color:#fff;font-size:12px;font-weight:700;">1</div>
                        <div style="width:2px;height:24px;background:#e5e7eb;margin:0 auto;"></div>
                      </td>
                      <td style="padding-left:14px;vertical-align:top;padding-top:4px;">
                        <p style="color:#1a1a1a;font-size:13px;font-weight:700;margin:0 0 2px;">Eingereicht ✓</p>
                        <p style="color:#6b7280;font-size:12px;margin:0 0 16px;">Ihr Fragebogen wurde erfolgreich empfangen</p>
                      </td>
                    </tr>
                  </table>
                  <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:4px;">
                    <tr>
                      <td width="32" style="vertical-align:top;text-align:center;">
                        <div style="width:28px;height:28px;background:#ffffff;border:2px solid #7b42f6;border-radius:50%;line-height:24px;text-align:center;color:#7b42f6;font-size:12px;font-weight:700;">2</div>
                        <div style="width:2px;height:24px;background:#f3f4f6;margin:0 auto;"></div>
                      </td>
                      <td style="padding-left:14px;vertical-align:top;padding-top:4px;">
                        <p style="color:#7b42f6;font-size:13px;font-weight:700;margin:0 0 2px;">Ärztliche Prüfung (In Bearbeitung)</p>
                        <p style="color:#6b7280;font-size:12px;margin:0 0 16px;">Ihre Einsendung wird derzeit von unserem Arzt geprüft</p>
                      </td>
                    </tr>
                  </table>
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="32" style="vertical-align:top;text-align:center;">
                        <div style="width:28px;height:28px;background:#f9fafb;border:2px solid #e5e7eb;border-radius:50%;line-height:24px;text-align:center;color:#9ca3af;font-size:12px;font-weight:700;">3</div>
                      </td>
                      <td style="padding-left:14px;vertical-align:top;padding-top:4px;">
                        <p style="color:#9ca3af;font-size:13px;font-weight:700;margin:0 0 2px;">Entscheidung & Rückmeldung</p>
                        <p style="color:#9ca3af;font-size:12px;margin:0;">Wir benachrichtigen Sie, sobald die Prüfung abgeschlossen ist</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background-color:#f9fafb;padding:32px 48px;border-top:1px solid #e5e7eb;text-align:center;">
            <p style="color:#6b7280;font-size:12px;margin:0 0 8px;">Fragen? Kontaktieren Sie uns unter <a href="mailto:{{ $support_email }}" style="color:#7b42f6;text-decoration:none;font-weight:600;">{{ $support_email }}</a></p>
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
