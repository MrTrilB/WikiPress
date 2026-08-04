<?php
/**
 * Higher-level helpers for the WikiPress WordPress hook loader.
 *
 * @package TrilBDev\WikiPress\Includes\Functions\Helpers
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Includes\Functions\Helpers;

use TrilBDev\WikiPress\Includes\Core\WP\WPLoader;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Extend the core loader with component hook registration helpers.
 */
class LoaderHelper extends WPLoader {
    /**
     * Register multiple hooks belonging to one component.
     *
     * Each definition requires `type`, `hook`, and `callback`, and may provide
     * `priority` and `accepted_args`.
     *
     * @param object|string|array $component Callback component.
     * @param array<int, array<string, mixed>> $hooks Hook definitions.
     * @return self
     */
    public function register_component( object|string|array $component, array $hooks ): self {
        foreach ( $hooks as $definition ) {
            $type = $definition['type'] ?? 'action';
            $hook = $definition['hook'] ?? '';
            $callback = $definition['callback'] ?? '';
            $priority = (int) ( $definition['priority'] ?? 10 );
            $accepted_args = (int) ( $definition['accepted_args'] ?? 1 );

            if ( ! is_string( $hook ) || '' === $hook || ! is_string( $callback ) || '' === $callback ) {
                throw new \InvalidArgumentException( 'Hook definitions require a hook and callback string.' );
            }
            if ( 'filter' === $type ) {
                $this->add_filter( $hook, $component, $callback, $priority, $accepted_args );
                continue;
            }
            if ( 'action' === $type ) {
                $this->add_action( $hook, $component, $callback, $priority, $accepted_args );
                continue;
            }
            throw new \InvalidArgumentException( 'Hook definition type must be action or filter.' );
        }

        return $this;
    }
}
