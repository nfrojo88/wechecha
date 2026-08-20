<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Letter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'letter_number',
        'type',
        'date',
        'subject',
        'specification',
        'sender',
        'sender_department',
        'recipient_organization',
        'priority',
        'status',
        'created_by',
        'closed_by',
        'closed_at',
        'closing_notes',
    ];

    protected $casts = [
        'date' => 'date',
        'closed_at' => 'datetime',
    ];

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_VIEWED = 'viewed';
    const STATUS_REDIRECTED = 'redirected';
    const STATUS_CLOSED = 'closed';

    // Type Constants
    const TYPE_INCOMING = 'incoming';
    const TYPE_OUTGOING = 'outgoing';

    // Priority Constants
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Relationship: Creator (Secretary or Admin)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Closer (User who marked closed)
     */
    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * Relationship: Multi-file Attachments
     */
    public function attachments()
    {
        return $this->hasMany(LetterAttachment::class, 'letter_id');
    }

    /**
     * Relationship: Routing / Recipients / Hand-offs
     */
    public function recipients()
    {
        return $this->hasMany(LetterRecipient::class, 'letter_id')->orderBy('id', 'asc');
    }

    /**
     * Relationship: Latest Active Recipient / Hand-off
     */
    public function latestRecipient()
    {
        return $this->hasOne(LetterRecipient::class, 'letter_id')->latestOfMany('id');
    }

    /**
     * Relationship: Notifications
     */
    public function notifications()
    {
        return $this->hasMany(LetterNotification::class, 'letter_id');
    }

    /**
     * Generate next suggested letter number
     */
    public static function generateSuggestedNumber(string $type = 'incoming'): string
    {
        $prefix = ($type === self::TYPE_OUTGOING) ? 'OUT' : 'IN';
        $year = date('Y');
        $pattern = "{$prefix}-{$year}-%";

        $lastLetter = self::withTrashed()
            ->where('letter_number', 'like', $pattern)
            ->orderBy('id', 'desc')
            ->first();

        $seq = 1;
        if ($lastLetter && preg_match("/{$prefix}-{$year}-(\d+)/", $lastLetter->letter_number, $matches)) {
            $seq = intval($matches[1]) + 1;
        }

        return sprintf('%s-%s-%03d', $prefix, $year, $seq);
    }

    /**
     * Check if a specific user can view / access this letter
     */
    public function isAccessibleBy(User $user): bool
    {
        // Global admin, secretary, and creator always have access
        if ($user->hasRole(['admin', 'global_admin', 'secretary']) || $this->created_by === $user->id) {
            return true;
        }

        // Direct user recipient
        $directRecipient = $this->recipients()->where('to_user_id', $user->id)->exists();
        if ($directRecipient) {
            return true;
        }

        // Role-based recipient
        $userRoles = $user->getRoleNames()->toArray();
        if (!empty($userRoles)) {
            $roleRecipient = $this->recipients()->whereIn('to_role_name', $userRoles)->exists();
            if ($roleRecipient) {
                return true;
            }
        }

        // Forwarder / Question Sender in routing chain
        $isSenderInChain = $this->recipients()->where('from_user_id', $user->id)->exists();
        if ($isSenderInChain) {
            return true;
        }

        // Management / Executive Hierarchy
        if ($user->hasAnyRole(['gm', 'general_manager', 'managing_director', 'director', 'ceo', 'deputy_general_manager'])) {
            return true;
        }

        return false;
    }
}
