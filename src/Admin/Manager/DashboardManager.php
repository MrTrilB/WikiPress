<?php

namespace TrilBDev\WikiPress\Admin\Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class DashboardManager extends PageManager {
    public function render(): void {
        $this->header( __( 'Dashboard', 'wikipress' ) );
        echo '<div class="row g-4">';
        $this->card( __( 'Wikis', 'wikipress' ), wp_count_posts( 'wikipress_wiki' )->publish ?? 0, 'wikipress-wikis' );
        $this->card( __( 'Wiki Pages', 'wikipress' ), wp_count_posts( 'wikipress_page' )->publish ?? 0, 'wikipress-pages' );
        $this->card( __( 'Categories', 'wikipress' ), wp_count_terms( 'wikipress_category' ), 'wikipress-categories' );
        $this->card( __( 'Tags', 'wikipress' ), wp_count_terms( 'wikipress_tag' ), 'wikipress-tags' );
        echo '</div>';
        $this->footer();
    }
}
