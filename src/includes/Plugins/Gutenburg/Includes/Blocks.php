<?php
/**
 * Register WikiPress Gutenberg blocks.
 *
 * @package TrilBDev\WikiPress
 */
namespace TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes;

use TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks\WikiBreadcrumbs;
use TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks\WikiList;
use TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks\WikiReadingTime;
use TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks\WikiRelated;
use TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks\WikiSearchModal;
use TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks\WikiTOC;
use TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Blocks {
    private const BLOCKS = [
        'wiki-breadcrumbs' => WikiBreadcrumbs::class,
        'wiki-list' => WikiList::class,
        'wiki-reading-time' => WikiReadingTime::class,
        'wiki-related' => WikiRelated::class,
        'wiki-toc' => WikiTOC::class,
        'wiki-search-modal' => WikiSearchModal::class,
    ];

    public static function register(): void {
        foreach ( self::BLOCKS as $block => $renderer ) {
            if ( ! Settings::block_enabled( $block ) ) {
                continue;
            }

            $path = __DIR__ . '/Blocks/' . $block;
            register_block_type(
                $path,
                [ 'render_callback' => [ $renderer, 'render' ] ]
            );
        }
    }
}
