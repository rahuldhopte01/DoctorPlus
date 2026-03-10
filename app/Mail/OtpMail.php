<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer_name;
    public $otp_code;
    public $otp_expiry;
    public $year;
    public $privacy_url;
    public $contact_url;
    public $appName;

    public function __construct(array $data)
    {
        $this->customer_name = $data['customer_name'] ?? '';
        $this->otp_code = $data['otp_code'] ?? '';
        $this->otp_expiry = $data['otp_expiry'] ?? 10;
        $this->year = $data['year'] ?? date('Y');
        $this->privacy_url = $data['privacy_url'] ?? url('/privacy-policy');
        $this->contact_url = $data['contact_url'] ?? url('/');
        $this->appName = $data['app_name'] ?? config('mail.from.name', 'dr.fuxx');
    }

    public function build()
    {
        $fromAddress = config('mail.from.address', env('MAIL_FROM_ADDRESS'));
        $fromName = config('mail.from.name', env('MAIL_FROM_NAME'));

        return $this->from($fromAddress, $fromName)
            ->subject(__('Your verification code') . ' - ' . $this->appName)
            ->view('emails.otp')
            ->with([
                'customer_name' => $this->customer_name,
                'otp_code' => $this->otp_code,
                'otp_expiry' => $this->otp_expiry,
                'year' => $this->year,
                'privacy_url' => $this->privacy_url,
                'contact_url' => $this->contact_url,
                'appName' => $this->appName,
            ]);
    }
}
