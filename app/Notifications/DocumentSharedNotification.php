<?php

namespace App\Notifications;

use App\Models\DmsDocument;
use App\Models\DmsShare;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class DocumentSharedNotification extends Notification
{
    use Queueable;

    public function __construct(public DmsDocument $document, public DmsShare $share, public User $sharedBy) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $media = $this->document->currentVersionMedia();
        $mail = (new MailMessage)
            ->subject("{$this->sharedBy->name} shared a document with you: {$this->document->name}")
            ->line("{$this->sharedBy->name} shared \"{$this->document->name}\" with you via ".config('app.name').'.');

        $maxAttachmentBytes = (int) config('dms.email_attachment_max_bytes');

        if ($media && $media->size <= $maxAttachmentBytes) {
            $mail->attach($media->getPath(), ['as' => $this->document->name, 'mime' => $media->mime_type]);
            $mail->line('The document is attached to this email.');
        } else {
            $mail->action('Download Document', URL::temporarySignedRoute(
                'dms.shares.download',
                now()->addDays((int) config('dms.share_link_expires_days')),
                ['share' => $this->share->id]
            ));
        }

        return $mail;
    }
}
