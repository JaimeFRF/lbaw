<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class MailModel extends Mailable
{
    public $mailData;

    public function __construct($mailData, $templateName = 'default')
    {
        $this->mailData = $mailData;
        $this->templateName = $templateName;
    }

    public function envelope()
    {   
        if ($this->templateName == 'example'){

            return new Envelope(
                from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
                subject: 'Antiquus: Reset Password',
            );
        }
        else {
            return new Envelope(
                from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
                subject: 'Antiquus: Set Password',
            );
        }
    }
    
    public function content()
    {
        return new Content(
            view: 'emails.' . $this->templateName,
        );
    }

}
