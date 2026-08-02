<?php

namespace MrTrilB\TrilBDevPlugin\Includes\FontAwesome;
use MrTrilB\TrilBDevPlugin\Includes\Functions\utilities;
use Throwable;

// Import FontAwesome functions
use function FortAwesome\fa;
use FortAwesome\FontAwesome as FAFontAwesome;

/**
 * FontAwesome integration class for TrilB.Dev plugin.
 *
 * Handles FontAwesome WordPress plugin integration and configuration.
 */
class FontAwesome {

    /**
     * FontAwesome instance
     *
     * @var FontAwesome
     */
    private static $instance;

    /**
     * IconPicker instance
     *
     * @var IconPicker
     */
    private $icon_picker;

    /**
     * Get singleton instance.
     *
     * @return FontAwesome
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Initialize FontAwesome integration.
     */
    private function init() {
        // Check if the official FontAwesome plugin is available
        if (!$this->is_fontawesome_available()) {
            utilities::write_log('TrilB.Dev: FontAwesome plugin not available');
            return;
        }

        // Initialize IconPicker
        $this->initialize_icon_picker();

        utilities::write_log( 'TrilB.Dev: FontAwesome integration initialized' );
    }

    /**
     * Initialize IconPicker functionality.
     */
    private function initialize_icon_picker() {
        if ( class_exists( '\MrTrilB\TrilBDevPlugin\Includes\FontAwesome\IconPicker' ) ) {
            $this->icon_picker = \MrTrilB\TrilBDevPlugin\Includes\FontAwesome\IconPicker::get_instance();
            utilities::write_log( 'TrilB.Dev: IconPicker initialized' );
        } else {
            utilities::write_log( 'TrilB.Dev: IconPicker class not found' );
        }
    }

    /**
     * Check if FontAwesome is available and initialized.
     *
     * @return bool
     */
    public function is_fontawesome_available() {
        // Check if the fa function exists (primary check)
        if (!function_exists('fa')) {
            // Try FontAwesome::instance() directly
            if (!class_exists('FortAwesome\\FontAwesome')) {
                return false;
            }
            
            try {
                $fa = FAFontAwesome::instance();
                $fa->version(); // Test if it's working
                return true;
            } catch (Throwable $e) {
                utilities::write_log('TrilB.Dev FontAwesome direct class check error: ' . $e->getMessage());
                return false;
            }
        }

        try {
            // Try to get the FontAwesome instance
            $fa = fa();
            $fa->version(); // Test if it's working
            return true;
        } catch (Throwable $e) {
            utilities::write_log('TrilB.Dev FontAwesome check error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the IconPicker instance.
     *
     * @return IconPicker|null
     */
    public function get_icon_picker() {
        return $this->icon_picker;
    }
}
