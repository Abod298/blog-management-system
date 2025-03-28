<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class DeleteOldPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:delete-old';
    protected $description = 'Son haftada hiç yorum almayan yazılar silmesi';

    public function handle()
    {
        $deletedCount = Post::withoutConfirmedCommentsLastWeek()->delete();
        $this->info("$deletedCount Yazı başarıyla silinmiş .");
    }
}
