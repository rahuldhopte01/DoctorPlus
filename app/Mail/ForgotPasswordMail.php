<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $fromAddress = config('mail.from.address', env('MAIL_FROM_ADDRESS'));
        $fromName = config('mail.from.name', env('MAIL_FROM_NAME'));
        $setting = \App\Models\Setting::first();
        $appName = $setting->business_name ?? config('mail.from.name', 'dr.fuxx');

        $customerName = $this->data['customer_name'] ?? '';
        $customerEmail = $this->data['customer_email'] ?? '';
        $newPassword = $this->data['new_password'] ?? '';
        $changeDate = $this->data['change_date'] ?? now()->format('F j, Y');
        $changeTime = $this->data['change_time'] ?? now()->format('g:i A');
        $supportEmail = $this->data['support_email'] ?? optional($setting)->email ?? config('mail.from.address');
        $ipAddress = $this->data['ip_address'] ?? request()->ip() ?? '—';
        $loginUrl = $this->data['login_url'] ?? url('/patient-login');
        $year = $this->data['year'] ?? date('Y');
        $privacyUrl = $this->data['privacy_url'] ?? url('/privacy-policy');
        $contactUrl = $this->data['contact_url'] ?? url('/');

        return $this->from($fromAddress, $fromName)
            ->subject(__('Your New Password') . ' - ' . $appName)
            ->view('emails.forgot_password')
            ->with([
                'customerName' => $customerName,
                'customerEmail' => $customerEmail,
                'newPassword' => $newPassword,
                'changeDate' => $changeDate,
                'changeTime' => $changeTime,
                'supportEmail' => $supportEmail,
                'ipAddress' => $ipAddress,
                'loginUrl' => $loginUrl,
                'year' => $year,
                'privacyUrl' => $privacyUrl,
                'contactUrl' => $contactUrl,
                'appName' => $appName,
            ]);
    }
}
