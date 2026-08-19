<?php
/**
 * TrilB.Dev Plugin - User Roles Manager Admin Role Manager
 *
 * @package WikiPress
 * @subpackage Admin\Wiki\Plugins\UserRolesManager\Includes\Admin
 * @since 1.0.0
 */
namespace WikiPress\Includes\Plugins\UserRolesManager\Includes\Admin;

use WikiPress\Admin\Manager\Manager;
use WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use WikiPress\Includes\Functions\Helpers\AjaxHelper;
use WikiPress\Includes\Functions\Helpers\PermissionHelper;
use WikiPress\Includes\Functions\Helpers\RequestHelper;
use WikiPress\Includes\Functions\Helpers\SanitizationHelper;
use WikiPress\Includes\Functions\Helpers\UrlHelper;
use WikiPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class RoleManager extends Manager {
    /**
     * The slug for the Roles Manager admin page.
     */
	private const PAGE = 'wikipress-roles-manager';
	/**
	 * Registers post actions for the Roles Manager.
	 */
	public function register(): void {

		add_action( 'admin_post_wikipress_create_role', [ $this, 'create_role' ] );
		add_action( 'admin_post_wikipress_update_role', [ $this, 'update_role' ] );
		add_action( 'admin_post_wikipress_delete_role', [ $this, 'delete_role' ] );
	}
    /**
     * Renders the Roles Manager admin page.
     */
	public function render(): void {

		$roles = wp_roles()->roles;
		$capability_groups = $this->capability_groups();

		$this->header( __( 'Roles Manager', 'wikipress' ) );
		?>
		<div class="d-flex justify-content-end mb-4">
			<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#wikipress-add-role-modal"><?php esc_html_e( 'Add New', 'wikipress' ); ?></button>
		</div>
		<div class="row g-4">
			<?php foreach ( $roles as $slug => $role ) : ?>
				<div class="col-12 col-md-6 col-xl-4 d-flex">
					<article class="card wikipress-role-card shadow-sm h-100 w-100">
						<div class="card-body d-flex flex-column">
							<h2 class="h5 mb-2"><?php echo esc_html( translate_user_role( $role['name'] ) ); ?></h2>
							<p class="text-secondary mb-1"><code><?php echo esc_html( $slug ); ?></code></p>
							<p class="text-secondary mb-4"><?php /* translators: %d is the number of capabilities assigned to the role. */ echo esc_html( sprintf( _n( '%d permission', '%d permissions', count( $role['capabilities'] ), 'wikipress' ), count( $role['capabilities'] ) ) ); ?></p>
							<button type="button" class="btn btn-outline-primary mt-auto" data-bs-toggle="modal" data-bs-target="#wikipress-edit-role-<?php echo esc_attr( $slug ); ?>"><?php esc_html_e( 'Edit', 'wikipress' ); ?></button>
						</div>
					</article>
				</div>
			<?php endforeach; ?>
		</div>
		<?php $this->render_add_modal( $capability_groups ); ?>
		<?php foreach ( $roles as $slug => $role ) : $this->render_edit_modal( $slug, $role, $capability_groups ); endforeach; ?>
		<?php
		$this->footer();
	}
    /**
     * Renders the header for the Roles Manager admin page.
     *
     * @param string $title The title of the page.
     */
	public function create_role(): void {

		$this->authorize_action( 'wikipress_create_role' );
		$display_name = RequestHelper::text( $_POST, 'role_display_name' );
		$slug = $this->valid_slug( RequestHelper::key( $_POST, 'role_slug' ) );
		if ( '' === $display_name || '' === $slug || wp_roles()->is_role( $slug ) ) {
			$this->redirect( 'invalid' );
		}
		add_role( $slug, $display_name, $this->submitted_capabilities() );
		$this->redirect( 'created' );
	}
    /**
     * Updates an existing role with new display name and capabilities.
     */
	public function update_role(): void {

		$this->authorize_action( 'wikipress_update_role' );
		$old_slug = RequestHelper::key( $_POST, 'old_role_slug' );
		$display_name = RequestHelper::text( $_POST, 'role_display_name' );
		$new_slug = $this->valid_slug( RequestHelper::key( $_POST, 'role_slug' ) );
		$roles = wp_roles();
		if ( '' === $old_slug || ! $roles->is_role( $old_slug ) || '' === $display_name || '' === $new_slug || ( $old_slug !== $new_slug && $roles->is_role( $new_slug ) ) || ( $old_slug !== $new_slug && $this->role_has_users( $old_slug ) ) || ( 'administrator' === $old_slug && 'administrator' !== $new_slug ) ) {
			$this->redirect( 'invalid' );
		}
		$capabilities = $this->submitted_capabilities();
		if ( $old_slug !== $new_slug ) {
			add_role( $new_slug, $display_name, $capabilities );
			$roles->remove_role( $old_slug );
		} else {
			$role = $roles->get_role( $old_slug );
			$role->name = $display_name;
			foreach ( array_keys( $role->capabilities ) as $capability ) {
				$role->remove_cap( $capability );
			}
			foreach ( $capabilities as $capability ) {
				$role->add_cap( $capability );
			}
			$roles->roles[ $old_slug ]['name'] = $display_name;
			update_option( $roles->role_key, $roles->roles );
		}
		$this->redirect( 'updated' );
	}
    /**
     * Deletes an existing role if it has no users assigned to it.
     */
	public function delete_role(): void {
		$this->authorize_action( 'wikipress_delete_role' );
		$slug = RequestHelper::key( $_POST, 'role_slug' );
		if ( 'administrator' === $slug || ! wp_roles()->is_role( $slug ) || $this->role_has_users( $slug ) ) {
			$this->redirect( 'invalid' );
		}
		remove_role( $slug );
		$this->redirect( 'deleted' );
	}
    /**
     * Checks if a role has any users assigned to it.
     *
     * @param string $slug The role slug.
     * @return bool True if the role has users, false otherwise.
     */
	private function render_add_modal( array $groups ): void {
		$this->render_modal_start( 'wikipress-add-role-modal', __( 'Add New Role', 'wikipress' ), 'wikipress_create_role' );
		$this->render_identity_step();
		$this->render_capability_step( $groups, [] );
		$this->render_modal_end( true );
	}
    /**
     * Renders the edit role modal for a specific role.
     *
     * @param string $slug The role slug.
     * @param array $role The role data.
     * @param array $groups The capability groups.
     */
	private function render_edit_modal( string $slug, array $role, array $groups ): void {
		$id = 'wikipress-edit-role-' . $slug;
		$this->render_modal_start( $id, __( 'Edit Role', 'wikipress' ), 'wikipress_update_role' );
		?>
		<?php echo FormFieldHelper::input( 'old_role_slug', $slug, [ 'type' => 'hidden' ] ); ?>
		<div class="row g-3 mb-4">
			<div class="col-md-6"><?php echo FormFieldHelper::label( $id . '-name', __( 'Role Display Name', 'wikipress' ) ); ?><?php echo FormFieldHelper::input( 'role_display_name', $role['name'], [ 'id' => $id . '-name', 'required' => true ] ); ?></div>
			<div class="col-md-6"><?php echo FormFieldHelper::label( $id . '-slug', __( 'Role Slug', 'wikipress' ) ); ?><?php echo FormFieldHelper::input( 'role_slug', $slug, [ 'id' => $id . '-slug', 'pattern' => '[a-z_]+', 'required' => true, 'disabled' => 'administrator' === $slug ] ); ?></div>
		</div>
		<?php $this->render_capability_step( $groups, $role['capabilities'] ); ?>
		<?php $this->render_modal_end( false, 'administrator' !== $slug, $slug ); ?>
		<?php
	}
    /**
     * Renders the start of a modal dialog.
     *
     * @param string $id The modal ID.
     * @param string $title The modal title.
     * @param string $action The form action.
     */
	private function render_modal_start( string $id, string $title, string $action ): void {
		?>
		<div class="modal fade" id="<?php echo esc_attr( $id ); ?>" tabindex="-1" aria-labelledby="<?php echo esc_attr( $id ); ?>-title" aria-hidden="true">
			<div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered"><div class="modal-content"><form method="post" action="<?php echo esc_url( UrlHelper::admin_action( $action ) ); ?>" class="wikipress-role-form">
				<div class="modal-header"><h2 class="modal-title h5" id="<?php echo esc_attr( $id ); ?>-title"><?php echo esc_html( $title ); ?></h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'wikipress' ); ?>"></button></div>
				<div class="modal-body"><?php echo FormFieldHelper::input( 'action', $action, [ 'type' => 'hidden' ] ); ?><?php wp_nonce_field( $action ); ?>
		<?php
	}
    /**
     * Renders the identity step of the modal dialog.
     */
	private function render_identity_step(): void {
		?>
		<div class="wikipress-role-step" data-role-step="identity">
			<div class="row g-3"><div class="col-md-6"><?php echo FormFieldHelper::label( 'wikipress-add-role-name', __( 'Role Display Name', 'wikipress' ) ); ?><?php echo FormFieldHelper::input( 'role_display_name', '', [ 'id' => 'wikipress-add-role-name', 'required' => true ] ); ?></div><div class="col-md-6"><?php echo FormFieldHelper::label( 'wikipress-add-role-slug', __( 'Role Slug', 'wikipress' ) ); ?><?php echo FormFieldHelper::input( 'role_slug', '', [ 'id' => 'wikipress-add-role-slug', 'pattern' => '[a-z_]+', 'title' => __( 'Use lowercase letters and underscores only.', 'wikipress' ), 'required' => true ] ); ?></div></div>
		</div>
		<?php
	}
    /**
     * Renders the capability selection step of the modal dialog.
     *
     * @param array $groups The capability groups.
     * @param array $selected The selected capabilities.
     */
	private function render_capability_step( array $groups, array $selected ): void {
		?>
		<div class="wikipress-role-step" data-role-step="capabilities"><h3 class="h6 mb-3"><?php esc_html_e( 'Capabilities', 'wikipress' ); ?></h3><div class="accordion" id="wikipress-capability-groups-<?php echo esc_attr( wp_rand() ); ?>">
			<?php $index = 0; foreach ( $groups as $label => $capabilities ) : $index++; $collapse_id = 'wikipress-capability-group-' . wp_rand(); ?>
				<div class="accordion-item"><h4 class="accordion-header"><button class="accordion-button <?php echo 1 === $index ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo esc_attr( $collapse_id ); ?>"><?php echo esc_html( $label ); ?></button></h4><div id="<?php echo esc_attr( $collapse_id ); ?>" class="accordion-collapse collapse <?php echo 1 === $index ? 'show' : ''; ?>"><div class="accordion-body"><div class="row g-2">
				<?php foreach ( $capabilities as $capability => $description ) : ?><div class="col-12 col-sm-6 col-lg-4 col-xl-2"><div class="card h-100 p-2 wikipress-capability-card"><?php echo FormFieldHelper::checkbox( 'capabilities[]', $capability, $description ?: $capability, [ 'class' => 'mt-1', 'checked' => ! empty( $selected[ $capability ] ), 'wrapper_class' => 'd-flex align-items-start gap-2 mb-0' ] ); ?></div></div><?php endforeach; ?>
				</div></div></div></div>
			<?php endforeach; ?>
		</div></div>
		<?php
	}
    /**
     * Renders the end of a modal dialog.
     *
     * @param bool $multi_step Whether the modal has multiple steps.
     * @param bool $allow_delete Whether to show the delete button.
     * @param string $slug The role slug (for delete action).
     */
	private function render_modal_end( bool $multi_step, bool $allow_delete = false, string $slug = '' ): void {
		?>
				</div><div class="modal-footer justify-content-between"><div><?php if ( $allow_delete ) : ?><button type="submit" class="btn btn-outline-danger" formaction="<?php echo esc_url( UrlHelper::admin_action( 'wikipress_delete_role' ) ); ?>" formmethod="post" name="action" value="wikipress_delete_role" data-role-slug="<?php echo esc_attr( $slug ); ?>" onclick="this.form.querySelector('[name=role_slug]').value = this.dataset.roleSlug; this.form.querySelector('[name=_wpnonce]').value = '<?php echo esc_js( wp_create_nonce( 'wikipress_delete_role' ) ); ?>'; return confirm('<?php echo esc_js( __( 'Delete this role?', 'wikipress' ) ); ?>');"><?php esc_html_e( 'Delete', 'wikipress' ); ?></button><?php endif; ?></div><div class="d-flex gap-2"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php esc_html_e( 'Cancel', 'wikipress' ); ?></button><button type="button" class="btn btn-outline-primary <?php echo $multi_step ? '' : 'd-none'; ?>" data-role-back><?php esc_html_e( 'Back', 'wikipress' ); ?></button><button type="button" class="btn btn-primary <?php echo $multi_step ? '' : 'd-none'; ?>" data-role-next disabled><?php esc_html_e( 'Next', 'wikipress' ); ?></button><button type="submit" class="btn btn-primary" data-role-save><?php esc_html_e( 'Save', 'wikipress' ); ?></button></div></div>
			</form></div></div>
		</div>
		<?php
	}
    /**
     * Redirects to the Roles Manager page with a status message.
     *
     * @param string $status The status message key.
     */
	private function capability_groups(): array {
		$groups = [
			'Multisite' => is_multisite() ? [ 'manage_network' => __( 'Manage Network', 'wikipress' ), 'manage_sites' => __( 'Manage Sites', 'wikipress' ), 'manage_network_users' => __( 'Manage Network Users', 'wikipress' ), 'manage_network_plugins' => __( 'Manage Network Plugins', 'wikipress' ), 'manage_network_themes' => __( 'Manage Network Themes', 'wikipress' ) ] : [],
			'Manage' => [ 'manage_options' => __( 'Manage Options', 'wikipress' ), 'manage_categories' => __( 'Manage Categories', 'wikipress' ), 'edit_users' => __( 'Edit Users', 'wikipress' ), 'list_users' => __( 'List Users', 'wikipress' ), 'promote_users' => __( 'Promote Users', 'wikipress' ), 'create_users' => __( 'Create Users', 'wikipress' ), 'delete_users' => __( 'Delete Users', 'wikipress' ) ],
			'Content' => [ 'read' => __( 'Read', 'wikipress' ), 'edit_posts' => __( 'Edit Posts', 'wikipress' ), 'edit_pages' => __( 'Edit Pages', 'wikipress' ), 'publish_posts' => __( 'Publish Posts', 'wikipress' ), 'publish_pages' => __( 'Publish Pages', 'wikipress' ), 'delete_posts' => __( 'Delete Posts', 'wikipress' ), 'upload_files' => __( 'Upload Files', 'wikipress' ) ],
			'WikiPress' => [ 'create_wikis' => __( 'Create Wikis', 'wikipress' ), 'write_pages' => __( 'Write Wiki Pages', 'wikipress' ), 'view_analytics' => __( 'View Analytics', 'wikipress' ), 'manage_plugins' => __( 'Manage WikiPress Plugins', 'wikipress' ) ],
		];
		$known = [];
		foreach ( $groups as $capabilities ) {
			$known = array_merge( $known, array_keys( $capabilities ) );
		}
		$plugin_capabilities = [];
		foreach ( wp_roles()->roles as $role ) {
			foreach ( array_keys( $role['capabilities'] ) as $capability ) {
				if ( ! in_array( $capability, $known, true ) && ! str_starts_with( $capability, 'level_' ) ) {
					$plugin_capabilities[ $capability ] = ucwords( str_replace( [ '_', '-' ], ' ', $capability ) );
				}
			}
		}
		ksort( $plugin_capabilities, SORT_NATURAL | SORT_FLAG_CASE );
        
		if ( $plugin_capabilities ) {

			$groups['Plugins'] = $plugin_capabilities;

		}

		return array_filter( $groups );
	}
    /**
     * Collects the capabilities submitted from the form.
     *
     * @return array The submitted capabilities.
     */
	private function submitted_capabilities(): array {

		$capabilities = array_map( [ SanitizationHelper::class, 'key' ], RequestHelper::array( $_POST, 'capabilities' ) );
		$capabilities = array_unique( array_filter( $capabilities ) );

		return array_fill_keys( $capabilities, true );
	}
    /**
     * Checks if a role has any users assigned to it.
     *
     * @param string $slug The role slug.
     * @return bool True if the role has users, false otherwise.
     */
	private function role_has_users( string $slug ): bool {

		return ! empty( get_users( [ 'role' => $slug, 'fields' => 'ID', 'number' => 1 ] ) );

	}
    /**
     * Validates a role slug.
     *
     * @param string $value The role slug to validate.
     * @return string The sanitized role slug, or an empty string if invalid.
     */
	private function valid_slug( $value ): string {

		$slug = strtolower( SanitizationHelper::text( $value ) );

		return preg_match( '/^[a-z_]+$/', $slug ) ? $slug : '';

	}
    /**
     * Authorizes an action by checking user capabilities and nonce.
     *
     * @param string $action The action to authorize.
     */
	private function authorize_action( string $action ): void {

		if ( ! PermissionHelper::can( Settings::get_key( 'role_manager_capability', 'manage_options' ) ) ) {

			wp_die( esc_html__( 'You are not allowed to manage roles.', 'wikipress' ), 403 );

		}

		if ( ! check_admin_referer( $action, '_wpnonce', false ) ) {
			wp_die( esc_html__( 'Security check failed.', 'wikipress' ), 403 );
		}
	}
    /**
     * Redirects to the Roles Manager page with a status message.
     *
     * @param string $status The status message key.
     */
	private function redirect( string $status ): void {

		wp_safe_redirect( add_query_arg( [ 'page' => self::PAGE, 'role_status' => $status ], admin_url( 'users.php' ) ) );

		exit;

	}
}