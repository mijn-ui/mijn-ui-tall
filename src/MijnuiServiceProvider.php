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
    }

    public function boot(): void
    {
        $this->bootAsset();
        $this->bootComponentPath();
        $this->bootTagCompiler();
    }

    public function bootAsset()
    {
        Blade::directive('mijnuiScripts', function () {
            return '<script src="' . asset('vendor/mijnui/js/mijnui.js') . '"></script>';
        });
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
        ], 'assets');
    }
}
