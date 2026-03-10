<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Questionnaire Not Approved – {{ $appName ?? 'dr.fuxx' }}</title>
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
                  <img src="{{ asset('images/13.png') }}" alt="{{ $appName ?? 'dr.fuxx' }}" style="max-width:180px;height:auto;display:block;margin:0 auto;" />
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Red accent bar for Rejected -->
        <tr>
          <td style="background:linear-gradient(90deg,#8a1a1a,#e74c3c);height:3px;"></td>
        </tr>

        <!-- Hero rejected banner -->
        <tr>
          <td style="background:linear-gradient(135deg,#1a0707,#2a0e0e);padding:32px 48px;text-align:center;">
            <div style="display:inline-block;background:rgba(231,76,60,0.12);border:2px solid #e74c3c;border-radius:50%;width:80px;height:80px;line-height:80px;text-align:center;margin-bottom:16px;">
              <span style="font-size:36px;">❌</span>
            </div>
            <h1 style="color:#e74c3c;font-family:'Georgia',serif;font-size:28px;font-weight:700;margin:0 0 8px;">Questionnaire Not Approved</h1>
            <p style="color:#c0908e;font-family:'Helvetica Neue',Arial,sans-serif;font-size:13px;margin:0;letter-spacing:1px;">Your submission has been reviewed. Please see the doctor's feedback below.</p>
          </td>
        </tr>

        <!-- Main Content -->
        <tr>
          <td style="padding:40px 48px 32px;">
            <p style="color:#cccccc;font-family:'Helvetica Neue',Arial,sans-serif;font-size:15px;line-height:1.7;margin:0 0 20px;">
              Dear <strong style="color:#ffffff;">{{ $customer_name }}</strong>,
            </p>
            <p style="color:#cccccc;font-family:'Helvetica Neue',Arial,sans-serif;font-size:15px;line-height:1.7;margin:0 0 20px;">
              Thank you for submitting your questionnaire. After a thorough review, Dr. <strong style="color:#ffffff;">{{ $doctor_name }}</strong> was unfortunately unable to approve your submission at this time. @if(!empty($rejection_reason))Please review the doctor's feedback carefully.@endif
            </p>
            <p style="color:#cccccc;font-family:'Helvetica Neue',Arial,sans-serif;font-size:15px;line-height:1.7;margin:0 0 32px;">
              We understand this may be disappointing. Please know that this decision is made with your well-being in mind. You are welcome to resubmit after addressing the points raised.
            </p>

            @if(!empty($rejection_reason))
            <!-- Doctor's Feedback Box -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#1f0d0d;border:1px solid #4d1e1e;border-radius:8px;margin-bottom:24px;">
              <tr>
                <td style="padding:24px 28px;">
                  <table cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
                    <tr>
                      <td style="vertical-align:middle;padding-right:12px;">
                        <div style="width:40px;height:40px;background:#2a1010;border:1px solid #e74c3c;border-radius:50%;line-height:40px;text-align:center;">
                          <span style="font-size:18px;">👨‍⚕️</span>
                        </div>
                      </td>
                      <td style="vertical-align:middle;">
                        <p style="color:#e74c3c;font-family:'Helvetica Neue',Arial,sans-serif;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin:0 0 2px;">Doctor's Feedback</p>
                        <p style="color:#888888;font-family:'Helvetica Neue',Arial,sans-serif;font-size:12px;margin:0;">Dr. {{ $doctor_name }} &nbsp;·&nbsp; {{ $review_date }}</p>
                      </td>
                    </tr>
                  </table>
                  <p style="color:#cccccc;font-family:'Helvetica Neue',Arial,sans-serif;font-size:14px;line-height:1.7;margin:0;font-style:italic;border-left:3px solid #e74c3c;padding-left:16px;">
                    "{{ $rejection_reason }}"
                  </p>
                </td>
              </tr>
            </table>
            @endif

            <!-- Rejection Details -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#141414;border:1px solid #2a2a2a;border-radius:8px;margin-bottom:24px;">
              <tr>
                <td style="padding:20px 28px;">
                  <p style="color:#888888;font-family:'Helvetica Neue',Arial,sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;margin:0 0 14px;">Review Summary</p>
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="color:#888888;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;border-bottom:1px solid #222;">Reference ID</td>
                      <td style="color:#E87B1E;font-size:13px;font-family:'Courier New',monospace;padding:6px 0;border-bottom:1px solid #222;text-align:right;font-weight:700;">{{ $submission_id }}</td>
                    </tr>
                    <tr>
                      <td style="color:#888888;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;border-bottom:1px solid #222;">Reviewed By</td>
                      <td style="color:#ffffff;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;border-bottom:1px solid #222;text-align:right;">Dr. {{ $doctor_name }}</td>
                    </tr>
                    <tr>
                      <td style="color:#888888;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;border-bottom:1px solid #222;">Review Date</td>
                      <td style="color:#ffffff;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;border-bottom:1px solid #222;text-align:right;">{{ $review_date }}</td>
                    </tr>
                    <tr>
                      <td style="color:#888888;font-size:13px;font-family:'Helvetica Neue',Arial,sans-serif;padding:6px 0;">Status</td>
                      <td style="padding:6px 0;text-align:right;">
                        <span style="background:#2a1010;color:#e74c3c;font-size:11px;font-weight:700;font-family:'Helvetica Neue',Arial,sans-serif;padding:3px 10px;border-radius:20px;letter-spacing:1px;text-transform:uppercase;">NOT APPROVED</span>
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
          <td style="background-color:#0d0d0d;padding:24px 48px;border-top:1px solid #1e1e1e;text-align:center;">
            <p style="color:#555555;font-family:'Helvetica Neue',Arial,sans-serif;font-size:12px;margin:0 0 8px;">We're here to help. Reach out at <a href="mailto:{{ $support_email }}" style="color:#E87B1E;text-decoration:none;">{{ $support_email }}</a></p>
            <p style="color:#444444;font-family:'Helvetica Neue',Arial,sans-serif;font-size:11px;margin:0;">© {{ $year }} {{ $appName ?? 'dr.fuxx' }}. All rights reserved. &nbsp;|&nbsp; <a href="{{ $privacy_url }}" style="color:#E87B1E;text-decoration:none;">Privacy Policy</a> &nbsp;|&nbsp; <a href="{{ $contact_url }}" style="color:#E87B1E;text-decoration:none;">Contact Us</a></p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
