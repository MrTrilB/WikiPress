<?php

namespace TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Widgets\WikiPress;

use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Templates\Templates;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Widgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WikiSearchModal extends Widgets {

    public const SLUG = 'trilbdev_wiki_search_modal';

    protected function get_default_title(): string {
        return __( 'Wiki Search Modal', 'wikipress' );
    }

    protected function get_default_icon(): string {
        return 'eicon-search';
    }

    protected function get_default_category(): string {
        return 'trilbdev-wiki';
    }

    /**
     * @return array<int, string>
     */
    protected function get_default_keywords(): array {
        return [ 'wiki', 'knowledge base', 'search', 'modal' ];
    }

    protected function register_controls(): void {
        // No controls required.
    }

    public function render(): void {
        $settings = (array) $this->get_settings_for_display();
        $context  = $this->prepare_render_context();

        /**
         * Filter the Wiki Search Modal widget render context.
         *
         * @param array<string, mixed> $context  Prepared context array.
         * @param array<string, mixed> $settings Widget settings.
         * @param self                 $widget   Widget instance.
         */
        $context = \apply_filters( 'trilbdev/elementor/widgets/wiki_search_modal/context', $context, $settings, $this );

        Templates::render(
            'widgets/wiki/search-modal',
            [
                'widget'   => $this,
                'settings' => $settings,
                'context'  => $context,
            ]
        );
    }

    /**
     * Prepare modal context.
     *
     * @return array<string, mixed>
     */
    private function prepare_render_context(): array {
        $action_url = \home_url( '/' );

        return [
            'wrapper_classes'     => [ 'trilbdev-wiki-searchmodal' ],
            'action_url'          => $action_url,
            'open_button_label'   => __( 'Search Wiki', 'wikipress' ),
            'search_placeholder'  => __( 'Search wiki...', 'wikipress' ),
            'submit_label'        => __( 'Search', 'wikipress' ),
            'close_label'         => '×',
        ];
    }
}
