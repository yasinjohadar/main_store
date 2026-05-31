<?php

namespace App\Contracts;

interface Seoable
{
    public function getSeoTitle(): string;

    public function getSeoDescription(): ?string;

    public function getSeoKeywords(): ?string;

    public function getSeoCanonical(): ?string;

    public function getSeoRobots(): string;

    public function getSeoOgTitle(): ?string;

    public function getSeoOgDescription(): ?string;

    public function getSeoOgImage(): ?string;

    public function getSeoSchemaType(): ?string;

    public function getSeoFocusKeyword(): ?string;
}
