<?php

namespace Mijnui\Mijnui;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Support\ServiceProvider;

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
                // Compile opening tags
                $value = $compiler->compileOpeningTags($value);

                // Compile self-closing tags
                $value = $compiler->compileSelfClosingTags($value);

                // Compile closing tags
                $value = $compiler->compileClosingTags($value);

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
