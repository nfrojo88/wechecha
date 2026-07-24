<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyManpowerReport extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $reportData;
    public $weekStarting;

    public function __construct($reportData, $weekStarting)
    {
        $this->reportData = $reportData;
        $this->weekStarting = $weekStarting;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Weekly Manpower Report - Week of ' . $this->weekStarting->format('F d, Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-manpower-report',
            with: [
                'reportData' => $this->reportData,
                'weekStarting' => $this->weekStarting,
            ],
        );
    }
}
