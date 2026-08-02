<?php
/**
 * TrilB.Dev Plugin - Wiki Plugin Interface
 *
 * Defines the contract for Wiki extensions that can be discovered by the
 * Wiki plugin loader or registered from a normal WordPress plugin.
 *
 * @package TrilBDev
 * @subpackage Includes\Wiki\Plugins
 * @since 1.0.0
 */

namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\Plugins;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

interface WikiPluginInterface {
    public function get_slug(): string;
    public function get_name(): string;
    public function is_active(): bool;
    public function init(): void;
}
