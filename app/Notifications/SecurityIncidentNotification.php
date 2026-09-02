<?php

namespace App\Notifications;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SecurityIncidentNotification extends Notification
{
    use Queueable;

    public function __construct(public Incident $incident) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $context = $this->incident->context ?? [];
        $contextSummary = empty($context)
            ? 'No additional context provided.'
            : collect($context)
                ->map(fn ($value, $key) => sprintf('%s: %s', (string) $key, json_encode($value)))
                ->implode("\n");

        return (new MailMessage)
            ->subject('Security Incident Alert: '.$this->incident->type)
            ->line('A security incident has been detected by '.config('app.name').'.')
            ->line('Type: '.$this->incident->type)
            ->line('Severity: '.$this->incident->severity)
            ->line('Occurred At: '.$this->incident->occurred_at?->toDateTimeString())
            ->line('IP Address: '.($this->incident->ip_address ?? 'N/A'))
            ->line('Context:')
            ->line($contextSummary)
            ->action('View Incidents', url('/admin/incidents'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'incident_id' => $this->incident->id,
            'type' => $this->incident->type,
            'severity' => $this->incident->severity,
            'status' => $this->incident->status,
            'occurred_at' => $this->incident->occurred_at?->toIso8601String(),
            'ip_address' => $this->incident->ip_address,
            'context' => $this->incident->context,
        ];
    }
}
