<?php

namespace TrilBDev\WikiPress\Admin\Manager;

use TrilBDev\WikiPress\Includes\Analytics\Analytics;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class AnalyticsManager extends PageManager {
    public function render(): void {
        $this->header( __( 'Analytics', 'wikipress' ) );
        $this->card( __( 'Total Wiki Page Views', 'wikipress' ), Analytics::total_views(), 'wikipress-pages' );
        echo '<h2 class="h4 mt-4">' . esc_html__( 'Most Viewed Wiki Pages', 'wikipress' ) . '</h2><div class="table-responsive"><table class="table table-striped table-hover align-middle"><thead><tr><th>' . esc_html__( 'Page', 'wikipress' ) . '</th><th>' . esc_html__( 'Views', 'wikipress' ) . '</th></tr></thead><tbody>';
        foreach ( Analytics::top_pages() as $page ) {
            printf( '<tr><td><a href="%s">%s</a></td><td>%d</td></tr>', esc_url( $page['link'] ), esc_html( $page['title'] ), absint( $page['views'] ) );
        }
        echo '</tbody></table></div>';
        $this->footer();
    }
}
