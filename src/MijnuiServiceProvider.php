<?php

namespace Mijnui\Mijnui;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Support\ServiceProvider;

/**
 * MijnUI - A TALL stack (Tailwind, Alpine.js, Laravel, Livewire) Blade component library.
 *
 * This service provider bootstraps MijnUI by registering:
 * - A singleton {@see MijnuiTagCompiler} that compiles <mijnui:*> tags into standard Blade components.
 * - A singleton {@see AssetManager} (bound as 'mijnui') for managing publishable CSS/JS assets.
 * - The 'mijnui' view namespace pointing to the package's bundled Blade views.
 * - Anonymous Blade component paths for all top-level and subdirectory components.
 *
 * Component discovery works by scanning the package's `resources/views/components` directory.
 * Top-level components are registered under the 'mijnui' prefix, while subdirectories are
 * registered under 'mijnui.{directory-name}', enabling nested component namespacing
 * (e.g. <x-mijnui.button::icon />).
 *
 * Assets (CSS, JS, calendar locale files) are publishable via the 'mijnui-assets' tag
 * and are placed under `public/vendor/mijnui/`.
 *
 * @package Mijnui\Mijnui
 */
class MijnuiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MijnuiTagCompiler::class);
        $this->app->singleton('mijnui', function () {
            return new AssetManager();
        });
    }

    public function boot(): void
    {
        $this->bootComponentPath();
        $this->bootTagCompiler();
        $this->bootPublishes();

        AssetManager::boot();

//        app('mijnui')->boot();

    }


    public function bootComponentPath()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'mijnui');

        Blade::anonymousComponentPath(__DIR__.'/resources/views/components', 'mijnui');

        $componentsPath = __DIR__.'/resources/views/components';
        $directories = array_filter(glob($componentsPath.'/*'), 'is_dir');

        foreach ($directories as $directory) {
            $name = basename($directory);
            Blade::anonymousComponentPath($directory, "mijnui.$name");
        }
    }


    public function bootTagCompiler()
    {
        $compiler = new MijnuiTagCompiler();
        $this->app->extend('blade.compiler', function (BladeCompiler $bladeCompiler) use ($compiler) {
            $bladeCompiler->extend(function ($value) use ($compiler) {
                // Protect @php...@endphp blocks from tag compilation
                // so that mijnui tags inside PHP strings are not compiled
                $phpBlocks = [];
                $value = preg_replace_callback('/@php(.*?)@endphp/s', function ($match) use (&$phpBlocks) {
                    $placeholder = '___MIJNUI_PHP_BLOCK_' . count($phpBlocks) . '___';
                    $phpBlocks[$placeholder] = $match[0];
                    return $placeholder;
                }, $value);

                // Compile opening tags
                $value = $compiler->compileOpeningTags($value);

                // Compile self-closing tags
                $value = $compiler->compileSelfClosingTags($value);

                // Compile closing tags
                $value = $compiler->compileClosingTags($value);

                // Restore @php...@endphp blocks
                $value = str_replace(array_keys($phpBlocks), array_values($phpBlocks), $value);

                return $value;
            });

            return $bladeCompiler;
        });

    }

    public function bootPublishes()
    {
        $this->publishes([
            __DIR__.'/../dist/mijnui.css' => public_path('vendor/mijnui/css/mijnui.css'),
            __DIR__.'/../dist/mijnui.js' => public_path('vendor/mijnui/js/mijnui.js'),

            // Calendar
            __DIR__.'/../dist/calendar/dayjs.min.js' => public_path('vendor/mijnui/js/calendar/dayjs.min.js'),
            __DIR__.'/../dist/calendar/localeData.js' => public_path('vendor/mijnui/js/calendar/localeData.js'),
            __DIR__.'/../dist/calendar/advancedFormat.js' => public_path('vendor/mijnui/js/calendar/advancedFormat.js'),
            __DIR__.'/../dist/calendar/locale/en.js' => public_path('vendor/mijnui/js/calendar/locale/en.js'),
            __DIR__.'/../dist/calendar/locale/my.js' => public_path('vendor/mijnui/js/calendar/locale/my.js'),
        ], 'mijnui-assets');
    }
}
