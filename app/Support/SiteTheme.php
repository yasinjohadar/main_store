<?php

namespace App\Support;

use App\Services\SiteSettingsService;

class SiteTheme
{
    public const DEFAULT_PRIMARY = '#336699';

    public const DEFAULT_BORDER_RADIUS = 5;

    public static function primary(): string
    {
        $color = site_setting(SiteSettingsService::KEY_SITE_PRIMARY_COLOR, self::DEFAULT_PRIMARY);

        return self::normalizeHex((string) $color) ?? self::DEFAULT_PRIMARY;
    }

    public static function borderRadius(): int
    {
        $value = site_setting(SiteSettingsService::KEY_SITE_BORDER_RADIUS, self::DEFAULT_BORDER_RADIUS);

        return max(0, min(50, (int) $value));
    }

    /**
     * @return array{primary: string, rgb: string, dark: string, darker: string, darkest: string, mid: string, deep: string}
     */
    public static function variants(): array
    {
        $primary = self::primary();

        return [
            'primary' => $primary,
            'rgb' => implode(', ', self::hexToRgb($primary)),
            'dark' => self::mix($primary, '#000000', 0.18),
            'darker' => self::mix($primary, '#000000', 0.35),
            'darkest' => self::mix($primary, '#000000', 0.52),
            'mid' => self::mix($primary, '#000000', 0.12),
            'deep' => self::mix($primary, '#000000', 0.25),
        ];
    }

    public static function normalizeHex(string $hex): ?string
    {
        $hex = trim($hex);

        if ($hex === '') {
            return null;
        }

        if ($hex[0] !== '#') {
            $hex = '#' . $hex;
        }

        if (preg_match('/^#([A-Fa-f0-9]{3})$/', $hex, $m)) {
            $chars = str_split($m[1]);

            return '#' . implode('', array_map(fn ($c) => $c . $c, $chars));
        }

        if (preg_match('/^#([A-Fa-f0-9]{6})$/', $hex)) {
            return strtolower($hex);
        }

        return null;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    public static function hexToRgb(string $hex): array
    {
        $hex = ltrim(self::normalizeHex($hex) ?? self::DEFAULT_PRIMARY, '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    public static function mix(string $hex1, string $hex2, float $weight): string
    {
        $weight = max(0, min(1, $weight));
        [$r1, $g1, $b1] = self::hexToRgb($hex1);
        [$r2, $g2, $b2] = self::hexToRgb($hex2);

        return sprintf(
            '#%02x%02x%02x',
            (int) round($r1 * (1 - $weight) + $r2 * $weight),
            (int) round($g1 * (1 - $weight) + $g2 * $weight),
            (int) round($b1 * (1 - $weight) + $b2 * $weight)
        );
    }
}
