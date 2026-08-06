<?php

namespace TrilBDev\WikiPress\Admin\Manager\Settings;

use TrilBDev\WikiPress\Admin\Manager\Manager;
use TrilBDev\WikiPress\Assets\Assets;
use TrilBDev\WikiPress\Includes\Settings\Settings;
use TrilBDev\WikiPress\Includes\Functions\Helpers\SanitizationHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class SettingsManager extends Manager {
    private SettingsGeneral $general_page;
    private SettingsLayout $layout_page;
    private SettingsAccess $access_page;
    private SettingsTools $tools_page;
    private SettingsPlugins $plugins_page;

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
        $this->page = 'dashboard';
        $this->general_page = new SettingsGeneral();
        $this->layout_page = new SettingsLayout();
        $this->access_page = new SettingsAccess();
        $this->tools_page = new SettingsTools();
        $this->plugins_page = new SettingsPlugins();
    }
    /**
     * Renders the settings page.
     *
     * @since 1.0.0
     * @return void
     */
    public function render(): void {
        $tab = SanitizationHelper::key( $_GET['tab'] ?? 'general', 'general' );
        $layout_section = SanitizationHelper::key( $_GET['layout_section'] ?? 'general', 'general' );
        $groups = Settings::get_all();
        $values = $groups[ $tab ] ?? [];
        $tab_context = [
            'general' => [ 'description' => __( 'Configure WikiPress names, URL slugs, and permalink settings.', 'wikipress' ), 'tooltip' => __( 'These settings affect how WikiPress content is identified and linked throughout the site.', 'wikipress' ) ],
            'layout' => [ 'description' => __( 'Choose which navigation and page layout features WikiPress displays.', 'wikipress' ), 'tooltip' => __( 'Layout settings control the visitor-facing WikiPress interface.', 'wikipress' ) ],
            'access' => [ 'description' => __( 'Set the minimum WordPress capabilities required for WikiPress tasks.', 'wikipress' ), 'tooltip' => __( 'Choose carefully so editors and administrators retain the access they need.', 'wikipress' ) ],
            'tools' => [ 'description' => __( 'Manage diagnostics and import or export WikiPress data.', 'wikipress' ), 'tooltip' => __( 'Use import and export tools carefully and keep debug logging disabled when it is not needed.', 'wikipress' ) ],
            'plugins' => [ 'description' => __( 'View the WikiPress plugins installed on this site.', 'wikipress' ), 'tooltip' => __( 'Plugin-specific configuration is available from each plugin settings page when provided.', 'wikipress' ) ],
            'third-party' => [ 'description' => __( 'View third-party plugins installed on this site.', 'wikipress' ), 'tooltip' => __( 'Third-party plugin settings are managed through WordPress or the plugin author’s own settings page.', 'wikipress' ) ],
        ];
        $this->header( __( 'Settings', 'wikipress' ) );
        ?>
        <?php if ( isset( $tab_context[ $tab ] ) ) : ?>
            <p class="text-secondary mb-4"><?php echo esc_html( $tab_context[ $tab ]['description'] ); ?> <?php echo FormFieldHelper::label( 'wikipress-settings-context', __( 'Settings information', 'wikipress' ), [ 'tooltip' => $tab_context[ $tab ]['tooltip'], 'tooltip_type' => 'info', 'tooltip_icon' => 'fa-circle-info', 'class' => 'visually-hidden' ] ); ?></p>
        <?php endif; ?>
        <?php if ( in_array( $tab, [ 'plugins', 'third-party' ], true ) ) : ?>
            <?php $this->plugins_page->render( $tab ); ?>
        <?php else : ?>
        <form method="post" action="options.php" class="wikipress-settings-form card shadow-sm">
            <?php settings_fields( 'wikipress_settings' ); ?>
            <div class="card-body"><table class="form-table table align-middle"><tbody>
            <?php if ( 'general' === $tab ) : $this->general_page->render( $values ); ?>
            <?php elseif ( 'layout' === $tab ) : $this->layout_page->render( $values, $layout_section ); ?>
            <?php elseif ( 'access' === $tab ) : $this->access_page->render( $values ); ?>
            <?php elseif ( $this->plugins_page->has_settings_page( $tab ) ) : $this->plugins_page->render_settings_page( $tab, $values ); ?>
            <?php elseif ( 'tools' === $tab ) : $this->tools_page->render( $values ); ?>
            <?php endif; ?>
            </tbody></table>
            <?php submit_button( __( 'Save Changes', 'wikipress' ), 'primary', 'submit', true, [ 'class' => 'btn btn-primary' ] ); ?></div>
        </form>
        <?php if ( 'tools' === $tab ) : $this->tools_page->render_import_form(); endif; ?>
        <?php endif; ?>
        <?php $this->footer();
    }

    public function register_assets( Assets $assets ): void {
        $this->register_page_assets( $assets, [ 'wikipress-settings' ], 'settings' );
    }

}
