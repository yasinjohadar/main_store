<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    public function getRouteKeyName(): string
    {
        return 'key';
    }

    protected $fillable = [
        'key',
        'title',
        'subtitle',
        'eyebrow',
        'is_visible',
        'sort_order',
        'settings',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
        'settings' => 'array',
    ];

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    public function resolvedTitle(?string $fallback = null): ?string
    {
        return $this->title ?: $fallback;
    }

    public function resolvedSubtitle(?string $fallback = null): ?string
    {
        return $this->subtitle ?: $fallback;
    }

    public function resolvedEyebrow(?string $fallback = null): ?string
    {
        return $this->eyebrow ?: $fallback;
    }
}
