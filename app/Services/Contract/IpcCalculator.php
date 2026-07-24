<?php

namespace App\Services\Contract;

use App\Models\IpcRecord;
use App\Models\SubconAgreement;

class IpcCalculator
{
    /**
     * Calculates the Interim Payment Certificate details based on cumulative work.
     * 
     * @param SubconAgreement $agreement The subcontractor agreement context
     * @param float $cumulativeWorkValue The total gross value of work done to date
     * @return array Calculated breakdown
     */
    public function calculatePayment(SubconAgreement $agreement, float $cumulativeWorkValue): array
    {
        // Calculate retention amount
        $retentionPercentage = $agreement->retention_percentage ?? 0;
        $retentionAmount = $cumulativeWorkValue * ($retentionPercentage / 100);

        // Calculate advance payment deduction if applicable
        $advanceDeduction = 0;
        // Logic for advance deduction could go here based on agreement terms

        // Total gross amount certified to date
        $totalCertifiedToDate = $cumulativeWorkValue - $retentionAmount - $advanceDeduction;

        // Sum up previous payments from previous IPCs
        $previousIpcs = IpcRecord::where('subcon_agreement_id', $agreement->id)
            ->where('status', 'approved')
            ->sum('amount_certified');

        // Net amount to certify for the current period
        $netAmountToCertify = $totalCertifiedToDate - $previousIpcs;

        // Prevent negative certification unless specifically handling a reversal
        if ($netAmountToCertify < 0) {
            $netAmountToCertify = 0;
        }

        return [
            'gross_work_to_date' => $cumulativeWorkValue,
            'retention_amount'   => $retentionAmount,
            'advance_deduction'  => $advanceDeduction,
            'total_certified_to_date' => $totalCertifiedToDate,
            'less_previous_payments' => $previousIpcs,
            'net_amount_to_certify'  => $netAmountToCertify,
        ];
    }
}
