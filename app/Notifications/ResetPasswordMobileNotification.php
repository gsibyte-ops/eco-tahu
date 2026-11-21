<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordMobileNotification extends Notification
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $email = $notifiable->getEmailForPasswordReset();
        $webUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $email,
        ], false));
        
        $mobileUrl = "ecotahu://reset-password?token={$this->token}&email=" . urlencode($email);

        return (new MailMessage)
            ->subject('Reset Password Akun EcoTahu')
            ->greeting('Halo, ' . ($notifiable->username ?? 'Pelanggan EcoTahu') . '!')
            ->line('Anda menerima email ini karena ada permintaan reset password untuk akun Anda di EcoTahu.')
            ->line('Kode Token Reset Password Anda:')
            ->line('**' . $this->token . '**')
            ->action('Reset Password di Aplikasi EcoTahu', $mobileUrl)
            ->line('Atau Anda juga dapat mereset password melalui web browser pada link berikut:')
            ->line('[' . $webUrl . '](' . $webUrl . ')')
            ->line('Kode token ini berlaku selama 60 menit.')
            ->salutation('Salam hangat, Tim EcoTahu');
    }
}
