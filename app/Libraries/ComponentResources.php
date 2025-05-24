<?php

namespace App\Libraries;

class ComponentResources
{
    // Add your library methods here

    /**
     * Get the default minified JS and CSS resources for a component.
     * @param string $componentName The component name (e.g., 'navbar')
     * @return array ['js' => ..., 'css' => ...]
     */
    public static function getDefaultResources(string $componentName): array
    {
        $defaults = [
            'navbar' => [
                'js' => 'resources/js/components/navbar.min.js',
                'css' => 'resources/css/components/navbar.min.css',
            ],
            'footer' => [
                'js' => 'resources/js/components/footer.min.js',
                'css' => 'resources/css/components/footer.min.css',
            ],
            'sidebar' => [
                'js' => 'resources/js/components/sidebar.min.js',
                'css' => 'resources/css/components/sidebar.min.css',
            ],
        ];
        if (isset($defaults[$componentName])) {
            return $defaults[$componentName];
        }
        // fallback to generic pattern
        $base = 'resources/';
        return [
            'js' => $base . 'js/components/' . $componentName . '.min.js',
            'css' => $base . 'css/components/' . $componentName . '.min.css',
        ];
    }

    /**
     * Get all default component resources (js/css) as arrays for styles/scripts.
     * @return array ['styles' => [...], 'scripts' => [...]]
     */
    public static function getAllDefaultResources(): array
    {
        $defaults = [
            'navbar' => [
                'js' => 'resources/js/components/navbar.min.js',
                'css' => 'resources/css/components/navbar.min.css',
            ],
            'footer' => [
                'js' => 'resources/js/components/footer.min.js',
                'css' => 'resources/css/components/footer.min.css',
            ],
            'sidebar' => [
                'js' => 'resources/js/components/sidebar.min.js',
                'css' => 'resources/css/components/sidebar.min.css',
            ],
        ];
        $styles = [];
        $scripts = [];
        foreach ($defaults as $comp) {
            if (!empty($comp['css'])) $styles[] = $comp['css'];
            if (!empty($comp['js'])) $scripts[] = $comp['js'];
        }
        return ['styles' => $styles, 'scripts' => $scripts];
    }

    /**
     * Add custom resources for a component.
     * @param array $resources Array with keys 'js' and/or 'css' and their paths
     * @param array $existingResources Existing resources array to merge into
     * @return array The merged resources array
     */
    public static function addCustomResources(array $resources, array $existingResources = []): array
    {
        foreach (['js', 'css'] as $type) {
            if (isset($resources[$type])) {
                if (!isset($existingResources[$type])) {
                    $existingResources[$type] = [];
                }
                if (is_array($resources[$type])) {
                    $existingResources[$type] = array_merge($existingResources[$type], $resources[$type]);
                } else {
                    $existingResources[$type][] = $resources[$type];
                }
            }
        }
        return $existingResources;
    }
}
