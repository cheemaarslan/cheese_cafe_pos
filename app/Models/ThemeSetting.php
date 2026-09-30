<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ThemeSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = "settings";

    public function getLogoAttribute() : string
    {
        if (!empty($this->getFirstMediaUrl('theme-logo'))) {
            return asset($this->getMedia('theme-logo')->last()->getUrl('logo'));
        }
        return asset('images/theme/theme-logo.png');
    }

    public function getFaviconLogoAttribute() : string
    {
        if (!empty($this->getFirstMediaUrl('theme-favicon-logo'))) {
            return asset($this->getMedia('theme-favicon-logo')->last()->getUrl('favicon'));
        }
        return asset('images/theme/theme-favicon-logo.png');
    }

    public function getFooterLogoAttribute() : string
    {
        if (!empty($this->getFirstMediaUrl('theme-footer-logo'))) {
            return asset($this->getMedia('theme-footer-logo')->last()->getUrl('footer_logo'));
        }
        return asset('images/theme/theme-footer-logo.png');
    }

    public function getFooterImageAttribute() : string
    {
        if (!empty($this->getFirstMediaUrl('theme-footer-image'))) {
            return asset($this->getMedia('theme-footer-image')->last()->getUrl('footer_image'));
        }
        return asset('images/theme/theme-footer-image.png');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('logo')->width(448)->height(140)->format('webp')->quality(80)->nonQueued();
        $this->addMediaConversion('favicon')->width(128)->height(128)->format('webp')->quality(80)->nonQueued();
        $this->addMediaConversion('footer_logo')->width(448)->height(140)->format('webp')->quality(80)->nonQueued();
        $this->addMediaConversion('footer_image')->width(320)->height(420)->format('webp')->quality(80)->nonQueued();
    }
}
