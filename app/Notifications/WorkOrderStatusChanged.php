<?php

namespace App\Notifications;

use App\Models\EngWorkOrder;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkOrderStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public EngWorkOrder $workOrder,
        public string $newStatus,
        public User $changedBy
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = ucwords(str_replace('_', ' ', $this->newStatus));

        return (new MailMessage)
            ->subject("Work Order {$statusLabel}: {$this->workOrder->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A work order status has been updated by {$this->changedBy->name}.")
            ->line("**{$this->workOrder->title}**")
            ->line("New Status: **{$statusLabel}**")
            ->action('View Work Order', route('eng-schedule.show', $this->workOrder->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'work_order_status_changed',
            'work_order_id' => $this->workOrder->id,
            'title'         => $this->workOrder->title,
            'new_status'    => $this->newStatus,
            'changed_by'    => $this->changedBy->name,
            'message'       => "{$this->changedBy->name} updated \"{$this->workOrder->title}\" to " . ucwords(str_replace('_', ' ', $this->newStatus)),
            'url'           => route('eng-schedule.show', $this->workOrder->id),
        ];
    }
}
