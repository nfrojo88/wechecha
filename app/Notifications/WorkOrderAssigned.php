<?php

namespace App\Notifications;

use App\Models\EngWorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkOrderAssigned extends Notification
{
    use Queueable;

    public function __construct(public EngWorkOrder $workOrder) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Work Order Assigned: {$this->workOrder->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have been assigned a new work order.")
            ->line("**{$this->workOrder->title}**")
            ->line("📍 Location: " . ($this->workOrder->location ?? 'N/A'))
            ->line("📅 Start: " . $this->workOrder->start_datetime->format('M d, Y H:i'))
            ->line("🏁 End: " . $this->workOrder->end_datetime->format('M d, Y H:i'))
            ->line("⚡ Priority: " . strtoupper($this->workOrder->priority))
            ->action('View Work Order', route('eng-schedule.show', $this->workOrder->id))
            ->line('Please accept or decline as soon as possible.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'work_order_assigned',
            'work_order_id' => $this->workOrder->id,
            'title'         => $this->workOrder->title,
            'start'         => $this->workOrder->start_datetime->toIso8601String(),
            'priority'      => $this->workOrder->priority,
            'message'       => "You have been assigned: {$this->workOrder->title}",
            'url'           => route('eng-schedule.show', $this->workOrder->id),
        ];
    }
}
