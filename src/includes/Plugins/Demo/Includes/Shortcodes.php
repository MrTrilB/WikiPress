<?php

namespace TrilBDev\WikiPress\Includes\Plugins\Demo\Includes;

use TrilBDev\WikiPress\Includes\Functions\Helpers\ShortcodeHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shortcodes supplied by the Demo WikiPress plugin.
 */
final class Shortcodes {
    /**
     * Return this plugin's shortcode definitions.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function definitions(): array {
        return [
            ShortcodeHelper::define(
                'wikipress_demo',
                [ self::class, 'render_demo' ],
                [ 'message' => 'WikiPress Demo' ],
                [
                    'description' => 'Render a message from the WikiPress Demo plugin.',
                    'category' => 'demo',
                ]
            ),
        ];
    }

    /**
     * Render the Demo shortcode.
     *
     * @param array<string, mixed> $atts Shortcode attributes.
     * @param string|null $content Enclosed content.
     * @param string $tag Shortcode tag.
     */
    public static function render_demo( array $atts = [], ?string $content = null, string $tag = '' ): string {
        return esc_html( (string) $atts['message'] );
    }
}