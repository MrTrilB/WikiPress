<?php
/**
 * ToolsManager class for WikiPress plugin.
 *
 * @package WikiPress
 * @subpackage Admin\Manager\Tools
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Admin\Manager\Tools;

use TrilBDev\WikiPress\Admin\Manager\Manager;
use TrilBDev\WikiPress\Admin\Manager\Tools\DebugManager;
use TrilBDev\WikiPress\Admin\Manager\Tools\ExportManager;
use TrilBDev\WikiPress\Admin\Manager\Tools\ImportManager;
use TrilBDev\WikiPress\Admin\Manager\Tools\AnalyticsManager;
use TrilBDev\WikiPress\Assets\Assets;


final class ToolsManager extends Manager {
    /**
     * The Page variable.
     *
     * @since 1.0.0
     * @access protected
     * @var string $page The page variable.
     */
    protected $page;
    /**
     * `Constructor` method for the `ToolsManager` class.
     *
     * @since 1.0.0
     * @return void
     */
    private DebugManager $debug_manager;
    /**
     * ExportManager instance for managing export-related admin pages.
     *
     * @var ExportManager
     */
    private ExportManager $export_manager;
    /**
     * ImportManager instance for managing import-related admin pages.
     *
     * @var ImportManager
     */
    private ImportManager $import_manager;
    /**
     * AnalyticsManager instance for managing analytics-related admin pages.
     *
     * @var AnalyticsManager
     */
    private AnalyticsManager $analytics_manager;

    /**
     * `Constructor` method for the `ToolsManager` class.
     *
     * @since 1.0.0
     * @return void
     */
    public function __construct() {
        /**
         * Set the page variable to 'tools'.
         *
         * @since 1.0.0
         */
        $this->page = 'tools';
        /**
         * Initialize the Debug Manager page.
         *
         * @since 1.0.0
         */
        $this->debug_manager = new DebugManager();
        /**
         * Initialize the Export Manager page.
         *
         * @since 1.0.0
         */
        $this->export_manager = new ExportManager();
        /**
         * Initialize the Import Manager page.
         *
         * @since 1.0.0
         */
        $this->import_manager = new ImportManager();
        /**
         * Initialize the Analytics Manager page.
         *
         * @since 1.0.0
         */
        $this->analytics_manager = new AnalyticsManager();
    }
    /**
     * Renders the tools page.
     *
     * @since 1.0.0
     * @return void
     */
    public function render(): void {
        $tool = sanitize_key( $_GET['tool'] ?? 'debug' );
        $tool = in_array( $tool, [ 'analytics', 'debug', 'import', 'export' ], true ) ? $tool : 'debug';
        $this->header( $this->title( $tool ) );
        if ( 'analytics' === $tool ) {
            $this->analytics_manager->render_content();
        } elseif ( 'debug' === $tool ) {
            $this->debug_manager->render_page_content();
        } elseif ( 'import' === $tool ) {
            $this->import_manager->render();
        } else {
            $this->export_manager->render_page_content();
        }
        $this->footer();
    }
    /**
     * Registers the assets for the tools page.
     *
     * @since 1.0.0
     * @param Assets $assets The Assets instance.
     * @return void
     */
    public function register_assets( Assets $assets ): void {
        $this->register_page_assets( $assets, [ 'wikipress-tools' ], 'analytics' );
    }
    /**
     * Returns the title for the given tool.
     *
     * @since 1.0.0
     * @param string $tool The tool name.
     * @return string The title for the tool.
     */
    private function title( string $tool ): string {
        return [
            'analytics' => __( 'Analytics', 'wikipress' ),
            'debug' => __( 'Debug', 'wikipress' ),
            'import' => __( 'Import', 'wikipress' ),
            'export' => __( 'Export', 'wikipress' ),
        ][ $tool ];
    }
}