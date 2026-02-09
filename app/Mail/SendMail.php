<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $content;

    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content, $subject)
    {
        $this->content = $content;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fromAddress = config('mail.from.address', env('MAIL_FROM_ADDRESS'));
        $fromName = config('mail.from.name', env('MAIL_FROM_NAME'));

        return $this->from($fromAddress, $fromName)->subject($this->subject)
            ->view('send_mail')->with([
            'content' => $this->content,
        ]);
    }
}
