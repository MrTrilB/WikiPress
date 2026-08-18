<?php
/**
 * DebugManager class for WikiPress plugin.
 * 
 * @package WikiPress
 * @subpackage Admin\Manager\Tools
 * @since 1.0.0
 */
namespace WikiPress\Admin\Manager\Tools;

use WikiPress\Admin\Manager\Manager;
use WikiPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class DebugManager extends Manager {
    /**
     * Constructor for the DebugManager class.
     *
     * @since 1.0.0
     */
    public function __construct() {
    }
    /**
     * Render the debug settings page content.
     *
     * @return void
     */
    public function render_page_content(): void {
        echo '<div class="card shadow-sm"><div class="card-body"><h2 class="h5">' . esc_html__( 'Debug logging', 'wikipress' ) . '</h2><p class="text-secondary">' . esc_html__( 'Configure diagnostic logging from the Tools settings tab.', 'wikipress' ) . '</p><a class="btn btn-primary" href="' . esc_url( admin_url( 'admin.php?page=wikipress-settings&tab=tools' ) ) . '">' . esc_html__( 'Open Tools Settings', 'wikipress' ) . '</a></div></div>';
    }

    /**
     * Render debug-related settings fields.
     *
     * @param array<string, mixed> $values Current settings.
     * @return void
     */
    public function render( array $values ): void {
        $field_id = 'wikipress-debug-logging';
        $field = [
            'description' => __( 'Write diagnostic information to the WordPress debug log.', 'wikipress' ),
            'tooltip' => __( 'Enable this only while investigating a problem, because logs can grow over time.', 'wikipress' ),
            'tooltip_type' => 'info',
        ];
        echo '<tr><th scope="row">' . wp_kses_post( FormFieldHelper::label( $field_id, __( 'Debug logging', 'wikipress' ), $field ) ) . '</th><td>' . wp_kses_post( FormFieldHelper::checkbox( 'wikipress_tools[debug_logging]', '1', __( 'Enable WikiPress debug logging', 'wikipress' ), [ 'id' => $field_id, 'checked' => ! empty( $values['debug_logging'] ) ] ) ) . '</td></tr>';

        $field_id = 'wikipress-console-logging';
        $field = [
            'description' => __( 'Write diagnostic information to the browser console.', 'wikipress' ),
            'tooltip' => __( 'Use this during frontend troubleshooting and disable it afterward.', 'wikipress' ),
        ];
        echo '<tr><th scope="row">' . wp_kses_post( FormFieldHelper::label( $field_id, __( 'Console logging', 'wikipress' ), $field ) ) . '</th><td>' . wp_kses_post( FormFieldHelper::checkbox( 'wikipress_tools[console_logging]', '1', __( 'Enable browser console logging', 'wikipress' ), [ 'id' => $field_id, 'checked' => ! empty( $values['console_logging'] ) ] ) ) . '</td></tr>';
    }
}