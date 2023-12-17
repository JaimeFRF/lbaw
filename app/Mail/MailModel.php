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

    // Necessary to pass data from the controller.
    public $mailData;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData, $templateName = 'default')
    {
        $this->mailData = $mailData;
        $this->templateName = $templateName;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
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
    
    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.' . $this->templateName,
        );
    }

}
