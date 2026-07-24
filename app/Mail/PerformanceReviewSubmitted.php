<?php

namespace App\Mail;

use App\Models\PerformanceReview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PerformanceReviewSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public PerformanceReview $review;

    public function __construct(PerformanceReview $review)
    {
        $this->review = $review;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Performance Review Submitted for Approval - ' . $this->review->employee->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.performance-review-submitted',
            with: [
                'review' => $this->review,
                'employee' => $this->review->employee,
                'reviewer' => $this->review->reviewer,
            ],
        );
    }
}
