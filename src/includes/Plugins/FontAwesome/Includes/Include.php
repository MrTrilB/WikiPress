<?php
/**
 * Font Awesome Plugin for WikiPress
 * 
 * 
 * 
 * @package    Wikipress
 * @subpackage Wikipress/includes
 */
namespace TrilBDev\WikiPress\Includes\Plugins\FontAwesome;
use TrilBDev\WikiPress\Includes\Includes as BaseIncludes; 
use TrilBDev\WikiPress\Includes\Plugins\Demo\Assets\Assets;

class Includes Extends BaseIncludes {

    /**
     * Load the Font Awesome plugin.
     *
     * @since 1.0.0
     */
    public function load() {
        // Enqueue Font Awesome CSS
        add_action('wp_enqueue_scripts', [$this, 'enqueue_font_awesome']);
    }

    /**
     * Enqueue Font Awesome CSS.
     *
     * @since 1.0.0
     */
    public function enqueue_font_awesome() {
        wp_enqueue_style('font-awesome', '');
    }
}