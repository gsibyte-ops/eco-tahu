<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UpdatePesanan extends Notification
{
    use Queueable;

    public $pesan;
    public $url;

    public function __construct($pesan, $url)
    {
        $this->pesan = $pesan;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['database']; 
    }

    public function toArray($notifiable)
    {
        return [
            'pesan' => $this->pesan,
            'url' => $this->url,
        ];
    }
}