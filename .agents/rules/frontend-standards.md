---
trigger: glob
globs: **/*.{blade.php,js,css,php}
---

# Frontend Standards

## Primary Framework
Livewire 4 is the primary frontend framework for this project.

## Components
- Always prefer Livewire components over raw JavaScript or other frontend frameworks.
- Keep components focused and reusable.
- Follow Laravel and Livewire best practices for state management and interactivity.

## Asset Bundling
- Vite is used for asset bundling.
- Respect the Vite configuration when adding new scripts or styles.
- Use the `@vite` directive in Blade templates for linking assets.
