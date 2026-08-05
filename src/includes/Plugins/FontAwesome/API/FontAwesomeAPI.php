<?php

namespace TrilBDev\WikiPress\Includes\Plugins\FontAwesome\API;

use TrilBDev\WikiPress\Includes\Plugins\FontAwesome\Includes\Settings\Settings;

final class FontAwesomeAPI {
    public static function configure(): void {
        if ( ! class_exists( '\\FortAwesome\\FontAwesome' ) ) {
            return;
        }

        $options = get_option( 'font-awesome', [] );
        $options = is_array( $options ) ? $options : [];
        $kit_id = self::kit_id();

        if ( Settings::source() === 'kit' && $kit_id !== '' ) {
            $options['kitToken'] = $kit_id;
            $options['apiToken'] = true;
            $options['version'] = 'latest';
        } else {
            $options['kitToken'] = null;
            $options['apiToken'] = false;
            $options['version'] = Settings::version();
        }

        $options['usePro'] = false;
        $options['compat'] = true;
        $options['technology'] = 'webfont';
        $options['pseudoElements'] = true;
        $options['dataVersion'] = 4;
        update_option( 'font-awesome', $options, false );
    }

    public static function is_available(): bool {
        return function_exists( 'FortAwesome\\fa' );
    }

    public static function instance() {
        return self::is_available() ? \FortAwesome\fa() : null;
    }

    public static function source(): string {
        return Settings::source();
    }

    public static function kit_id(): string {
        return Settings::kit_id();
    }
}