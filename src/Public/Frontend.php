<?php

namespace TrilBDev\WikiPress\PublicArea;

use TrilBDev\WikiPress\Includes\Core\PostType;
use TrilBDev\WikiPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Frontend {
    public function filter_content( string $content ): string {
        if ( ! is_singular( PostType::PAGE ) || ! is_main_query() || ! in_the_loop() ) {
            return $content;
        }

        $parts = [ '<article class="wikipress-page">' ];
        if ( Settings::get_bool( 'show_search', true ) ) {
            $parts[] = $this->render_search_form();
        }
        $parts[] = '<div class="wikipress-content">' . $content . '</div>';
        $parts[] = '</article>';

        return (string) apply_filters( 'wikipress_frontend_content', implode( '', $parts ), $content );
    }

    public function body_classes( array $classes ): array {
        if ( is_singular( PostType::PAGE ) ) {
            $classes[] = 'wikipress-page-template';
            if ( Settings::get_bool( 'show_search', true ) ) {
                $classes[] = 'wikipress-search-enabled';
            }
        }

        return array_values( array_unique( array_map( 'sanitize_html_class', $classes ) ) );
    }

    public function render_search_form(): string {
        $form = '<form class="wikipress-search" method="get" action="' . esc_url( home_url( '/' ) ) . '">';
        $form .= '<label class="screen-reader-text" for="wikipress-search-input">' . esc_html__( 'Search WikiPress', 'wikipress' ) . '</label>';
        $form .= '<input id="wikipress-search-input" name="s" type="search" placeholder="' . esc_attr__( 'Search this site', 'wikipress' ) . '">';
        $form .= '<button type="submit">' . esc_html__( 'Search', 'wikipress' ) . '</button>';
        $form .= '</form>';

        return (string) apply_filters( 'wikipress_search_form', $form );
    }
}
