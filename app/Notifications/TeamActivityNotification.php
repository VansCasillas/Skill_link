<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class TeamActivityNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $description,
        public string $url,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
        ];
    }
}
