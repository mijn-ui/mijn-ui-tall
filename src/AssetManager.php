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

    public static function mijnuiAppearance()
    {
        return <<<HTML
            <style>
                :root.dark {
                    color-scheme: dark;
                }
            </style>
            <script>
                window.mijnui = {
                    applyAppearance (appearance) {
                        let applyDark = () => document.documentElement.classList.add('dark')
                        let applyLight = () => document.documentElement.classList.remove('dark')

                        if (appearance === 'system') {
                            let media = window.matchMedia('(prefers-color-scheme: dark)')

                            window.localStorage.removeItem('mijnui.appearance')

                            media.matches ? applyDark() : applyLight()
                        } else if (appearance === 'dark') {
                            window.localStorage.setItem('mijnui.appearance', 'dark')

                            applyDark()
                        } else if (appearance === 'light') {
                            window.localStorage.setItem('mijnui.appearance', 'light')

                            applyLight()
                        }
                    }
                }

                // Apply the appearance on page load
                window.mijnui.applyAppearance(window.localStorage.getItem('mijnui.appearance') || 'system');
            </script>
            HTML;
    }
}
