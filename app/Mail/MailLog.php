<?php

namespace App\Mail;

use App\Shared\Mail\BaseMail;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use App\Shared\Mail\Traits\HasExcelFile;

class MailLog extends BaseMail
{ 
    use HasExcelFile;

    /**
     * Create a new message instance.
     *
     * @param array $errors
     * @return void
     */
    public function __construct(
        protected array $errors
    ) { }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "E-mail Log - " . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.empty'
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $path = $this->createFileExcelSheet($this->errors);

        return [
            Attachment::fromPath($path)
                        ->as('logs-' . now()->format('Y-m-d') . '.xlsx')
        ];
    }
}
