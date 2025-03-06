# MIJN UI

MIJNUI is a UI component library designed specifically for [Livewire](https://livewire.laravel.com) applications. It is built with [Tailwind CSS](https://tailwindcss.com), offering a collection of components that are simple to implement and highly customizable.

Explore the components in action by visiting [https://mijn-ui.vercel.app](https://mijn-ui.vercel.app).

## Prerequisites

Before installing Mijnui, ensure your project meets the following requirements:

* Laravel v10.0+
* Livewire v3.5.19+
* Tailwind CSS v4.0+

## Installation

To install, run the following command from your project’s root directory:

```bash
composer require mijnui/mijnui
```

## Getting Started

### 1. Include Mijnui Assets

Add the `@mijnuiScripts` Blade directives to your layout file:

```html
<body>
    ...
    @mijnuiScripts
</body>
```

### 2. Set up Tailwind CSS

Mijnui uses Tailwind CSS for its default styling. Add the following configuration to your `resources/css/app.css` file:

```css
@import '../../vendor/mijnui/mijnui/dist/mijnui.css';

@tailwind base;
@tailwind components;
@tailwind utilities;
```

If you haven't installed Tailwind yet, you can follow the installation guide on the [Tailwind website](https://tailwindcss.com/docs/guides/laravel).


Configure Tailwind to use this font in your `resources/css/app.css`:

## Asset Publishing

This command will publish the necessary assets for the MIJN UI component library to your Laravel project. The assets include CSS, JavaScript, and other resources needed for the components to function properly in your application.

```bash
php artisan vendor:publish --tag=mijnui-assets
```
