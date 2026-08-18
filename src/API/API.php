<?php
/**
 * REST API helper for WikiPress.
 *
 * @package WikiPress
 * @subpackage API
 * @since 1.0.0
 */
namespace WikiPress\API;

use WikiPress\Includes\Core\PostType;
use WikiPress\Includes\Core\Taxonomy;
use WikiPress\Includes\Functions\Functions;
use WikiPress\Includes\Functions\Helpers\PostHelper;
use WikiPress\Includes\Functions\Helpers\QueryHelper;
use WikiPress\Includes\Functions\Helpers\SanitizationHelper;
use WikiPress\Includes\Functions\Helpers\TaxonomyHelper;
use WikiPress\Includes\Functions\Helpers\PermalinkHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class API {
    /**
     * Lists all wikis with optional query parameters.
     *
     * @param array $args Optional query parameters for filtering and pagination.
     * @return array An array containing the list of wikis, total count, and current page.
     */
    public static function list_wikis( array $args = [] ): array {
        $query_args = wp_parse_args( $args, [ 'post_type' => PostType::WIKI, 'posts_per_page' => 20, 'paged' => 1, 'post_status' => 'publish' ] );
        $query = QueryHelper::posts( $query_args );
        $posts = array_filter( $query->posts, static fn( \WP_Post $post ): bool => apply_filters( 'wikipress_wiki_access_allowed', true, (int) $post->ID ) );
        return [ 'items' => array_map( [ self::class, 'format_wiki' ], $posts ), 'total' => (int) $query->found_posts, 'page' => (int) ( $query_args['paged'] ?? 1 ) ];
    }
    /**
     * Retrieves a specific wiki by its ID.
     *
     * @param int $id The ID of the wiki to retrieve.
     * @return array|null An array containing the wiki data or null if not found or access denied.
     */
    public static function get_wiki( int $id ): ?array {
        $post = PostHelper::get( $id );
        return $post && $post->post_type === PostType::WIKI && apply_filters( 'wikipress_wiki_access_allowed', true, $id ) ? self::format_wiki( $post ) : null;
    }
    /**
     * Retrieves a specific page by its ID.
     *
     * @param int $id The ID of the page to retrieve.
     * @return array|null An array containing the page data or null if not found or access denied.
     */
    public static function create_wiki( array $payload ): \WP_Error|array {
        $payload = apply_filters( 'wikipress_wiki_payload', $payload, null );
        $title = SanitizationHelper::text( $payload['title'] ?? '' );
        if ( $title === '' ) {
            return new \WP_Error( 'missing_title', __( 'A Wiki name is required.', 'wikipress' ) );
        }
        $id = wp_insert_post( [
            'post_type' => PostType::WIKI,
            'post_title' => $title,
            'post_content' => is_scalar( $payload['description'] ?? null ) ? wp_kses_post( (string) $payload['description'] ) : '',
            'post_status' => self::sanitize_status( $payload['status'] ?? 'publish' ),
        ], true );
        if ( is_wp_error( $id ) ) {
            return $id;
        }
		self::save_wiki_permalink( (int) $id, $payload );
        do_action( 'wikipress_wiki_saved', (int) $id, $payload );
        return self::get_wiki( (int) $id );
    }
    /**
     * Updates an existing wiki by its ID.
     *
     * @param int $id The ID of the wiki to update.
     * @param array $payload The data to update the wiki with.
     * @return array|\WP_Error An array containing the updated wiki data or a WP_Error if an error occurred.
     */
    public static function update_wiki( int $id, array $payload ): \WP_Error|array {
        $post = PostHelper::get( $id );
        if ( ! $post || $post->post_type !== PostType::WIKI ) {
            return new \WP_Error( 'not_found', __( 'Wiki not found.', 'wikipress' ), [ 'status' => 404 ] );
        }
        $payload = apply_filters( 'wikipress_wiki_payload', $payload, $post );
        $title = SanitizationHelper::text( $payload['title'] ?? '' );
        if ( $title === '' ) {
            return new \WP_Error( 'missing_title', __( 'A Wiki name is required.', 'wikipress' ) );
        }
        $updated = wp_update_post( [
            'ID' => $id,
            'post_title' => $title,
            'post_content' => is_scalar( $payload['description'] ?? null ) ? wp_kses_post( (string) $payload['description'] ) : '',
            'post_status' => self::sanitize_status( $payload['status'] ?? $post->post_status ),
        ], true );
        if ( is_wp_error( $updated ) ) {
            return $updated;
        }
		self::save_wiki_permalink( $id, $payload );
        do_action( 'wikipress_wiki_saved', $id, $payload );
        return self::get_wiki( $id );
    }
    /**
     * Deletes a specific wiki by its ID.
     *
     * @param int $id The ID of the wiki to delete.
     * @param bool $force Whether to force delete the wiki (bypass trash).
     * @return bool|\WP_Error True on success, false on failure, or a WP_Error if the wiki was not found.
     */
    public static function delete_wiki( int $id, bool $force = false ): \WP_Error|bool {
        $post = PostHelper::get( $id );
        if ( ! $post || $post->post_type !== PostType::WIKI ) {
            return new \WP_Error( 'not_found', __( 'Wiki not found.', 'wikipress' ), [ 'status' => 404 ] );
        }
        return (bool) wp_delete_post( $id, $force );
    }
    /**
     * Sanitizes the status value for a wiki or page.
     *
     * @param string $status The status value to sanitize.
     * @return string The sanitized status value ('publish', 'draft', or 'private').
     */
    public static function list_pages( array $args = [] ): array {
        $query_args = wp_parse_args( $args, [ 'post_type' => PostType::PAGE, 'posts_per_page' => 20, 'paged' => 1, 'post_status' => 'publish' ] );
        $query = QueryHelper::posts( $query_args );
        $posts = array_filter( $query->posts, static function ( \WP_Post $post ): bool {
            $wiki_id = absint( get_post_meta( $post->ID, '_wikipress_wiki_id', true ) );
            return $wiki_id < 1 || apply_filters( 'wikipress_wiki_access_allowed', true, $wiki_id );
        } );
        return [ 'items' => array_map( [ self::class, 'format_post' ], $posts ), 'total' => (int) $query->found_posts, 'page' => (int) ( $query_args['paged'] ?? 1 ) ];
    }
    /**
     * Retrieves a specific page by its ID.
     *
     * @param int $id The ID of the page to retrieve.
     * @return array|null An array containing the page data or null if not found or access denied.
     */
    public static function get_page( int $id ): ?array {
        $post = PostHelper::get( $id );
        $wiki_id = $post ? absint( get_post_meta( $post->ID, '_wikipress_wiki_id', true ) ) : 0;
        return $post && $post->post_type === PostType::PAGE && ( $wiki_id < 1 || apply_filters( 'wikipress_wiki_access_allowed', true, $wiki_id ) ) ? self::format_post( $post ) : null;
    }
    /**
     * Sanitizes the status value for a wiki or page.
     *
     * @param string $status The status value to sanitize.
     * @return string The sanitized status value ('publish', 'draft', or 'private').
     */
    public static function create_page( array $payload ): \WP_Error|array {
        $payload = Functions::sanitize_wiki_payload( $payload );
        if ( $payload['title'] === '' ) {
            return new \WP_Error( 'missing_title', __( 'A page title is required.', 'wikipress' ) );
        }
        $id = wp_insert_post( [
            'post_type' => PostType::PAGE,
            'post_title' => $payload['title'],
            'post_content' => $payload['content'],
            'post_excerpt' => $payload['excerpt'],
            'post_status' => $payload['status'],
        ], true );
        if ( is_wp_error( $id ) ) {
            return $id;
        }
        self::save_relationships( (int) $id, $payload );
        return self::get_page( (int) $id );
    }
    /**
     * Updates an existing page by its ID.
     *
     * @param int $id The ID of the page to update.
     * @param array $payload The data to update the page with.
     * @return array|\WP_Error An array containing the updated page data or a WP_Error if an error occurred.
     */
    public static function update_page( int $id, array $payload ): \WP_Error|array {
        $post = PostHelper::get( $id );
        if ( ! $post || $post->post_type !== PostType::PAGE ) {
            return new \WP_Error( 'not_found', __( 'Wiki page not found.', 'wikipress' ), [ 'status' => 404 ] );
        }
        $payload = Functions::sanitize_wiki_payload( $payload );
        if ( $payload['title'] === '' ) {
            return new \WP_Error( 'missing_title', __( 'A page title is required.', 'wikipress' ) );
        }
        $updated = wp_update_post( [
            'ID' => $id,
            'post_title' => $payload['title'],
            'post_content' => $payload['content'],
            'post_excerpt' => $payload['excerpt'],
            'post_status' => $payload['status'],
        ], true );
        if ( is_wp_error( $updated ) ) {
            return $updated;
        }
        self::save_relationships( $id, $payload );
        return self::get_page( $id );
    }
    /**
     * Deletes a specific page by its ID.
     *
     * @param int $id The ID of the page to delete.
     * @param bool $force Whether to force delete the page (bypass trash).
     * @return bool|\WP_Error True on success, false on failure, or a WP_Error if the page was not found.
     */
    public static function delete_page( int $id, bool $force = false ): \WP_Error|bool {
        $post = PostHelper::get( $id );
        if ( ! $post || $post->post_type !== PostType::PAGE ) {
            return new \WP_Error( 'not_found', __( 'Wiki page not found.', 'wikipress' ), [ 'status' => 404 ] );
        }
        return (bool) wp_delete_post( $id, $force );
    }
    /**
     * Saves the relationships (wiki, categories, tags) for a page.
     *
     * @param int $id The ID of the page.
     * @param array $payload The data containing relationships to save.
     */
    private static function save_relationships( int $id, array $payload ): void {
        $wiki_id = absint( $payload['wiki_id'] ?? 0 );
        $wiki = PostHelper::get( $wiki_id );
        if ( $wiki && PostHelper::is_wiki( $wiki ) ) {
            update_post_meta( $id, '_wikipress_wiki_id', $wiki_id );
        } else {
            delete_post_meta( $id, '_wikipress_wiki_id' );
        }
        wp_set_post_terms( $id, TaxonomyHelper::names( $payload['categories'] ), Taxonomy::CATEGORY, false );
        wp_set_post_terms( $id, TaxonomyHelper::names( $payload['tags'] ), Taxonomy::TAG, false );
    }
    /**
     * Sanitizes the status value for a wiki or page.
     *
     * @param string $status The status value to sanitize.
     * @return string The sanitized status value ('publish', 'draft', or 'private').
     */
    public static function format_post( \WP_Post $post ): array {
        return [
            'id' => $post->ID,
            'title' => get_the_title( $post ),
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'status' => $post->post_status,
            'author' => (int) $post->post_author,
            'wiki_id' => (int) get_post_meta( $post->ID, '_wikipress_wiki_id', true ),
            'categories' => TaxonomyHelper::ids( TaxonomyHelper::terms( Taxonomy::CATEGORY, $post->ID ) ),
            'tags' => TaxonomyHelper::ids( TaxonomyHelper::terms( Taxonomy::TAG, $post->ID ) ),
            'date' => $post->post_date_gmt,
            'link' => get_permalink( $post ),
        ];
    }
    /**
     * Sanitizes the status value for a wiki or page.
     *
     * @param string $status The status value to sanitize.
     * @return string The sanitized status value ('publish', 'draft', or 'private').
     */
    public static function format_wiki( \WP_Post $post ): array {
        $pages = QueryHelper::posts( [ 'post_type' => PostType::PAGE, 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_wikipress_wiki_id', 'meta_value' => $post->ID ] );
        return [
            'id' => $post->ID,
            'name' => get_the_title( $post ),
            'description' => $post->post_content,
            'status' => $post->post_status,
            'author' => (int) $post->post_author,
            'page_count' => (int) $pages->found_posts,
            'date' => $post->post_date_gmt,
			'permalink' => (string) get_post_meta( $post->ID, PermalinkHelper::OVERRIDE_META, true ),
        ];
    }
    /**
     * Sanitizes the status value for a wiki or page.
     *
     * @param string $status The status value to sanitize.
     * @return string The sanitized status value ('publish', 'draft', or 'private').
     */
    private static function sanitize_status( string $status ): string {
        return SanitizationHelper::one_of( SanitizationHelper::key( $status ), [ 'publish', 'draft', 'private' ], 'draft' );
    }

    private static function save_wiki_permalink( int $wiki_id, array $payload ): void {
        if ( ! array_key_exists( 'permalink', $payload ) ) {
            return;
        }
        $pattern = PermalinkHelper::sanitize_pattern( $payload['permalink'] );
        if ( '' === $pattern ) {
            delete_post_meta( $wiki_id, PermalinkHelper::OVERRIDE_META );
            return;
        }
        update_post_meta( $wiki_id, PermalinkHelper::OVERRIDE_META, $pattern );
    }
}
