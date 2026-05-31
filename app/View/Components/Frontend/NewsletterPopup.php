<?php

namespace App\View\Components\Frontend;

use App\Services\SiteSettingsService;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NewsletterPopup extends Component
{
    public array $config;

    public function __construct(SiteSettingsService $siteSettings)
    {
        $this->config = $siteSettings->newsletterPopupConfig();
    }

    public function shouldRender(): bool
    {
        return (bool) ($this->config['enabled'] ?? false);
    }

    public function render(): View
    {
        return view('components.frontend.newsletter-popup');
    }
}
