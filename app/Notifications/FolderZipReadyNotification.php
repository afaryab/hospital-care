<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class FolderZipReadyNotification extends Notification
{
    use Queueable;

    public function __construct(public string $folderName, public string $zipFilename) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'dms.folders.zip-download',
            now()->addDay(),
            ['filename' => $this->zipFilename]
        );

        return (new MailMessage)
            ->subject("Your zip download is ready: {$this->folderName}")
            ->line("The zip archive for \"{$this->folderName}\" has finished building.")
            ->action('Download Zip', $url)
            ->line('This link expires in 24 hours.');
    }
}
