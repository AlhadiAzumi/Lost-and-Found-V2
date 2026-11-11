<?php

namespace App\Mail;

use App\Models\CampusApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampusAdminInvitation extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public CampusApplication $application,
        public string $token,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Portal kampus disetujui');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.campus-admin-invitation',
            with: [
                'setupUrl' => route('campus-applications.setup', [
                    'application' => $this->application->id,
                    'token' => $this->token,
                ]),
            ],
        );
    }
}
