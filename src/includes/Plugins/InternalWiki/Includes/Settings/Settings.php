<?php
/**
 * Settings for the Internal Wiki plugin.
 * @package TrilBDev
 * @subpackage Admin\Wiki\Plugins\InternalWiki\Includes
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Includes\Plugins\InternalWiki\Includes\Settings;
use TrilBDev\WikiPress\Includes\Settings\Settings as BaseSettings;
use TrilBDev\WikiPress\Includes\Functions\Helpers\LoaderHelper;

final class Settings {
    private LoaderHelper $loader;
    private const META_ENABLED = '_wikipress_internal_enabled';
    private const META_ACCESS_TYPE = '_wikipress_internal_access_type';
    private const META_ROLES = '_wikipress_internal_roles';
    private const META_PERMISSIONS = '_wikipress_internal_permissions';

    public function __construct( ?LoaderHelper $loader = null ) {
        $this->loader = $loader ?? new LoaderHelper();
    }

    /**
     * Returns the settings for the Internal Wiki plugin.
     *
     * @return array The settings array.
     */
    public function register(): void {
        BaseSettings::register_group( 'internal_wiki', [
            'default_access_type' => 'logged_in_user',
            'default_roles' => [],
            'default_permissions' => [],
        ] );
        $this->loader->register_component( $this, [
            [ 'type' => 'filter', 'hook' => 'wikipress_wiki_form_fields', 'callback' => 'render_fields', 'accepted_args' => 2 ],
            [ 'type' => 'filter', 'hook' => 'wikipress_wiki_payload', 'callback' => 'sanitize_payload', 'accepted_args' => 2 ],
            [ 'type' => 'filter', 'hook' => 'wikipress_wiki_access_allowed', 'callback' => 'filter_access', 'accepted_args' => 2 ],
            [ 'type' => 'action', 'hook' => 'wikipress_wiki_saved', 'callback' => 'save_access', 'accepted_args' => 2 ],
            [ 'type' => 'action', 'hook' => 'template_redirect', 'callback' => 'enforce_access' ],
        ] )->run();
    }

    public function render_fields( string $fields, ?\WP_Post $post = null ): string {
        $enabled = $post ? (bool) get_post_meta( $post->ID, self::META_ENABLED, true ) : false;
        $access_type = $post ? (string) get_post_meta( $post->ID, self::META_ACCESS_TYPE, true ) : 'logged_in_user';
        $roles = $post ? (array) get_post_meta( $post->ID, self::META_ROLES, true ) : [];
        $permissions = $post ? (array) get_post_meta( $post->ID, self::META_PERMISSIONS, true ) : [];
        $access_types = [
            'logged_in_user' => __( 'Logged in user', 'internal-wiki-plugin' ),
            'roles' => __( 'Roles', 'internal-wiki-plugin' ),
            'permissions' => __( 'Permissions', 'internal-wiki-plugin' ),
        ];

        ob_start();
        ?>
        <div class="wikipress-internal-wiki-fields row g-3" data-internal-wiki-fields>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="wikipress-internal-enabled" name="internal_wiki_enabled" value="1" <?php checked( $enabled ); ?> data-internal-wiki-toggle>
                    <label class="form-check-label" for="wikipress-internal-enabled"><?php esc_html_e( 'Make this Wiki Internal', 'internal-wiki-plugin' ); ?></label>
                </div>
            </div>
            <div class="col-md-4" data-internal-wiki-options>
                <label class="form-label" for="wikipress-internal-access-type"><?php esc_html_e( 'Limit access by', 'internal-wiki-plugin' ); ?></label>
                <select class="form-select" id="wikipress-internal-access-type" name="internal_wiki_access_type" data-internal-wiki-access-type>
                    <?php foreach ( $access_types as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $access_type, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4" data-internal-wiki-roles>
                <label class="form-label" for="wikipress-internal-roles"><?php esc_html_e( 'Roles', 'internal-wiki-plugin' ); ?></label>
                <select class="form-select" id="wikipress-internal-roles" name="internal_wiki_role">
                    <?php foreach ( wp_roles()->roles as $role_key => $role ) : ?>
                        <option value="<?php echo esc_attr( $role_key ); ?>" <?php selected( in_array( $role_key, $roles, true ) ); ?>><?php echo esc_html( translate_user_role( $role['name'] ) ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4" data-internal-wiki-permissions>
                <label class="form-label" for="wikipress-internal-permissions"><?php esc_html_e( 'Permissions', 'internal-wiki-plugin' ); ?></label>
                <input class="form-control" id="wikipress-internal-permissions" type="text" autocomplete="off" data-bootstrap-search="permissions" data-items="<?php echo esc_attr( wp_json_encode( array_map( static fn( string $permission ): array => [ 'value' => $permission, 'label' => $permission ], $this->permissions() ) ) ); ?>" data-selected-items="<?php echo esc_attr( wp_json_encode( array_map( static fn( string $permission ): array => [ 'value' => $permission, 'label' => $permission ], $permissions ) ) ); ?>">
                <div data-bootstrap-search-values></div>
            </div>
        </div>
        <?php
        return (string) ob_get_clean();
    }

    public function sanitize_payload( array $payload, ?\WP_Post $post = null ): array {
        $payload['internal_wiki_enabled'] = ! empty( $payload['internal_wiki_enabled'] );
        $payload['internal_wiki_access_type'] = in_array( $payload['internal_wiki_access_type'] ?? '', [ 'logged_in_user', 'roles', 'permissions' ], true ) ? sanitize_key( $payload['internal_wiki_access_type'] ) : 'logged_in_user';
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
        update_post_meta( $post_id, self::META_ENABLED, ! empty( $payload['internal_wiki_enabled'] ) );
        update_post_meta( $post_id, self::META_ACCESS_TYPE, sanitize_key( $payload['internal_wiki_access_type'] ?? 'logged_in_user' ) );
        update_post_meta( $post_id, self::META_ROLES, $this->valid_roles( $payload['internal_wiki_roles'] ?? [] ) );
        update_post_meta( $post_id, self::META_PERMISSIONS, $this->valid_permissions( $payload['internal_wiki_permissions'] ?? [] ) );
    }

    public function enforce_access(): void {
        if ( ! is_singular( \TrilBDev\WikiPress\Includes\Core\PostType::PAGE ) ) {
            return;
        }

        $wiki_id = absint( get_post_meta( get_queried_object_id(), '_wikipress_wiki_id', true ) );
        if ( $wiki_id > 0 && ! $this->can_access( $wiki_id ) ) {
            wp_die( esc_html__( 'You do not have permission to access this Wiki.', 'internal-wiki-plugin' ), 403 );
        }
    }

    public function can_access( int $wiki_id ): bool {
        if ( ! get_post_meta( $wiki_id, self::META_ENABLED, true ) ) {
            return true;
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
                if ( current_user_can( $permission ) ) {
                    return true;
                }
            }
        }

        return false;
    }

    public function filter_access( bool $allowed, int $wiki_id ): bool {
        return $allowed && $this->can_access( $wiki_id );
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
        $roles = is_array( $roles ) ? array_map( 'sanitize_key', $roles ) : [];
        return array_values( array_intersect( array_unique( $roles ), array_keys( wp_roles()->roles ) ) );
    }

    private function valid_permissions( $permissions ): array {
        $permissions = is_array( $permissions ) ? array_map( 'sanitize_key', $permissions ) : [];
        return array_values( array_intersect( array_unique( $permissions ), $this->permissions() ) );
    }
}