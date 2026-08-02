<?php

namespace TrilBDev\WikiPress\API;

use TrilBDev\WikiPress\Includes\Core\PostType;
use WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Routes {
    public static function register_routes(): void {
        register_rest_route( 'wikipress/v1', '/wikis', [
            [ 'methods' => 'GET', 'callback' => [ self::class, 'list_wikis' ], 'permission_callback' => [ self::class, 'read_permission' ] ],
            [ 'methods' => 'POST', 'callback' => [ self::class, 'create_wiki' ], 'permission_callback' => [ self::class, 'write_permission' ] ],
        ] );
        register_rest_route( 'wikipress/v1', '/wikis/(?P<id>\d+)', [
            [ 'methods' => 'GET', 'callback' => [ self::class, 'get_wiki' ], 'permission_callback' => [ self::class, 'read_permission' ] ],
            [ 'methods' => 'POST, PUT, PATCH', 'callback' => [ self::class, 'update_wiki' ], 'permission_callback' => [ self::class, 'write_permission' ] ],
            [ 'methods' => 'DELETE', 'callback' => [ self::class, 'delete_wiki' ], 'permission_callback' => [ self::class, 'write_permission' ] ],
        ] );
        register_rest_route( 'wikipress/v1', '/pages', [
            [ 'methods' => 'GET', 'callback' => [ self::class, 'list_pages' ], 'permission_callback' => [ self::class, 'read_permission' ] ],
            [ 'methods' => 'POST', 'callback' => [ self::class, 'create_page' ], 'permission_callback' => [ self::class, 'write_permission' ] ],
        ] );
        register_rest_route( 'wikipress/v1', '/pages/(?P<id>\d+)', [
            [ 'methods' => 'GET', 'callback' => [ self::class, 'get_page' ], 'permission_callback' => [ self::class, 'read_permission' ] ],
            [ 'methods' => 'POST, PUT, PATCH', 'callback' => [ self::class, 'update_page' ], 'permission_callback' => [ self::class, 'write_permission' ] ],
            [ 'methods' => 'DELETE', 'callback' => [ self::class, 'delete_page' ], 'permission_callback' => [ self::class, 'write_permission' ] ],
        ] );
    }

    public static function read_permission(): bool { return true; }
    public static function write_permission(): bool { return current_user_can( 'edit_posts' ); }

    public static function list_pages( WP_REST_Request $request ): \WP_REST_Response {
        return Response::success( API::list_pages( [ 'post_type' => PostType::PAGE, 'posts_per_page' => min( 100, max( 1, absint( $request->get_param( 'per_page' ) ?: 20 ) ) ), 'paged' => max( 1, absint( $request->get_param( 'page' ) ?: 1 ) ), 's' => sanitize_text_field( $request->get_param( 'search' ) ?: '' ), 'post_status' => 'publish' ] ) );
    }

    public static function list_wikis( WP_REST_Request $request ): \WP_REST_Response {
        return Response::success( API::list_wikis( [ 'posts_per_page' => min( 100, max( 1, absint( $request->get_param( 'per_page' ) ?: 20 ) ) ), 'paged' => max( 1, absint( $request->get_param( 'page' ) ?: 1 ) ), 's' => sanitize_text_field( $request->get_param( 'search' ) ?: '' ), 'post_status' => 'any' ] ) );
    }

    public static function get_wiki( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $wiki = API::get_wiki( absint( $request['id'] ) );
        return $wiki ? Response::success( $wiki ) : Response::error( 'not_found', __( 'Wiki not found.', 'wikipress' ), 404 );
    }

    public static function create_wiki( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $result = API::create_wiki( (array) $request->get_json_params() );
        return is_wp_error( $result ) ? $result : Response::success( $result, 201 );
    }

    public static function update_wiki( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $result = API::update_wiki( absint( $request['id'] ), (array) $request->get_json_params() );
        return is_wp_error( $result ) ? $result : Response::success( $result );
    }

    public static function delete_wiki( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $result = API::delete_wiki( absint( $request['id'] ), (bool) $request->get_param( 'force' ) );
        return is_wp_error( $result ) ? $result : Response::success( [ 'deleted' => true, 'id' => absint( $request['id'] ) ] );
    }

    public static function get_page( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $page = API::get_page( absint( $request['id'] ) );
        return $page ? Response::success( $page ) : Response::error( 'not_found', __( 'Wiki page not found.', 'wikipress' ), 404 );
    }

    public static function create_page( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $result = API::create_page( (array) $request->get_json_params() );
        return is_wp_error( $result ) ? $result : Response::success( $result, 201 );
    }

    public static function update_page( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $result = API::update_page( absint( $request['id'] ), (array) $request->get_json_params() );
        return is_wp_error( $result ) ? $result : Response::success( $result );
    }

    public static function delete_page( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $result = API::delete_page( absint( $request['id'] ), (bool) $request->get_param( 'force' ) );
        return is_wp_error( $result ) ? $result : Response::success( [ 'deleted' => true, 'id' => absint( $request['id'] ) ] );
    }
}
