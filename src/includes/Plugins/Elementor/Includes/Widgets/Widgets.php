<?php
/**
 * 
 * WikiPress Elementor Widget Integration Class
 * 
 * A Class that handles the integration of the WikiPress plugin with Elementor Widgets.
 * 
 * @package    Wikipress
 * @subpackage Wikipress/includes
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Widgets;

use Elementor\Widget_Base;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Settings\Settings;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Widgets\WikiPress\WikiBreadcrumbs;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Widgets\WikiPress\WikiList;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Widgets\WikiPress\WikiReadingTime;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Widgets\WikiPress\WikiRelated;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Widgets\WikiPress\WikiTOC;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Widgets\WikiPress\WikiSearchModal;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Widgets extends Widget_Base {
    abstract protected function get_default_title(): string;
    abstract protected function get_default_icon(): string;
    abstract protected function get_default_category(): string;

    protected function get_default_keywords(): array {
        return [ 'wiki', 'knowledge base' ];
    }

    public function get_name(): string {
        return (string) constant( static::class . '::SLUG' );
    }

    public function get_title(): string {
        return $this->get_default_title();
    }

    public function get_icon(): string {
        return $this->get_default_icon();
    }

    public function get_categories(): array {
        return [ $this->get_default_category() ];
    }

    public function get_keywords(): array {
        return $this->get_default_keywords();
    }

    public static function register( $manager ): void {
        $classes = [
            WikiBreadcrumbs::class,
            WikiList::class,
            WikiReadingTime::class,
            WikiRelated::class,
            WikiTOC::class,
            WikiSearchModal::class,
        ];

        foreach ( $classes as $class ) {
            $slug = (string) constant( $class . '::SLUG' );
            $setting_slug = str_replace( 'wikipress_', '', $slug );
            if ( Settings::widget_enabled( $setting_slug ) ) {
                $manager->register( new $class() );
            }
        }
    }
}