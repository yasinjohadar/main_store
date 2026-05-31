<?php

namespace App\Models;

use App\Contracts\Seoable;
use App\Traits\HasSeoFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Brand extends Model implements Seoable
{
    use HasFactory, HasSeoFields;

    protected $fillable = [
        'name', 'slug', 'image', 'order',
        'meta_title', 'meta_description', 'meta_keywords',
        'canonical_url', 'focus_keyword', 'focus_keyword_synonyms',
        'og_title', 'og_description', 'og_image', 'robots_meta', 'schema_type',
    ];

    protected $casts = ['order' => 'integer'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });

        static::updating(function ($brand) {
            if ($brand->isDirty('name') && !$brand->isDirty('slug')) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }
}
