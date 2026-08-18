<?php
/**
 * Register WikiPress Gutenberg blocks.
 *
 * @package WikiPress
 */
namespace WikiPress\Includes\Plugins\Gutenburg\Includes;

use WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks\WikiBreadcrumbs;
use WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks\WikiList;
use WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks\WikiReadingTime;
use WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks\WikiRelated;
use WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks\WikiSearchModal;
use WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks\WikiTOC;
use WikiPress\Includes\Plugins\Gutenburg\Includes\Settings\Settings;

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
