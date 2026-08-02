<?php
/**
 * @package    Wikipress
 * @subpackage Wikipress/includes
 * @since      1.0.0
 * @author     MrTrilB <
 */
namespace TrilBDev\WikiPress\Includes\Plugins\InternalWiki\Internal;

use TrilBDev\WikiPress\Internal\Internal as BaseInternal;
class Internal extends BaseInternal {

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    private $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of the plugin.
     */
    private $version;

}