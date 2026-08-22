<?php
/**
 * Settings for the Internal Wiki plugin.
 * @package WikiPress
 * @subpackage Admin\Wiki\Plugins\InternalWiki\Includes
 * @since 1.0.0
 */
namespace WikiPress\Includes\Plugins\InternalWiki\Includes\Settings;
use WikiPress\Includes\Core\PostType;
use WikiPress\Includes\Settings\Settings as BaseSettings;
use WikiPress\Includes\Functions\Helpers\LoaderHelper;
use WikiPress\Includes\Functions\Helpers\SanitizationHelper;
use WikiPress\Includes\Functions\Helpers\PermissionHelper;
use WikiPress\Includes\Plugins\InternalWiki\Includes\Templates\InternalWikiFields;

final class Settings {
    /**
     * Loader helper instance.
     *
     * @var LoaderHelper
     */
    private LoaderHelper $loader;
    /**
     * Meta keys for storing internal wiki settings.
     */
    private const META_ENABLED = '_wikipress_internal_enabled';
    /**
     * Meta keys for storing internal wiki settings.
     */
    private const META_ACCESS_TYPE = '_wikipress_internal_access_type';
    /**
     * Meta keys for storing internal wiki settings.
     */
    private const META_ROLES = '_wikipress_internal_roles';
    /**
     * Meta keys for storing internal wiki settings.
     */
    private const META_PERMISSIONS = '_wikipress_internal_permissions';
    /**
     * Constructor for the Settings class.
     *
     * @param LoaderHelper|null $loader Optional LoaderHelper instance. If not provided, a new instance will be created.
     */
    public function __construct( ?LoaderHelper $loader = null ) {

        $this->loader = $loader ?? new LoaderHelper();

    }

    /**
     * Returns the settings for the Internal Wiki plugin.
     *
     * @return array The settings array.
     */
    public function register(): void {
        BaseSettings::register_group( 
            'internal_wiki', 
            [
            'default_access_type' => 'logged_in_user',
            'default_roles' => [],
            'default_permissions' => [],
        ] );

        $this->loader->register_component( $this, 
        [

            [ 
                'type' => 'filter', 
                'hook' => 'wikipress_wiki_form_fields', 
                'callback' => 'render_fields', 
                'accepted_args' => 2 
            ],
            [ 
                'type' => 'filter', 
                'hook' => 'wikipress_wiki_payload', 
                'callback' => 'sanitize_payload', 
                'accepted_args' => 2 
            ],
            [ 
                'type' => 'filter', 
                'hook' => 'wikipress_wiki_access_allowed', 
                'callback' => 'filter_access', 
                'accepted_args' => 2 
            ],
            [ 
                'type' => 'action', 
                'hook' => 'wikipress_wiki_saved', 
                'callback' => 'save_access', 
                'accepted_args' => 2 
            ],
            [
                'type' => 'action',
                'hook' => 'before_delete_post',
                'callback' => 'authorize_delete',
                'accepted_args' => 2
            ],
            [ 
                'type' => 'action', 
                'hook' => 'template_redirect', 
                'callback' => 'enforce_access' 
            ],
        ] )->run();
    }

    public function get_settings_page(): array {

        $role_options = [];

        foreach ( wp_roles()->roles as $role_key => $role ) {

            $role_options[ $role_key ] = translate_user_role( $role['name'] );

        }

        $permission_options = array_map(

            static fn( string $permission ): array => [ 
                'value' => $permission, 
                'label' => $permission 
            ],
            $this->permissions()
        );

        return [
            'slug' => 'internal_wiki',
            'label' => __( 'Internal Wiki', 'wikipress' ),
            'title' => __( 'Internal Wiki settings', 'wikipress' ),
            'layout' => 'table',
            'fields' => [
                [
                    'key' => 'default_access_type',
                    'label' => __( 'Default access type', 'wikipress' ),
                    'description' => __( 'Choose the default access rule for new Wiki pages.', 'wikipress' ),
                    'type' => 'select',
                    'options' => [
                        'logged_in_user' => __( 'Logged-in users', 'wikipress' ),
                        'roles' => __( 'Specific roles', 'wikipress' ),
                        'permissions' => __( 'Specific permissions', 'wikipress' ),
                    ],
                    'default' => 'logged_in_user',
                    'attributes' => [ 'data-internal-wiki-access-type' => true ],
                ],
                [
                    'key' => 'default_roles',
                    'label' => __( 'Default roles', 'wikipress' ),
                    'description' => __( 'Choose which roles can access new Wiki pages when specific roles are selected.', 'wikipress' ),
                    'type' => 'multiselect',
                    'options' => $role_options,
                    'default' => [],
                    'attributes' => [ 'data-internal-wiki-roles-select' => true ],
                    'wrapper_class' => 'wikipress-internal-wiki-roles',
                    'wrapper_attributes' => [ 'data-internal-wiki-roles' => true ],
                    'visible_when' => [ 'field' => 'default_access_type', 'equals' => 'roles' ],
                ],
                [
                    'key' => 'default_permissions',
                    'label' => __( 'Default permissions', 'wikipress' ),
                    'description' => __( 'Choose which permissions can access new Wiki pages when specific permissions are selected.', 'wikipress' ),
                    'type' => 'multiselect',
                    'options' => $permission_options,
                    'default' => [],
                    'attributes' => [ 'data-internal-wiki-permissions-select' => true ],
                    'wrapper_class' => 'wikipress-internal-wiki-permissions',
                    'wrapper_attributes' => [ 'data-internal-wiki-permissions' => true ],
                    'visible_when' => [ 'field' => 'default_access_type', 'equals' => 'permissions' ],
                ],
            ],
        ];
    }

    public function sanitize( $input ): array {
        $input = is_array( $input ) ? $input : [];
        $access_type = SanitizationHelper::key( 
            $input['default_access_type'] ?? 'logged_in_user', 
            'logged_in_user' 
        );
        $settings = [
            'default_access_type' => in_array( 
                $access_type, 
                [ 
                    'logged_in_user', 
                    'roles', 
                    'permissions' 
                ], 
                true ) ? $access_type : 'logged_in_user',
            'default_roles' => $this->valid_roles( $input['default_roles'] ?? [] ),
            'default_permissions' => $this->valid_permissions( $input['default_permissions'] ?? [] ),
        ];
        BaseSettings::set_group( 'internal_wiki', $settings );
        return $settings;
    }

    public function render_fields( string $fields, ?\WP_Post $post = null ): string {
        if ( ! PermissionHelper::can( 'wikipress_admin_int_view' ) ) {
            return $fields;
        }

        $enabled = $post ? (bool) get_post_meta( $post->ID, self::META_ENABLED, true ) : false;
        $access_type = $post ? (string) get_post_meta( $post->ID, self::META_ACCESS_TYPE, true ) : BaseSettings::get_key( 'default_access_type', 'logged_in_user' );
        $roles = $post ? (array) get_post_meta( $post->ID, self::META_ROLES, true ) : (array) BaseSettings::get( 'default_roles', [] );
        $permissions = $post ? (array) get_post_meta( $post->ID, self::META_PERMISSIONS, true ) : (array) BaseSettings::get( 'default_permissions', [] );
        $access_types = [
            'logged_in_user' => __( 'Logged in user', 'wikipress' ),
            'roles' => __( 'Roles', 'wikipress' ),
            'permissions' => __( 'Permissions', 'wikipress' ),
        ];
        $role_options = [];
        foreach ( wp_roles()->roles as $role_key => $role ) {
            $role_options[ $role_key ] = translate_user_role( $role['name'] );
        }
        $permission_items = array_map( static fn( string $permission ): array => [ 'value' => $permission, 'label' => $permission ], $this->permissions() );
        $selected_permission_items = array_map( static fn( string $permission ): array => [ 'value' => $permission, 'label' => $permission ], $permissions );

        return InternalWikiFields::render( [
            'enabled' => $enabled,
            'access_type' => $access_type,
            'access_types' => $access_types,
            'role_options' => $role_options,
            'roles' => $roles,
            'permission_items' => $permission_items,
            'selected_permission_items' => $selected_permission_items,
        ] );
    }

    public function sanitize_payload( array $payload, ?\WP_Post $post = null ): array {
        $is_internal = $post && get_post_meta( $post->ID, self::META_ENABLED, true );
        if ( $is_internal && ! PermissionHelper::can( 'wikipress_admin_int_view' ) ) {
            $payload['internal_wiki_enabled'] = true;
            $payload['internal_wiki_access_type'] = get_post_meta( $post->ID, self::META_ACCESS_TYPE, true );
            $payload['internal_wiki_roles'] = get_post_meta( $post->ID, self::META_ROLES, true );
            $payload['internal_wiki_permissions'] = get_post_meta( $post->ID, self::META_PERMISSIONS, true );
        }
        $will_be_internal = $is_internal || ! empty( $payload['internal_wiki_enabled'] );
        if ( $will_be_internal ) {
            $this->authorize_mutation( $post, ! $is_internal, $payload );
        }

        $payload['internal_wiki_enabled'] = ! empty( $payload['internal_wiki_enabled'] );
        $access_type = SanitizationHelper::key( $payload['internal_wiki_access_type'] ?? '' );
        $payload['internal_wiki_access_type'] = in_array( $access_type, [ 'logged_in_user', 'roles', 'permissions' ], true ) ? $access_type : 'logged_in_user';
        $payload['internal_wiki_roles'] = $this->valid_roles( $payload['internal_wiki_role'] ?? $payload['internal_wiki_roles'] ?? [] );
        $payload['internal_wiki_permissions'] = $this->valid_permissions( $payload['internal_wiki_permissions'] ?? [] );
        if ( ! $payload['internal_wiki_enabled'] ) {
            $payload['internal_wiki_access_type'] = 'logged_in_user';
            $payload['internal_wiki_roles'] = [];
            $payload['internal_wiki_permissions'] = [];
        }
        return $payload;
    }

    public function save_access( int $post_id, array $payload ): void {
        update_post_meta( 
            $post_id, 
            self::META_ENABLED, 
            ! empty( 
                $payload[
                    'internal_wiki_enabled'
                ] 
            ) 
        );
        update_post_meta( 
            $post_id, 
            self::META_ACCESS_TYPE, 
            SanitizationHelper::key( 
                $payload[
                    'internal_wiki_access_type'
                ] ?? 'logged_in_user', 
                'logged_in_user' 
            ) 
        );
        update_post_meta( 
            $post_id, 
            self::META_ROLES, 
            $this->valid_roles( 
                $payload[
                    'internal_wiki_roles'
                ] ?? [] 
            ) 
        );
        update_post_meta( 
            $post_id, 
            self::META_PERMISSIONS, 
            $this->valid_permissions( 
                $payload[
                    'internal_wiki_permissions'
                ] ?? [] 
            ) 
        );
    }

    public function enforce_access(): void {
        if ( ! is_singular( \WikiPress\Includes\Core\PostType::PAGE ) ) {
            return;
        }

        $wiki_id = absint( get_post_meta( get_queried_object_id(), '_wikipress_wiki_id', true ) );
        if ( $wiki_id > 0 && ! $this->can_access( $wiki_id ) ) {
            wp_die( esc_html__( 'You do not have permission to access this Wiki.', 'wikipress' ), 403 );
        }
    }

    public function can_access( int $wiki_id ): bool {
        if ( ! get_post_meta( $wiki_id, self::META_ENABLED, true ) ) {
            return true;
        }

        if ( ! PermissionHelper::can( 'wikipress_int_read' ) ) {
            return false;
        }

        $access_type = get_post_meta( $wiki_id, self::META_ACCESS_TYPE, true );
        if ( ! is_user_logged_in() ) {
            return false;
        }

        if ( 'logged_in_user' === $access_type ) {
            return true;
        }

        if ( 'roles' === $access_type ) {
            $user = wp_get_current_user();
            return (bool) array_intersect( (array) $user->roles, (array) get_post_meta( $wiki_id, self::META_ROLES, true ) );
        }

        if ( 'permissions' === $access_type ) {
            foreach ( (array) get_post_meta( $wiki_id, self::META_PERMISSIONS, true ) as $permission ) {
                if ( PermissionHelper::can( $permission ) ) {
                    return true;
                }
            }
        }

        return false;
    }

    public function filter_access( bool $allowed, int $wiki_id ): bool {
        return $allowed && $this->can_access( $wiki_id );
    }

    private function authorize_mutation( ?\WP_Post $post, bool $creating, array $payload ): void {
        $capability = $creating ? 'wikipress_int_create' : 'wikipress_int_edit';
        if ( ! PermissionHelper::can( $capability ) ) {
            wp_die( esc_html__( 'You are not allowed to modify Internal Wiki content.', 'wikipress' ), 403 );
        }

        if ( ! $creating && $post && (int) $post->post_author !== get_current_user_id() && ! PermissionHelper::can( 'wikipress_int_edit_others' ) ) {
            wp_die( esc_html__( 'You are not allowed to modify content created by another user.', 'wikipress' ), 403 );
        }

        if ( ! $creating && $post && 'publish' === $post->post_status && ! PermissionHelper::can( 'wikipress_int_edit_published' ) ) {
            wp_die( esc_html__( 'You are not allowed to modify published Internal Wiki content.', 'wikipress' ), 403 );
        }

        $status = SanitizationHelper::key( $payload['status'] ?? ( $post->post_status ?? '' ) );
        if ( 'publish' === $status && ( $creating || ! $post || 'publish' !== $post->post_status ) && ! PermissionHelper::can( 'wikipress_int_publish' ) ) {
            wp_die( esc_html__( 'You are not allowed to publish Internal Wiki content.', 'wikipress' ), 403 );
        }
    }

    public function authorize_delete( int $post_id, \WP_Post $post ): void {
        $wiki_id = PostType::WIKI === $post->post_type
            ? $post_id
            : ( PostType::PAGE === $post->post_type ? absint( get_post_meta( $post_id, '_wikipress_wiki_id', true ) ) : 0 );
        if ( $wiki_id < 1 || ! get_post_meta( $wiki_id, self::META_ENABLED, true ) ) {
            return;
        }

        if ( ! PermissionHelper::can( 'wikipress_int_delete' ) ) {
            wp_die( esc_html__( 'You are not allowed to delete Internal Wiki content.', 'wikipress' ), 403 );
        }

        if ( (int) $post->post_author !== get_current_user_id() && ! PermissionHelper::can( 'wikipress_int_delete_others' ) ) {
            wp_die( esc_html__( 'You are not allowed to delete content created by another user.', 'wikipress' ), 403 );
        }

        if ( 'publish' === $post->post_status && ! PermissionHelper::can( 'wikipress_int_delete_published' ) ) {
            wp_die( esc_html__( 'You are not allowed to delete published Internal Wiki content.', 'wikipress' ), 403 );
        }
    }

    private function permissions(): array {
        $permissions = [];
        foreach ( wp_roles()->roles as $role ) {
            foreach ( array_keys( array_filter( $role['capabilities'] ?? [] ) ) as $permission ) {
                $permissions[ $permission ] = true;
            }
        }
        $permissions = array_keys( $permissions );
        sort( $permissions );
        return $permissions;
    }

    private function valid_roles( $roles ): array {
        $roles = is_array( $roles ) ? array_map( [ SanitizationHelper::class, 'key' ], $roles ) : [];
        return array_values( array_intersect( array_unique( $roles ), array_keys( wp_roles()->roles ) ) );
    }

    private function valid_permissions( $permissions ): array {
        $permissions = is_array( $permissions ) ? array_map( [ SanitizationHelper::class, 'key' ], $permissions ) : [];
        return array_values( array_intersect( array_unique( $permissions ), $this->permissions() ) );
    }
}