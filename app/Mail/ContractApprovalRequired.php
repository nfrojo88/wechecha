<?php

namespace App\Mail;

use App\Models\EmployeeContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractApprovalRequired extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public EmployeeContract $contract;
    public $approvalLevel;

    public function __construct(EmployeeContract $contract, $approvalLevel)
    {
        $this->contract = $contract;
        $this->approvalLevel = $approvalLevel;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contract Approval Required - ' . $this->contract->employee->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contract-approval-required',
            with: [
                'contract' => $this->contract,
                'employee' => $this->contract->employee,
                'approvalLevel' => $this->approvalLevel,
            ],
        );
    }
}
