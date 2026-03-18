<?php

namespace Mijnui\Mijnui\Support;

/**
 * Utility class for resolving Tailwind CSS color classes used by MijnUI components.
 *
 * Provides lookup maps and normalization helpers that translate component props
 * (color name + variant) into the appropriate Tailwind utility classes, ensuring
 * consistent theming across all MijnUI components (alerts, badges, buttons, etc.).
 *
 * @package Mijnui\Mijnui\Support
 */
class ColorHelper
{
    /**
     * Resolve the Tailwind text-color class for an alert's title or description.
     *
     * Looks up the correct foreground color based on the semantic color name
     * (e.g. 'primary', 'danger') and the visual variant (e.g. 'default', 'outline').
     * Returns the fallback class if the color/variant combination is not mapped.
     *
     * @param  string  $color     The semantic color name (default, primary, secondary, success, info, warning, danger).
     * @param  string  $variant   The visual variant (default, outline, subtle, ghost).
     * @param  string  $fallback  Fallback Tailwind class if no mapping exists.
     * @return string  The resolved Tailwind text-color utility class.
     */
    public static function alertTextColor(string $color, string $variant, string $fallback = 'text-inverse-foreground'): string
    {
        $map = [
            'default' => [
                'default' => 'text-inverse-foreground',
                'outline' => 'text-inverse',
                'subtle' => 'text-inverse',
                'ghost' => 'text-inverse',
            ],
            'primary' => [
                'default' => 'text-primary-foreground',
                'outline' => 'text-primary',
                'subtle' => 'text-primary',
                'ghost' => 'text-primary',
            ],
            'secondary' => [
                'default' => 'text-secondary-foreground',
                'outline' => 'text-secondary-foreground',
                'subtle' => 'text-secondary-foreground',
                'ghost' => 'text-secondary-foreground',
            ],
            'success' => [
                'default' => 'text-success-foreground',
                'outline' => 'text-success',
                'subtle' => 'text-success',
                'ghost' => 'text-success',
            ],
            'info' => [
                'default' => 'text-info-foreground',
                'outline' => 'text-info',
                'subtle' => 'text-info',
                'ghost' => 'text-info',
            ],
            'warning' => [
                'default' => 'text-warning-foreground',
                'outline' => 'text-warning',
                'subtle' => 'text-warning',
                'ghost' => 'text-warning',
            ],
            'danger' => [
                'default' => 'text-danger-foreground',
                'outline' => 'text-danger',
                'subtle' => 'text-danger',
                'ghost' => 'text-danger',
            ],
        ];

        return $map[$color][$variant] ?? $fallback;
    }

    /**
     * Normalize variant name aliases to their canonical form.
     *
     * Maps common alternative names to the standard variant identifiers used
     * internally by MijnUI components (e.g. 'outlined' becomes 'outline',
     * 'filled' becomes 'default'). Unrecognized variants pass through unchanged.
     *
     * @param  string  $variant  The variant name to normalize.
     * @return string  The canonical variant name.
     */
    public static function normalizeVariant(string $variant): string
    {
        return match ($variant) {
            'outlined' => 'outline',
            'filled' => 'default',
            default => $variant,
        };
    }
}
