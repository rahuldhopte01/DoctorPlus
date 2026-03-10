<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer_name;
    public $customer_email;
    public $registration_date;
    public $login_url;
    public $year;
    public $privacy_url;
    public $contact_url;
    public $appName;

    public function __construct(array $data)
    {
        $this->customer_name = $data['customer_name'] ?? '';
        $this->customer_email = $data['customer_email'] ?? '';
        $this->registration_date = $data['registration_date'] ?? now()->format('F j, Y');
        $this->login_url = $data['login_url'] ?? url('/patient-login');
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
            ->subject(__('Registration Successful') . ' - ' . $this->appName)
            ->view('emails.registration')
            ->with([
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'registration_date' => $this->registration_date,
                'login_url' => $this->login_url,
                'year' => $this->year,
                'privacy_url' => $this->privacy_url,
                'contact_url' => $this->contact_url,
                'appName' => $this->appName,
            ]);
    }
}
