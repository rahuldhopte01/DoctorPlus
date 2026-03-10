<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuestionnaireSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $viewData = [];

    public function __construct(array $data)
    {
        $this->viewData = array_merge([
            'app_name' => config('mail.from.name', 'dr.fuxx'),
            'year' => date('Y'),
            'privacy_url' => url('/privacy-policy'),
            'contact_url' => url('/'),
            'support_email' => optional(\App\Models\Setting::first())->email ?? config('mail.from.address'),
        ], $data);
    }

    public function build()
    {
        $fromAddress = config('mail.from.address', env('MAIL_FROM_ADDRESS'));
        $fromName = config('mail.from.name', env('MAIL_FROM_NAME'));

        return $this->from($fromAddress, $fromName)
            ->subject(__('Questionnaire Submitted') . ' - ' . ($this->viewData['app_name'] ?? 'dr.fuxx'))
            ->view('emails.questionnaire_submitted')
            ->with($this->viewData);
    }
}
