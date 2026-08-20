<?php

namespace App\Services;

use App\Models\ProcurementSmsLog;
use Illuminate\Support\Facades\Log;

/**
 * Procurement SMS Service
 * 
 * Sends SMS notifications at each procurement lifecycle stage transition.
 * Uses Africa's Talking gateway (configurable via .env).
 * Falls back gracefully if credentials are not set.
 */
class ProcurementSmsService
{
    private string $apiKey;
    private string $username;
    private string $shortcode;
    private bool   $enabled;

    public function __construct()
    {
        $this->apiKey    = config('services.africastalking.api_key', '');
        $this->username  = config('services.africastalking.username', 'sandbox');
        $this->shortcode = config('services.africastalking.shortcode', '');
        $this->enabled   = !empty($this->apiKey) && config('services.africastalking.enabled', false);
    }

    /**
     * Send an SMS to a phone number and log the attempt.
     *
     * @param int    $purchaseRequestId
     * @param string $phone             E.164 format e.g. +251911123456
     * @param string $recipientRole
     * @param string $message
     */
    public function send(int $purchaseRequestId, string $phone, string $recipientRole, string $message): void
    {
        if (empty($phone)) {
            Log::warning("ProcurementSMS: No phone for role [{$recipientRole}] on PR #{$purchaseRequestId}");
            return;
        }

        $status = 'failed';
        $error  = null;

        if ($this->enabled) {
            try {
                $response = $this->dispatchViaAfricasTalking($phone, $message);
                $status   = $response ? 'sent' : 'failed';
            } catch (\Throwable $e) {
                $error = $e->getMessage();
                Log::error("ProcurementSMS error: {$error}");
            }
        } else {
            // When not configured, log the message for debugging
            Log::info("ProcurementSMS [SIMULATED] → {$phone} | {$recipientRole} | {$message}");
            $status = 'sent'; // simulate success in dev
        }

        // Always log to DB for traceability
        try {
            \DB::table('procurement_sms_logs')->insert([
                'purchase_request_id' => $purchaseRequestId,
                'recipient_phone'     => $phone,
                'recipient_role'      => $recipientRole,
                'message'             => $message,
                'status'              => $status,
                'error_message'       => $error,
                'sent_at'             => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("ProcurementSMS DB log failed: " . $e->getMessage());
        }
    }

    /**
     * Send to all users of a given role who have a phone number.
     */
    public function notifyRole(int $purchaseRequestId, string $roleName, string $message): void
    {
        $users = \App\Models\User::role($roleName)
            ->whereHas('employee', fn($q) => $q->whereNotNull('phone')->where('phone', '!=', ''))
            ->with('employee:id,user_id,phone')
            ->get();

        foreach ($users as $user) {
            $phone = $user->employee?->phone ?? null;
            if ($phone) {
                // Normalize to E.164 if Ethiopian number
                $phone = $this->normalizePhone($phone);
                $this->send($purchaseRequestId, $phone, $roleName, $message);
            }
        }
    }

    private function dispatchViaAfricasTalking(string $phone, string $message): bool
    {
        $url  = 'https://api.africastalking.com/version1/messaging';
        $data = [
            'username' => $this->username,
            'to'       => $phone,
            'message'  => $message,
        ];
        if ($this->shortcode) {
            $data['from'] = $this->shortcode;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'apiKey: ' . $this->apiKey,
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_TIMEOUT        => 10,
        ]);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 201) {
            throw new \RuntimeException("Africa's Talking HTTP {$httpCode}: {$result}");
        }

        return true;
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        // Ethiopian numbers: starts with 09 → +2519
        if (str_starts_with($phone, '09') && strlen($phone) === 10) {
            $phone = '+251' . substr($phone, 1);
        }
        // Already international
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }
        return $phone;
    }
}
