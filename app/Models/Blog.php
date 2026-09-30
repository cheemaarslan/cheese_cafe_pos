<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;

class Blog extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;
    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'author',
        'category',
        'status',
        'creator_type',
        'creator_id',
        'editor_type',
        'editor_id',
    ];

    protected $casts = [
        'id'           => 'integer',
        'title'        => 'string',
        'slug'         => 'string',
        'description'  => 'string',
        'content'      => 'string',
        'author'       => 'string',
        'category'     => 'string',
        'status'       => 'integer',
        'creator_type' => 'string',
        'creator_id'   => 'integer',
        'editor_type'  => 'string',
        'editor_id'    => 'integer',
    ];


    public function getThumbAttribute(): string
    {
         if (!empty($this->getFirstMediaUrl('blog'))) {
            $item = $this->getMedia('blog')->last();
            return $item->getUrl('thumb');
        }
        // Placeholder logic for thumbnail URL
        return asset('images/blog/thumb.png');
    }

     public function getCoverAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('blog'))) {
            $item = $this->getMedia('blog')->last();
            return $item->getUrl('cover');
        }
        return asset('images/blog/cover.png');
    }

    public function getPreviewAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('blog'))) {
            $item = $this->getMedia('blog')->last();
            return $item->getUrl('preview');
        }
        return asset('images/blog/cover.png');
    }


    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->crop('crop-center', 168, 180)->format('webp')->quality(75)->sharpen(10);
        $this->addMediaConversion('cover')->crop('crop-center', 390, 270)->format('webp')->quality(75)->sharpen(10);
        $this->addMediaConversion('preview')->width(400)->format('webp')->quality(75)->sharpen(10);
    }





    public function creator()
    {
        return $this->morphTo(__FUNCTION__, 'creator_type', 'creator_id');
    }

    public function editor()
    {
        return $this->morphTo(__FUNCTION__, 'editor_type', 'editor_id');
    }
}
