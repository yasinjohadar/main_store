<?php

namespace App\Models;

use App\Contracts\Seoable;
use App\Traits\HasSeoFields;
use Illuminate\Database\Eloquent\Model;

class Page extends Model implements Seoable
{
    use HasSeoFields;

    protected $fillable = [
        'slug', 'route_name', 'title', 'excerpt', 'content', 'is_active',
        'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
        'focus_keyword', 'focus_keyword_synonyms', 'og_title', 'og_description',
        'og_image', 'robots_meta', 'schema_type',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
