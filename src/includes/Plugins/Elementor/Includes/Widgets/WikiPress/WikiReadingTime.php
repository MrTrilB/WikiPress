<?php

namespace WikiPress\Includes\Plugins\Elementor\Includes\Widgets\WikiPress;

use Elementor\Controls_Manager;
use WikiPress\Includes\Functions\Helpers\PostHelper;
use WikiPress\Includes\Plugins\Elementor\Includes\Templates\Templates;
use WikiPress\Includes\Plugins\Elementor\Includes\Widgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WikiReadingTime extends Widgets {

    public const SLUG = 'wikipress_wiki_reading_time';

    protected function get_default_title(): string {
        return __( 'Wiki Reading Time', 'wikipress' );
    }

    protected function get_default_icon(): string {
        return 'eicon-time-line';
    }

    protected function get_default_category(): string {
        return 'wikipress-wiki';
    }

    /**
     * @return array<int, string>
     */
    protected function get_default_keywords(): array {
        return [ 'wiki', 'knowledge base', 'reading time', 'duration' ];
    }

    protected function register_controls(): void {
        $this->start_controls_section( 'content_section', [ 'label' => __( 'Content', 'wikipress' ) ] );

        $this->add_control(
            'wpm',
            [
                'label'   => __( 'Words Per Minute', 'wikipress' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 200,
                'min'     => 60,
                'max'     => 600,
            ]
        );

        $this->end_controls_section();
    }

    public function render(): void {
        $settings = (array) $this->get_settings_for_display();
        $context  = $this->prepare_render_context( $settings );

        if ( empty( $context['has_content'] ) ) {
            return;
        }

        /**
         * Filter the Wiki Reading Time widget render context.
         *
         * @param array<string, mixed> $context  Prepared context array.
         * @param array<string, mixed> $settings Widget settings.
         * @param self                 $widget   Widget instance.
         */
        $context = \apply_filters( 'wikipress/elementor/widgets/wiki_reading_time/context', $context, $settings, $this );

        Templates::render(
            'widgets/wiki/reading-time',
            [
                'widget'   => $this,
                'settings' => $settings,
                'context'  => $context,
            ]
        );
    }

    /**
     * Prepare the reading time context for rendering.
     *
     * @param array<string, mixed> $settings Widget settings.
     *
     * @return array<string, mixed>
     */
    private function prepare_render_context( array $settings ): array {
        $post_id = PostHelper::current_id();

        if ( ! $post_id ) {
            return [ 'has_content' => false ];
        }

        $raw_content = \get_post_field( 'post_content', $post_id );

        if ( ! is_string( $raw_content ) || '' === trim( $raw_content ) ) {
            return [ 'has_content' => false ];
        }

        $wpm = $this->sanitize_int_range( $settings['wpm'] ?? 200, 60, 600, 200 );

            $content = \apply_filters( 'the_content', $raw_content );
        $words   = str_word_count( \wp_strip_all_tags( (string) $content ) );

        if ( $words <= 0 ) {
            return [ 'has_content' => false ];
        }

        $minutes = max( 1, (int) ceil( $words / $wpm ) );

        /* translators: %d is the estimated reading time in minutes. */
        $display_text = sprintf( __( '~%d min read', 'wikipress' ), $minutes );

        return [
            'has_content'    => true,
            'minutes'        => $minutes,
            'words'          => $words,
            'wpm'            => $wpm,
            'display_text'   => $display_text,
            'wrapper_classes'=> [ 'wikipress-wiki-reading-time' ],
        ];
    }

    /**
     * Sanitize an integer within a given range with fallback.
     *
     * @param mixed $value    Raw value.
     * @param int   $min      Minimum allowed value.
     * @param int   $max      Maximum allowed value.
     * @param int   $fallback Default when invalid.
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
