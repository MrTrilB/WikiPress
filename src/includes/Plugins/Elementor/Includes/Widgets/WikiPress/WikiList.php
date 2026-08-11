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

class WikiList extends Widgets {

    public const SLUG = 'wikipress_wiki_list';

    protected function get_default_title(): string {
        return __( 'Wiki List', 'wikipress' );
    }

    protected function get_default_icon(): string {
        return 'eicon-post-list';
    }

    protected function get_default_category(): string {
        return 'wikipress-wiki';
    }

    /**
     * @return array<int, string>
     */
    protected function get_default_keywords(): array {
        return [ 'wiki', 'knowledge base', 'list', 'kb' ];
    }

    protected function register_controls(): void {
        $this->start_controls_section( 'content_section', [ 'label' => __( 'Content', 'wikipress' ) ] );

        $this->add_control(
            'category',
            [
                'label'       => __( 'Categories (slugs, comma-separated)', 'wikipress' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'tag',
            [
                'label'       => __( 'Tags (slugs, comma-separated)', 'wikipress' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'per_page',
            [
                'label'   => __( 'Per Page', 'wikipress' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 10,
                'min'     => 1,
                'max'     => 50,
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label'   => __( 'Order By', 'wikipress' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date'  => __( 'Date', 'wikipress' ),
                    'title' => __( 'Title', 'wikipress' ),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label'   => __( 'Order', 'wikipress' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'DESC' => 'DESC',
                    'ASC'  => 'ASC',
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function render(): void {
        $settings = (array) $this->get_settings_for_display();
        $context  = $this->prepare_render_context( $settings );

        /**
         * Filter the Wiki List widget render context.
         *
         * @param array<string, mixed> $context  Prepared context array.
         * @param array<string, mixed> $settings Widget settings.
         * @param self                 $widget   Widget instance.
         */
        $context = \apply_filters( 'trilbdev/elementor/widgets/wiki_list/context', $context, $settings, $this );

        Templates::render(
            'widgets/wiki/list',
            [
                'widget'   => $this,
                'settings' => $settings,
                'context'  => $context,
            ]
        );
    }

    /**
     * Prepare the render context for the Wiki list.
     *
     * @param array<string, mixed> $settings Widget settings.
     *
     * @return array<string, mixed>
     */
    private function prepare_render_context( array $settings ): array {
        $query_args = $this->build_query_args( $settings );
        $query      = QueryHelper::posts( $query_args );

        $posts = [];

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();

                $posts[] = [
                    'id'        => PostHelper::id(),
                    'title'     => get_the_title() ?: '',
                    'permalink' => get_permalink(),
                    'excerpt'   => get_the_excerpt(),
                ];
            }
        }

        $pagination = $this->build_pagination( $query );

        \wp_reset_postdata();

        $has_posts = ! empty( $posts );

        return [
            'posts'               => $posts,
            'has_posts'           => $has_posts,
            'pagination'          => $pagination,
            'no_results_message'  => __( 'No wiki posts found.', 'wikipress' ),
            'wrapper_classes'     => [ 'wikipress-wiki-list-widget' ],
            'query_args'          => $query_args,
        ];
    }

    /**
     * Build the WP_Query arguments for the Wiki list.
     *
     * @param array<string, mixed> $settings Widget settings.
     *
     * @return array<string, mixed>
     */
    private function build_query_args( array $settings ): array {
        $per_page = $this->sanitize_int_range( $settings['per_page'] ?? 10, 1, 50, 10 );

        $orderby = isset( $settings['orderby'] ) ? sanitize_key( (string) $settings['orderby'] ) : 'date';
        if ( ! in_array( $orderby, [ 'date', 'title', 'views' ], true ) ) {
            $orderby = 'date';
        }

        $order = isset( $settings['order'] ) ? strtoupper( sanitize_key( (string) $settings['order'] ) ) : 'DESC';
        if ( ! in_array( $order, [ 'ASC', 'DESC' ], true ) ) {
            $order = 'DESC';
        }

        $tax_query = [];
        $categories = $this->parse_slugs( $settings['category'] ?? '' );
        if ( ! empty( $categories ) ) {
            $tax_query[] = [
                'taxonomy' => 'doc_categories',
                'field'    => 'slug',
                'terms'    => $categories,
            ];
        }

        $tags = $this->parse_slugs( $settings['tag'] ?? '' );
        if ( ! empty( $tags ) ) {
            $tax_query[] = [
                'taxonomy' => 'doc_tags',
                'field'    => 'slug',
                'terms'    => $tags,
            ];
        }

        $paged = $this->resolve_paged();

        $args = [
            'post_type'      => 'wiki',
            'posts_per_page' => $per_page,
            'orderby'        => $orderby,
            'order'          => $order,
            'tax_query'      => $tax_query,
            'paged'          => $paged,
        ];

        if ( 'views' === $orderby ) {
            $args['meta_key']                     = '_wikipress_docs_views';
            $args['orderby']                      = 'meta_value_num';
            $args['wikipress_docs_orderby_views']  = true;
        }

        return $args;
    }

    /**
     * Build pagination markup for the provided query.
     */
    private function build_pagination( WP_Query $query ): string {
        if ( $query->max_num_pages <= 1 ) {
            return '';
        }

        $big = 999999999;

        return (string) \paginate_links( [
            'base'      => str_replace( $big, '%#%', \esc_url( \get_pagenum_link( $big ) ) ),
            'format'    => '?paged=%#%',
            'current'   => max( 1, $this->resolve_paged() ),
            'total'     => max( 1, (int) $query->max_num_pages ),
            'type'      => 'plain',
        ] );
    }

    /**
     * Parse comma-separated slugs into a sanitized array.
     *
     * @param string $slugs Raw slug string.
     *
     * @return array<int, string>
     */
    private function parse_slugs( $slugs ): array {
        if ( empty( $slugs ) ) {
            return [];
        }

        $parts = array_map( 'trim', explode( ',', (string) $slugs ) );
        $parts = array_filter( $parts, static function ( $part ) {
            return '' !== $part;
        } );

        return array_map( 'sanitize_title', $parts );
    }

    /**
     * Determine the current pagination number.
     */
    private function resolve_paged(): int {
        $paged = max( 0, (int) \get_query_var( 'paged' ) );
        $page  = max( 0, (int) \get_query_var( 'page' ) );

        $current = max( $paged, $page );

        return $current > 0 ? $current : 1;
    }

    /**
     * Sanitize an integer within a range with fallback.
     *
     * @param mixed $value    Raw value.
     * @param int   $min      Minimum inclusive value.
     * @param int   $max      Maximum inclusive value.
     * @param int   $fallback Fallback value when invalid.
     */
    private function sanitize_int_range( $value, int $min, int $max, int $fallback ): int {
        if ( null === $value ) {
            return $fallback;
        }

        $int = (int) $value;

        if ( $int < $min || $int > $max ) {
            return $fallback;
        }

        return $int;
    }
}
