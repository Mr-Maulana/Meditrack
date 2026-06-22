<?php

namespace App\Mail;

use App\Models\RadiologyResult;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class RadiologyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $result;

    public function __construct(RadiologyResult $result)
    {
        $this->result = $result;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hasil Baca & Laporan Radiologi - ' . $this->result->patient->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.radiology_report',
        );
    }

    public function attachments(): array
    {
        // Generate PDF using the same view as the email content
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.radiology_report_pdf', [
            'result' => $this->result,
        ]);

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                $pdf->output(),
                'radiology_report.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
