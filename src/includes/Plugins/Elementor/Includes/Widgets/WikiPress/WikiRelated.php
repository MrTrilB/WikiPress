<?php

namespace TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Widgets\WikiPress;

use Elementor\Controls_Manager;
use TrilBDev\WikiPress\Includes\Functions\Helpers\PostHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\QueryHelper;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Templates\Templates;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Widgets\Widgets;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WikiRelated extends Widgets {

    public const SLUG = 'wikipress_wiki_related';

    protected function get_default_title(): string {
        return __( 'Wiki Related', 'wikipress' );
    }

    protected function get_default_icon(): string {
        return 'eicon-posts-grid';
    }

    protected function get_default_category(): string {
        return 'wikipress-wiki';
    }

    /**
     * @return array<int, string>
     */
    protected function get_default_keywords(): array {
        return [ 'wiki', 'knowledge base', 'related', 'posts' ];
    }

    protected function register_controls(): void {
        $this->start_controls_section( 'content_section', [ 'label' => __( 'Content', 'wikipress' ) ] );

        $this->add_control(
            'limit',
            [
                'label'   => __( 'Limit', 'wikipress' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 5,
                'min'     => 1,
                'max'     => 12,
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

        if ( empty( $context['has_posts'] ) ) {
            return;
        }

        /**
         * Filter the Docs Related widget render context.
         *
         * @param array<string, mixed> $context  Prepared context array.
         * @param array<string, mixed> $settings Widget settings.
         * @param self                 $widget   Widget instance.
         */
        $context = \apply_filters( 'wikipress/elementor/widgets/wiki_related/context', $context, $settings, $this );

        Templates::render(
            'widgets/wiki/related',
            [
                'widget'   => $this,
                'settings' => $settings,
                'context'  => $context,
            ]
        );
    }

    /**
     * Prepare context for the related docs listing.
     *
     * @param array<string, mixed> $settings Widget settings.
     *
     * @return array<string, mixed>
     */
    private function prepare_render_context( array $settings ): array {
        $limit = $this->resolve_limit( $settings );

        if ( $limit <= 0 ) {
            return [ 'has_posts' => false ];
        }

        $post_id = PostHelper::current_id();

        if ( ! $post_id ) {
            return [ 'has_posts' => false ];
        }

        $tax_query = [];
        $terms     = \wp_get_post_terms( $post_id, 'wiki_categories', [ 'fields' => 'ids' ] );

        if ( ! \is_wp_error( $terms ) && ! empty( $terms ) ) {
            $tax_query[] = [
                'taxonomy' => 'wiki_categories',
                'field'    => 'term_id',
                'terms'    => $terms,
            ];
        }

        $query = QueryHelper::posts( [
            'post_type'      => 'wiki',
            'post__not_in'   => [ $post_id ],
            'posts_per_page' => $limit,
            'tax_query'      => $tax_query,
            'meta_key'       => '_wikipress_docs_views',
            'orderby'        => [ 'meta_value_num' => 'DESC', 'date' => 'DESC' ],
        ] );

        if ( ! $query->have_posts() ) {
            return [ 'has_posts' => false ];
        }

        $posts = [];

        while ( $query->have_posts() ) {
            $query->the_post();

            $views = (int) \get_post_meta( PostHelper::id(), '_wikipress_docs_views', true );

            $posts[] = [
                'id'        => PostHelper::id(),
                'title'     => get_the_title() ?: '',
                'permalink' => get_permalink(),
                'views'     => $views,
            ];
        }

        \wp_reset_postdata();

        return [
            'has_posts'       => ! empty( $posts ),
            'posts'           => $posts,
            'heading'         => __( 'Related Wiki Posts', 'wikipress' ),
            'wrapper_classes' => [ 'wikipress-related-wiki' ],
        ];
    }

    /**
     * Resolve the number of related posts to display.
     *
     * @param array<string, mixed> $settings Widget settings.
     */
    private function resolve_limit( array $settings ): int {
        $options       = \get_option( 'wikipress_wiki_settings', [] );
        if ( empty( $options ) ) {
            $options = \get_option( 'wikipress_docs_settings', [] );
        }
        $option_limit  = 0;

        if ( is_array( $options ) && isset( $options['related_limit'] ) ) {
            $option_limit = (int) $options['related_limit'];
        }

        $widget_limit = isset( $settings['limit'] ) ? (int) $settings['limit'] : null;

        $limit = null !== $widget_limit ? $widget_limit : $option_limit;

        if ( null === $limit ) {
            $limit = 5;
        }

        $limit = max( 0, min( 12, (int) $limit ) );

        return $limit;
    }
}
