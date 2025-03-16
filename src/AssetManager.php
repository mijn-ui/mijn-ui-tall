<?php

namespace Mijnui\Mijnui;

use Illuminate\Support\Facades\Blade;

class AssetManager
{
    static function boot()
    {
        $instance = new static;
        $instance->registerAssetDirective();
    }

    public function registerAssetDirective()
    {
        Blade::directive('mijnuiScripts', function () {
            return '<script src="' . asset('vendor/mijnui/js/mijnui.js') . '"></script>';
        });

        Blade::directive('mijnuiAppearance', function ($expression) {
            return <<<PHP
            {!! app('mijnui')->mijnuiAppearance($expression) !!}
            PHP;
        });
    }

    public static function mijnuiAppearance($appearance = 'light')
    {
        return <<<HTML
<style>
    :root.dark {
        color-scheme: dark;
    }
</style>
<script>
    window.mijnui = {
        appearance: window.localStorage.getItem('mijnui.appearance') || 'system',

        applyAppearance(appearance) {
            this.appearance = appearance;

            if (appearance === 'system') {
                let media = window.matchMedia('(prefers-color-scheme: dark)');
                window.localStorage.removeItem('mijnui.appearance');
                media.matches ? this.enableDarkMode() : this.enableLightMode();
            } else if (appearance === 'dark') {
                window.localStorage.setItem('mijnui.appearance', 'dark');
                this.enableDarkMode();
            } else if (appearance === 'light') {
                window.localStorage.setItem('mijnui.appearance', 'light');
                this.enableLightMode();
            }
        },

        enableDarkMode() {
            document.documentElement.classList.add('dark');
        },

        enableLightMode() {
            document.documentElement.classList.remove('dark');
        },

        toggleAppearance() {
            if (this.appearance === 'dark') {
                this.applyAppearance('light');
            } else {
                this.applyAppearance('dark');
            }
        }
    };

    // Apply the appearance on page load
    window.mijnui.applyAppearance(window.mijnui.appearance || 'light');
</script>
HTML;
    }
}
