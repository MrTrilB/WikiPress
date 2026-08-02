<?php
/**
 * TrilB.Dev Plugin - Wiki REST Routes
 *
 * Registers the Wiki REST API endpoints and maps them to service methods.
 *
 * @package TrilBDev
 * @subpackage Includes\Wiki\API
 * @since 1.0.0
 */

namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\API;

use MrTrilB\TrilBDevPlugin\Includes\Wiki\API\Response;
use MrTrilB\TrilBDevPlugin\Includes\Wiki\API\Schema;
use MrTrilB\TrilBDevPlugin\Includes\Wiki\API\Validators;
use MrTrilB\TrilBDevPlugin\Includes\Wiki\Pages\Taxonomies;
use MrTrilB\TrilBDevPlugin\Includes\Wiki\Functions\Functions;
use WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Routes {
    public static function register_routes(): void {
        if ( ! function_exists( 'register_rest_route' ) ) {
            return;
        }

        add_action( 'rest_api_init', [ Schema::class, 'register_schemas' ] );

        register_rest_route(
            'trilbdev-wiki/v1',
            '/pages',
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [ self::class, 'list_pages' ],
                    'permission_callback' => [ self::class, 'read_permission' ],
                    'args'                => Schema::get_list_args(),
                ],
                [
                    'methods'             => 'POST',
                    'callback'            => [ self::class, 'create_page' ],
                    'permission_callback' => [ self::class, 'write_permission' ],
                    'args'                => Schema::get_page_create_args(),
                ],
            ]
        );

        register_rest_route(
            'trilbdev-wiki/v1',
            '/pages/slug/(?P<slug>[a-zA-Z0-9-]+)',
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [ self::class, 'get_page_by_slug' ],
                    'permission_callback' => [ self::class, 'read_permission' ],
                ],
            ]
        );

        register_rest_route(
            'trilbdev-wiki/v1',
            '/taxonomies/(?P<type>categories|tags)',
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [ self::class, 'list_taxonomies' ],
                    'permission_callback' => [ self::class, 'read_permission' ],
                    'args'                => Schema::get_taxonomy_args(),
                ],
            ]
        );

        register_rest_route(
            'trilbdev-wiki/v1',
            '/pages/(?P<id>\d+)',
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [ self::class, 'get_page' ],
                    'permission_callback' => [ self::class, 'read_permission' ],
                ],
                [
                    'methods'             => [ 'PUT', 'PATCH' ],
                    'callback'            => [ self::class, 'update_page' ],
                    'permission_callback' => [ self::class, 'write_permission' ],
                    'args'                => Schema::get_page_update_args(),
                ],
                [
                    'methods'             => 'DELETE',
                    'callback'            => [ self::class, 'delete_page' ],
                    'permission_callback' => [ self::class, 'write_permission' ],
                ],
            ]
        );
    }

    public static function read_permission(): bool {
        return true;
    }

    public static function write_permission(): bool {
        return current_user_can( 'edit_posts' );
    }

    public static function list_pages( WP_REST_Request $request ): array {
        $query_args = [
            'posts_per_page' => absint( $request->get_param( 'per_page' ) ?? 50 ),
            'paged'          => absint( $request->get_param( 'page' ) ?? 1 ),
            'post_status'    => [ 'publish', 'draft', 'private' ],
            's'              => sanitize_text_field( $request->get_param( 'search' ) ?? '' ),
            'tax_query'      => self::build_tax_query( $request ),
        ];

        return ( new API() )->listPages( $query_args );
    }

    private static function build_tax_query( WP_REST_Request $request ): array {
        $tax_query = [];

        $category = sanitize_text_field( $request->get_param( 'category' ) ?? '' );
        if ( $category !== '' ) {
            $tax_query[] = [
                'taxonomy' => 'wiki-categories',
                'field'    => 'slug',
                'terms'    => explode( ',', $category ),
            ];
        }

        $tags = sanitize_text_field( $request->get_param( 'tags' ) ?? '' );
        if ( $tags !== '' ) {
            $tax_query[] = [
                'taxonomy' => 'wiki-tags',
                'field'    => 'slug',
                'terms'    => explode( ',', $tags ),
            ];
        }

        if ( count( $tax_query ) > 1 ) {
            $tax_query['relation'] = 'AND';
        }

        return $tax_query;
    }

    public static function get_page( WP_REST_Request $request ): array {
        return ( new API() )->getPage( (string) absint( $request->get_param( 'id' ) ) );
    }

    public static function get_page_by_slug( WP_REST_Request $request ): array {
        $slug = sanitize_text_field( $request->get_param( 'slug' ) );
        return ( new API() )->getPageBySlug( $slug );
    }

    public static function list_taxonomies( WP_REST_Request $request ): array {
        $type = sanitize_text_field( $request->get_param( 'type' ) );
        $search = sanitize_text_field( $request->get_param( 'search' ) ?? '' );
        $limit = absint( $request->get_param( 'limit' ) ?? 50 );

        if ( $type === 'categories' ) {
            return Response::success( Taxonomies::get_categories( $limit, $search ) );
        }

        if ( $type === 'tags' ) {
            return Response::success( Taxonomies::get_tags( $limit, $search ) );
        }

        return Response::error( 'Invalid taxonomy type.' );
    }

    public static function create_page( WP_REST_Request $request ): array {
        $payload = $request->get_json_params();
        $payload = is_array( $payload ) ? $payload : [];

        $validation = Validators::validate_page_payload( $payload );
        if ( ! $validation['valid'] ) {
            return Response::error( implode( ' ', $validation['errors'] ), [ 'errors' => $validation['errors'] ] );
        }

        return Response::success( ( new API() )->createPage( $validation['payload'] )['data'], 'Wiki page created.' );
    }

    public static function update_page( WP_REST_Request $request ): array {
        $page_id = absint( $request->get_param( 'id' ) );
        $payload = $request->get_json_params();
        $payload = is_array( $payload ) ? $payload : [];

        $validation = Validators::validate_page_payload( $payload );
        if ( ! $validation['valid'] ) {
            return Response::error( implode( ' ', $validation['errors'] ), [ 'errors' => $validation['errors'] ] );
        }

        $result = ( new API() )->updatePage( (string) $page_id, $validation['payload'] );
        return $result['success'] ? Response::success( $result['data'], 'Wiki page updated.' ) : Response::error( $result['message'], $result['data'] );
    }

    public static function delete_page( WP_REST_Request $request ): array {
        $page_id = absint( $request->get_param( 'id' ) );
        $deleted = ( new API() )->deletePage( (string) $page_id );

        return $deleted ? Response::success( [ 'id' => $page_id ], 'Wiki page deleted.' ) : Response::error( 'Wiki page not found.', [ 'id' => $page_id ] );
    }
}
