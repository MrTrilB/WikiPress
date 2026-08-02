<?php
/**
 * Settings for the Font Awesome WikiPress plugin.
 * 
 * @package    Wikipress
 * @subpackage Wikipress/includes
 */
namespace TrilBDev\WikiPress\Includes\Plugins\FontAwesome\Settings;
use TrilBDev\WikiPress\Includes\Settings as BaseSettings;

class Settings Extends BaseSettings {

    /**
     * Load the Font Awesome plugin settings.
     *
     * @since 1.0.0
     */
    public function load() {
        // Add settings for Font Awesome plugin
        add_action('admin_init', [$this, 'register_font_awesome_settings']);
    }

    /**
     * Register Font Awesome plugin settings.
     *
     * @since 1.0.0
     */
    public function register_font_awesome_settings() {
        register_setting('font_awesome_settings_group', 'font_awesome_version');
        add_settings_section('font_awesome_settings_section', 'Font Awesome Settings', null, 'font_awesome_settings_page');
        add_settings_field('font_awesome_version', 'Font Awesome Version', [$this, 'font_awesome_version_callback'], 'font_awesome_settings_page', 'font_awesome_settings_section');
    }

    /**
     * Callback for Font Awesome version field.
     *
     * @since 1.0.0
     */
    public function font_awesome_version_callback() {
        $version = get_option('font_awesome_version', '5.15.4');
        echo '<input type="text" name="font_awesome_version" value="' . esc_attr($version) . '" />';
    }
}