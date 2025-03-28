<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;

    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $post = $this->comment->post;
        $frontendUrl = env('FRONTEND_URL');
        $url = $frontendUrl . '/posts/show/' . $post->slug;

        return (new MailMessage)
            ->subject('Yorumunuz Onaylandı.')
            ->greeting('Merhaba ' . $notifiable->name . '!')
            ->line('Bıraktığınız Yorum Onaylandı !')
            ->action('Yorumu Görüntüle', url('/posts/show/' . $url))
            ->line('ُETERNA BLOG TASK');

    }
}
