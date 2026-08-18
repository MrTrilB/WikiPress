<?php
/**
 * AnalyticsManager class for WikiPress plugin.
 *
 * @package WikiPress
 * @subpackage Admin\Manager\Tools
 * @since 1.0.0
 */
namespace WikiPress\Admin\Manager\Tools;

use WikiPress\Admin\Manager\Manager;
use WikiPress\Assets\Assets;
use WikiPress\Includes\Analytics\Analytics;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class AnalyticsManager extends Manager {
    /**
     * The Page variable.
     *
     * @since 1.0.0
     * @access protected
     * @var string $page The page variable.
     */
    protected $page;
    /**
     * `Constructor` method for the `DashboardManager` class. 
     *
     * @since 1.0.0
     * @return void
     */

    public function __construct() {
        /**
         * Set the page variable to 'dashboard'.
         *
         * @since 1.0.0
         */
        $this->page = 'analytics';

    }
    /**
     * Renders the analytics page.
     *
     * @since 1.0.0
     * @return void
     */
    public function render_content(): void {
        echo '<div class="wikipress-analytics-summary">';
        $this->card( __( 'Total Wiki Page Views', 'wikipress' ), Analytics::total_views(), 'wikipress-manage' );
        echo '</div><h2 class="h4 mt-4">' . esc_html__( 'Most Viewed Wiki Pages', 'wikipress' ) . '</h2><div class="table-responsive"><table class="table wikipress-analytics-table table-striped table-hover align-middle"><thead><tr><th>' . esc_html__( 'Page', 'wikipress' ) . '</th><th>' . esc_html__( 'Views', 'wikipress' ) . '</th></tr></thead><tbody>';
        foreach ( Analytics::top_pages() as $page ) {
            printf( '<tr><td><a href="%s">%s</a></td><td>%d</td></tr>', esc_url( $page['link'] ), esc_html( $page['title'] ), absint( $page['views'] ) );
        }
        echo '</tbody></table></div>';
    }
}
