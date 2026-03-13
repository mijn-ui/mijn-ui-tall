# CLAUDE.md

## Project Overview

**mijnui** — A Blade component library for the TALL stack (Tailwind CSS v4+, Alpine.js, Laravel v10+, Livewire v3.5.19+). All components are anonymous Blade components using `<mijnui:component-name>` syntax via a custom tag compiler.

## Architecture

### Core Classes (`src/`)
- **MijnuiServiceProvider** — Registers components from `src/resources/views/components/`, sets up the custom tag compiler, publishes CSS/JS assets
- **MijnuiTagCompiler** — Extends Laravel's `ComponentTagCompiler` to support `<mijnui:component-name>` HTML-like syntax
- **AssetManager** — Handles asset registration, `@mijnuiScripts` and `@mijnuiAppearance` Blade directives
- **Mijnui** — Facade for the AssetManager

### Components (`src/resources/views/components/`)
~39 anonymous Blade components organized by directory. Each component has `index.blade.php` plus optional sub-parts:

- **Basic**: badge, button, icon, separator, progress, strength-indicator, switch, toggle
- **Input**: input (clearable/icon/viewable), textarea, checkbox, radio, select, label, field, with-field, error
- **Layout**: card, container, wrapper, main, header, navbar, breadcrumbs, sidebar
- **Interactive**: modal, alert-dialog, drawer, popover, dropdown, command
- **Data Display**: accordion, tab, table, pagination, list
- **Advanced**: calendar, gantt, kanban, code-block, component-preview
- **Misc**: avatar, brand, description

### Component Conventions
- Use `@props([...])` for component configuration
- Use `$attributes->merge()` for attribute forwarding
- Compose via named slots (`{{ $slot }}`, `@isset`)
- Props use kebab-case in templates, camelCase in PHP

### Built Assets (`dist/`)
- `mijnui.css` — HSL-based CSS variables for theming (primary, secondary, success, warning, danger + variants). Dark mode via `:root.dark`
- `mijnui.js` — Alpine.js store for sidebar state and dark mode toggling with localStorage persistence
- `calendar/` — Day.js locale/plugin assets

### Theming
CSS variable color system using HSL values. Appearance modes: `system`, `dark`, `light`. Dark mode uses the `class` strategy on `:root`.

## Namespace
`Mijnui\Mijnui` (PSR-4 mapped in composer.json: `src/`)

## Usage Syntax
```blade
{{-- Tag compiler syntax --}}
<mijnui:button variant="primary">Click me</mijnui:button>

{{-- Standard Blade syntax --}}
<x-mijnui::button variant="primary">Click me</x-mijnui::button>
```

## Publishing Assets
```bash
php artisan vendor:publish --tag=mijnui-assets
```
