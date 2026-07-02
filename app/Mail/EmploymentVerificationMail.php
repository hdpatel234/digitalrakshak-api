<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmploymentVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationUrl;
    public $candidateName;

    /**
     * Create a new message instance.
     */
    public function __construct($verificationUrl, $candidateName)
    {
        $this->verificationUrl = $verificationUrl;
        $this->candidateName = $candidateName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Employment Verification Request - ' . $this->candidateName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.employment_verification',
            with: [
                'verificationUrl' => $this->verificationUrl,
                'candidateName' => $this->candidateName,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
