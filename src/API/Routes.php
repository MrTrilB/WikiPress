<?php
/**
 * Routes class for registering REST API routes.
 *
 * @package TrilBDev\WikiPress\API
 */
namespace TrilBDev\WikiPress\API;

use TrilBDev\WikiPress\Includes\Core\PostType;
use TrilBDev\WikiPress\Includes\Functions\Helpers\PermissionHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\RequestHelper;
use WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Routes {
    /**
     * Registers all REST API routes for WikiPress.
     */
    public static function register_routes(): void {
        register_rest_route( 'wikipress/v1', '/wikis', [
            [ 'methods' => 'GET', 'callback' => [ self::class, 'list_wikis' ], 'permission_callback' => [ self::class, 'read_permission' ], 'args' => Schema::collection_parameters() ],
            [ 'methods' => 'POST', 'callback' => [ self::class, 'create_wiki' ], 'permission_callback' => [ self::class, 'write_permission' ], 'args' => Schema::wiki()['properties'] ],
        ] );
        register_rest_route( 'wikipress/v1', '/wikis/(?P<id>\d+)', [
            [ 'methods' => 'GET', 'callback' => [ self::class, 'get_wiki' ], 'permission_callback' => [ self::class, 'read_permission' ], 'args' => [ 'id' => [ 'type' => 'integer', 'minimum' => 1 ] ] ],
            [ 'methods' => 'POST, PUT, PATCH', 'callback' => [ self::class, 'update_wiki' ], 'permission_callback' => [ self::class, 'write_permission' ], 'args' => array_merge( [ 'id' => [ 'type' => 'integer', 'minimum' => 1 ] ], Schema::wiki()['properties'] ) ],
            [ 'methods' => 'DELETE', 'callback' => [ self::class, 'delete_wiki' ], 'permission_callback' => [ self::class, 'write_permission' ], 'args' => [ 'id' => [ 'type' => 'integer', 'minimum' => 1 ], 'force' => [ 'type' => 'boolean', 'default' => false ] ] ],
        ] );
        register_rest_route( 'wikipress/v1', '/pages', [
            [ 'methods' => 'GET', 'callback' => [ self::class, 'list_pages' ], 'permission_callback' => [ self::class, 'read_permission' ], 'args' => Schema::collection_parameters() ],
            [ 'methods' => 'POST', 'callback' => [ self::class, 'create_page' ], 'permission_callback' => [ self::class, 'write_permission' ], 'args' => Schema::page()['properties'] ],
        ] );
        register_rest_route( 'wikipress/v1', '/pages/(?P<id>\d+)', [
            [ 'methods' => 'GET', 'callback' => [ self::class, 'get_page' ], 'permission_callback' => [ self::class, 'read_permission' ], 'args' => [ 'id' => [ 'type' => 'integer', 'minimum' => 1 ] ] ],
            [ 'methods' => 'POST, PUT, PATCH', 'callback' => [ self::class, 'update_page' ], 'permission_callback' => [ self::class, 'write_permission' ], 'args' => array_merge( [ 'id' => [ 'type' => 'integer', 'minimum' => 1 ] ], Schema::page()['properties'] ) ],
            [ 'methods' => 'DELETE', 'callback' => [ self::class, 'delete_page' ], 'permission_callback' => [ self::class, 'write_permission' ], 'args' => [ 'id' => [ 'type' => 'integer', 'minimum' => 1 ], 'force' => [ 'type' => 'boolean', 'default' => false ] ] ],
        ] );
    }
    /**
     * Permission callback for read operations.
     *
     * @return bool True if the user has permission to read, false otherwise.
     */
    public static function read_permission(): bool { return true; }
    /**
     * Permission callback for write operations.
     *
     * @return bool True if the user has permission to write, false otherwise.
     */
    public static function write_permission(): bool { return PermissionHelper::can( 'edit_posts' ); }
    /**
     * Lists pages with optional filtering and pagination.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return \WP_REST_Response The REST API response containing the list of pages.
     */
    public static function list_pages( WP_REST_Request $request ): \WP_REST_Response {
        return Response::success( API::list_pages( [ 'post_type' => PostType::PAGE, 'posts_per_page' => RequestHelper::integer_range( $request->get_param( 'per_page' ), 1, 100, 20 ), 'paged' => max( 1, absint( $request->get_param( 'page' ) ?: 1 ) ), 's' => RequestHelper::text( [ 'search' => $request->get_param( 'search' ) ], 'search' ), 'post_status' => 'publish' ] ) );
    }
    /**
     * Lists wikis with optional filtering and pagination.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return \WP_REST_Response The REST API response containing the list of wikis.
     */
    public static function list_wikis( WP_REST_Request $request ): \WP_REST_Response {
        return Response::success( API::list_wikis( [ 'posts_per_page' => RequestHelper::integer_range( $request->get_param( 'per_page' ), 1, 100, 20 ), 'paged' => max( 1, absint( $request->get_param( 'page' ) ?: 1 ) ), 's' => RequestHelper::text( [ 'search' => $request->get_param( 'search' ) ], 'search' ), 'post_status' => 'any' ] ) );
    }
    /**
     * Retrieves a specific wiki by its ID.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return \WP_REST_Response|\WP_Error The REST API response containing the wiki data or an error if not found.
     */
    public static function get_wiki( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $wiki = API::get_wiki( absint( $request['id'] ) );
        return $wiki ? Response::success( $wiki ) : Response::error( 'not_found', __( 'Wiki not found.', 'wikipress' ), 404 );
    }
    /**
     * Creates a new wiki.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return \WP_REST_Response|\WP_Error The REST API response containing the created wiki data or an error if creation failed.
     */
    public static function create_wiki( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $result = API::create_wiki( (array) $request->get_json_params() );
        return is_wp_error( $result ) ? $result : Response::success( $result, 201 );
    }
    /**
     * Updates an existing wiki by its ID.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return \WP_REST_Response|\WP_Error The REST API response containing the updated wiki data or an error if update failed.
     */
    public static function update_wiki( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $result = API::update_wiki( absint( $request['id'] ), (array) $request->get_json_params() );
        return is_wp_error( $result ) ? $result : Response::success( $result );
    }
    /**
     * Deletes a specific wiki by its ID.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return \WP_REST_Response|\WP_Error The REST API response indicating success or an error if deletion failed.
     */
    public static function delete_wiki( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $result = API::delete_wiki( absint( $request['id'] ), RequestHelper::boolean( [ 'force' => $request->get_param( 'force' ) ], 'force' ) );
        return is_wp_error( $result ) ? $result : Response::success( [ 'deleted' => true, 'id' => absint( $request['id'] ) ] );
    }
    /**
     * Retrieves a specific page by its ID.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return \WP_REST_Response|\WP_Error The REST API response containing the page data or an error if not found.
     */
    public static function get_page( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $page = API::get_page( absint( $request['id'] ) );
        return $page ? Response::success( $page ) : Response::error( 'not_found', __( 'Wiki page not found.', 'wikipress' ), 404 );
    }
    /**
     * Creates a new page.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return \WP_REST_Response|\WP_Error The REST API response containing the created page data or an error if creation failed.
     */
    public static function create_page( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $result = API::create_page( (array) $request->get_json_params() );
        return is_wp_error( $result ) ? $result : Response::success( $result, 201 );
    }
    /**
     * Updates an existing page by its ID.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return \WP_REST_Response|\WP_Error The REST API response containing the updated page data or an error if update failed.
     */
    public static function update_page( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $result = API::update_page( absint( $request['id'] ), (array) $request->get_json_params() );
        return is_wp_error( $result ) ? $result : Response::success( $result );
    }
    /**
     * Deletes a specific page by its ID.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return \WP_REST_Response|\WP_Error The REST API response indicating success or an error if deletion failed.
     */
    public static function delete_page( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
        $result = API::delete_page( absint( $request['id'] ), RequestHelper::boolean( [ 'force' => $request->get_param( 'force' ) ], 'force' ) );
        return is_wp_error( $result ) ? $result : Response::success( [ 'deleted' => true, 'id' => absint( $request['id'] ) ] );
    }
}
