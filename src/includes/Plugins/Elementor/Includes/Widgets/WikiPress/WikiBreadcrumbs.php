<?php

namespace WikiPress\Includes\Plugins\Elementor\Includes\Widgets\WikiPress;

use Elementor\Controls_Manager;
use WikiPress\Includes\Functions\Helpers\PostHelper;
use WikiPress\Includes\Plugins\Elementor\Includes\Templates\Templates;
use WikiPress\Includes\Plugins\Elementor\Includes\Widgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WikiBreadcrumbs extends Widgets {

    public const SLUG = 'wikipress_wiki_breadcrumbs';

    protected function get_default_title(): string {
        return __( 'Wiki Breadcrumbs', 'wikipress' );
    }

    protected function get_default_icon(): string {
        return 'eicon-breadcrumbs';
    }

    protected function get_default_category(): string {
        return 'wikipress-wiki';
    }

    /**
     * @return array<int, string>
     */
    protected function get_default_keywords(): array {
        return [ 'wiki', 'knowledge base', 'breadcrumbs', 'navigation' ];
    }

    protected function register_controls(): void {
        $this->start_controls_section( 'content_section', [ 'label' => __( 'Content', 'wikipress' ) ] );

        $this->add_control(
            'delimiter',
            [
                'label'       => __( 'Delimiter', 'wikipress' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => ' / ',
                'label_block' => true,
            ]
        );

        $this->end_controls_section();
    }

    public function render(): void {
        if ( ! \is_singular( 'wiki' ) ) {
            return;
        }

        $settings = (array) $this->get_settings_for_display();
        $context  = $this->prepare_render_context( $settings );

        if ( empty( $context['crumbs'] ) ) {
            return;
        }

        /**
         * Filter the Wiki Breadcrumbs widget render context.
         *
         * @param array<string, mixed> $context  Prepared context array.
         * @param array<string, mixed> $settings Widget settings.
         * @param self                 $widget   Widget instance.
         */
        $context = \apply_filters( 'wikipress/elementor/widgets/wiki_breadcrumbs/context', $context, $settings, $this );

        Templates::render(
            'widgets/wiki/breadcrumbs',
            [
                'widget'   => $this,
                'settings' => $settings,
                'context'  => $context,
            ]
        );
    }

    /**
     * Prepare breadcrumb context for rendering.
     *
     * @param array<string, mixed> $settings Widget settings.
     *
     * @return array<string, mixed>
     */
    private function prepare_render_context( array $settings ): array {
        $crumbs = $this->build_crumbs();

        return [
            'crumbs'            => $crumbs,
            'delimiter'         => $this->resolve_delimiter( $settings ),
            'wrapper_classes'   => [ 'wikipress-wiki-breadcrumbs' ],
            'nav_aria_label'    => __( 'Breadcrumbs', 'wikipress' ),
            'has_crumbs'        => ! empty( $crumbs ),
        ];
    }

    /**
     * Resolve the delimiter to use between breadcrumb entries.
     *
     * @param array<string, mixed> $settings Widget settings.
     */
    private function resolve_delimiter( array $settings ): string {
        $option_delimiter = '';

        $options = \get_option( 'wikipress_wiki_settings', [] );
        if ( empty( $options ) ) {
            $options = \get_option( 'wikipress_docs_settings', [] );
        }
        if ( is_array( $options ) && isset( $options['breadcrumb_delim'] ) ) {
            $option_delimiter = (string) $options['breadcrumb_delim'];
        }

        $widget_delimiter = isset( $settings['delimiter'] ) ? (string) $settings['delimiter'] : '';

        $delimiter = '' !== $widget_delimiter ? $widget_delimiter : ( '' !== $option_delimiter ? $option_delimiter : ' / ' );

        return trim( \wp_strip_all_tags( $delimiter ) );
    }

    /**
     * Build the breadcrumb collection for the current Wiki entry.
     *
     * @return array<int, array<string, mixed>>
     */
    private function build_crumbs(): array {
        $post_id = PostHelper::current_id();

        if ( ! $post_id ) {
            return [];
        }

        $crumbs = [];

        $crumbs[] = $this->create_crumb( \esc_html__( 'Home', 'wikipress' ), \home_url( '/' ) );

        $archive_link = \get_post_type_archive_link( 'wiki' );
        if ( $archive_link ) {
            $crumbs[] = $this->create_crumb( \esc_html__( 'Wiki', 'wikipress' ), $archive_link );
        }

        $terms = \wp_get_post_terms( $post_id, 'doc_categories', [ 'orderby' => 'parent', 'order' => 'ASC' ] );

        if ( ! \is_wp_error( $terms ) && ! empty( $terms ) ) {
            $primary   = $terms[0];
            $ancestors = array_reverse( \get_ancestors( $primary->term_id, 'doc_categories' ) );

            foreach ( $ancestors as $ancestor_id ) {
                $ancestor = \get_term( $ancestor_id, 'doc_categories' );

                if ( ! $ancestor || \is_wp_error( $ancestor ) ) {
                    continue;
                }

                $link = \get_term_link( $ancestor );

                if ( \is_wp_error( $link ) ) {
                    continue;
                }

                $crumbs[] = $this->create_crumb( $ancestor->name, $link );
            }

            $primary_link = \get_term_link( $primary );

            if ( ! \is_wp_error( $primary_link ) ) {
                $crumbs[] = $this->create_crumb( $primary->name, $primary_link );
            }
        }

        $crumbs[] = $this->create_crumb( \get_the_title( $post_id ), null, true );

        return array_values( array_filter( $crumbs, static function ( $crumb ) {
            return is_array( $crumb ) && ! empty( $crumb['label'] );
        } ) );
    }

    /**
     * Create a sanitized breadcrumb entry.
     *
     * @param string      $label      Breadcrumb label.
     * @param string|null $url        Optional URL.
     * @param bool        $is_current Whether this is the current item.
     *
     * @return array<string, mixed>
     */
    private function create_crumb( string $label, ?string $url = null, bool $is_current = false ): array {
        $label = \wp_strip_all_tags( $label );

        return [
            'label'      => $label,
            'url'        => $url ? (string) $url : null,
            'is_current' => $is_current,
        ];
    }
}
