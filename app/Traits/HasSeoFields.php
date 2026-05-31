<?php

namespace App\Traits;

use App\Contracts\Seoable;

trait HasSeoFields
{
    public function getSeoTitle(): string
    {
        return $this->meta_title ?: ($this->name ?? $this->title ?? '');
    }

    public function getSeoDescription(): ?string
    {
        return $this->meta_description
            ?: ($this->short_description ?? $this->excerpt ?? $this->description ?? null);
    }

    public function getSeoKeywords(): ?string
    {
        return $this->meta_keywords ?? null;
    }

    public function getSeoCanonical(): ?string
    {
        return $this->canonical_url ?? null;
    }

    public function getSeoRobots(): string
    {
        return $this->robots_meta ?? 'index,follow';
    }

    public function getSeoOgTitle(): ?string
    {
        return $this->og_title ?? null;
    }

    public function getSeoOgDescription(): ?string
    {
        return $this->og_description ?? null;
    }

    public function getSeoOgImage(): ?string
    {
        return $this->og_image ?? null;
    }

    public function getSeoSchemaType(): ?string
    {
        return $this->schema_type ?? null;
    }

    public function getSeoFocusKeyword(): ?string
    {
        return $this->focus_keyword ?? null;
    }
}
