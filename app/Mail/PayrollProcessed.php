<?php

namespace App\Mail;

use App\Models\Payroll;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayrollProcessed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Payroll $payroll;

    public function __construct(Payroll $payroll)
    {
        $this->payroll = $payroll;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payroll Processed - ' . $this->payroll->period,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payroll-processed',
            with: [
                'payroll' => $this->payroll,
                'employee' => $this->payroll->employee,
            ],
        );
    }
}
