<?php
/**
 * ExportManager class for WikiPress plugin.
 * 
 * @package WikiPress
 * @subpackage Admin\Manager\Tools
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Admin\Manager\Tools;

use TrilBDev\WikiPress\Admin\Manager\Manager;
use TrilBDev\WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\UrlHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class ExportManager extends Manager {
    /**
     * Render the JSON export form below the tools settings form.
     *
     * @return void
     */
    public function __construct() {
    }
    /**
     * Render the JSON export form below the tools settings form.
     *
     * @return void
     */
    public function render_page_content(): void {
        echo '<div class="card shadow-sm"><div class="card-body"><h2 class="h5">' . esc_html__( 'Export WikiPress data', 'wikipress' ) . '</h2><p class="text-secondary">' . esc_html__( 'Download your WikiPress content and settings as a JSON file.', 'wikipress' ) . '</p>';
        echo wp_kses_post( FormFieldHelper::button( esc_html__( 'Export WikiPress JSON', 'wikipress' ), [ 'href' => UrlHelper::admin_action_nonce( 'wikipress_export', 'wikipress_export' ), 'class' => 'btn-outline-primary' ] ) );
        echo '</div></div>';
    }

    /**
     * Render export and database tool fields.
     *
     * @return void
     */
    public function render(): void {
        echo '<tr><th scope="row">' . wp_kses_post( FormFieldHelper::label( 'wikipress-export', esc_html__( 'Import and export', 'wikipress' ), [ 'description' => __( 'Export or import WikiPress content and settings as JSON.', 'wikipress' ), 'tooltip' => __( 'Exports are protected with a WordPress nonce.', 'wikipress' ) ] ) ) . '</th><td>' . wp_kses_post( FormFieldHelper::button( esc_html__( 'Export WikiPress JSON', 'wikipress' ), [ 'href' => UrlHelper::admin_action_nonce( 'wikipress_export', 'wikipress_export' ), 'class' => 'btn-outline-primary' ] ) ) . '</td></tr>';
        echo '<tr><th scope="row">' . wp_kses_post( FormFieldHelper::label( 'wikipress-database-manager', esc_html__( 'Database manager', 'wikipress' ), [ 'description' => __( 'The settings table is managed automatically during plugin activation.', 'wikipress' ), 'tooltip' => __( 'Manual database changes are not required for normal WikiPress operation.', 'wikipress' ) ] ) ) . '</th><td>' . esc_html__( 'Managed automatically', 'wikipress' ) . '</td></tr>';
    }
}