<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = ['title', 'body', 'slug', 'published_at', 'user_id'];

    protected static $recordEvents = ['updated'];


    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }


    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['title', 'body']);
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->sharpen(10);
    }
    public function scopeWithoutConfirmedCommentsLastWeek($query)
    {
        return $query->whereDoesntHave('comments', function ($query) {
            $query->whereNotNull('confirmed_at'); // فقط التعليقات المؤكدة
        })
            ->where('created_at', '<', now()->subDays(7)); // المنشورات الأقدم من 7 أيام
    }
    public function scopePublished($query): void
    {
        $query->whereNotNull('published_at');
    }
}
