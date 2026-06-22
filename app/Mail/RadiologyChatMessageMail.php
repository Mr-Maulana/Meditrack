<?php

namespace App\Mail;

use App\Models\RadiologyResult;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RadiologyChatMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $result;
    public $messageText;
    public $senderName;

    public function __construct(RadiologyResult $result, string $messageText, string $senderName)
    {
        $this->result = $result;
        $this->messageText = $messageText;
        $this->senderName = $senderName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesan Baru terkait Radiologi - Rumkit TK III IM Lhokseumawe',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.radiology_chat_message',
            with: [
                'result' => $this->result,
                'messageText' => $this->messageText,
                'senderName' => $this->senderName,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
