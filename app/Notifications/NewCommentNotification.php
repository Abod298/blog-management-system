<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;

    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $post = $this->comment->post;
        $frontendUrl = env('FRONTEND_URL');
        $url = $frontendUrl . '/posts/show/' . $post->slug;

        return (new MailMessage)
            ->subject('Gönderinize Yeni Bir Yorum Yapıldı')
            ->greeting('Merhaba ' . $notifiable->name . '!')
            ->line('Gönderinize yeni bir yorum yapıldı.')
            ->action('Yorumu Görüntüle', url('/posts/' . $url))
            ->line('ُETERNA BLOG TASK');
    }
}
