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
        if ( Settings::get( 'show_search', true ) ) {
            $parts[] = '<form class="wikipress-search" method="get" action="' . esc_url( home_url( '/' ) ) . '"><label class="screen-reader-text" for="wikipress-search-input">' . esc_html__( 'Search WikiPress', 'wikipress' ) . '</label><input id="wikipress-search-input" name="s" type="search" placeholder="' . esc_attr__( 'Search this site', 'wikipress' ) . '"><button type="submit">' . esc_html__( 'Search', 'wikipress' ) . '</button></form>';
        }
        $parts[] = '<div class="wikipress-content">' . $content . '</div>';
        $parts[] = '</article>';
        return implode( '', $parts );
    }
}
