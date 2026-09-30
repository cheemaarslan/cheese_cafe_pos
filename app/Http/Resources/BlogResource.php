<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray($request): array
    {
        // Get image URLs directly to avoid any accessor issues
        $imageUrl = '';
        $thumbUrl = '';
        $coverUrl = '';
        
        if (!empty($this->getFirstMediaUrl('blog'))) {
            $media = $this->getMedia('blog')->last();
            if ($media) {
                $imageUrl = $media->getUrl('preview');
                $thumbUrl = $media->getUrl('thumb');
                $coverUrl = $media->getUrl('cover');
            }
        }
        
        if (empty($imageUrl)) {
            $imageUrl = asset('images/blog/cover.png');
            $thumbUrl = asset('images/blog/thumb.png');
            $coverUrl = asset('images/blog/cover.png');
        }
        
        return [
            'id'          => $this->id,
            'title'       => $this->title ?? '',
            'slug'        => $this->slug,
            'description' => $this->description ?? '',
            'content'     => $this->content ?? '',
            'author'      => $this->author ?? '',
            'category'    => $this->category ?? '',
            'status'      => $this->status,
            'image'       => $imageUrl,
            'thumb'       => $thumbUrl,
            'cover'       => $coverUrl,
            'created_at'  => $this->created_at ? $this->created_at->toISOString() : null,
            'updated_at'  => $this->updated_at ? $this->updated_at->toISOString() : null,
        ];
    }
}
