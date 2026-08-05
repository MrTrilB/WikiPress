<?php

namespace TrilBDev\WikiPress\Admin\Manager\UI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Sidebar {
	public static function render(): void {
		$current = sanitize_key( $_GET['page'] ?? 'wikipress' );
		$groups = [
			'content' => [
				'label' => __( 'Content', 'wikipress' ),
				'icon' => 'fa-solid fa-file-lines',
				'items' => [
					'wikipress-manage' => [ 'label' => __( 'Manage Wiki', 'wikipress' ), 'icon' => 'fa-solid fa-book-open' ],
				],
			],
			'manage' => [
				'label' => __( 'Manage', 'wikipress' ),
				'icon' => 'fa-solid fa-sliders',
				'items' => [
					'wikipress-analytics' => [ 'label' => __( 'Analytics', 'wikipress' ), 'icon' => 'fa-solid fa-chart-line' ],
				],
			],
			'settings' => [
				'label' => __( 'Settings', 'wikipress' ),
				'icon' => 'fa-solid fa-gear',
				'items' => [
					'wikipress-settings&tab=general' => [ 'label' => __( 'General', 'wikipress' ), 'icon' => 'fa-solid fa-sliders' ],
					'wikipress-settings&tab=layout' => [ 'label' => __( 'Layout', 'wikipress' ), 'icon' => 'fa-solid fa-table-columns' ],
					'wikipress-settings&tab=plugins' => [ 'label' => __( 'Plugins', 'wikipress' ), 'icon' => 'fa-solid fa-puzzle-piece' ],
					'wikipress-settings&tab=third-party' => [ 'label' => __( '3rd Party', 'wikipress' ), 'icon' => 'fa-solid fa-plug' ],
					'wikipress-settings&tab=access' => [ 'label' => __( 'Access', 'wikipress' ), 'icon' => 'fa-solid fa-user-shield' ],
					'wikipress-settings&tab=tools' => [ 'label' => __( 'Tools', 'wikipress' ), 'icon' => 'fa-solid fa-screwdriver-wrench' ],
				],
			],
		];
		?>
		<aside class="col-12 col-lg-3 col-xl-2">
			<div class="wikipress-sidebar position-sticky" style="top: 32px;">
				<div class="d-flex align-items-center justify-content-between mb-3 px-2">
					<span class="small text-uppercase fw-semibold text-secondary"><?php esc_html_e( 'Navigate', 'wikipress' ); ?></span>
					<span class="badge rounded-pill text-bg-light">WP</span>
				</div>
				<nav aria-label="<?php esc_attr_e( 'WikiPress admin navigation', 'wikipress' ); ?>">
					<a class="wikipress-sidebar-link <?php echo 'wikipress' === $current ? 'active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress' ) ); ?>">
						<span class="wikipress-sidebar-icon" aria-hidden="true"><i class="fa-solid fa-house"></i></span><?php esc_html_e( 'Dashboard', 'wikipress' ); ?>
					</a>
					<div id="wikipress-sidebar-groups">
						<?php foreach ( $groups as $key => $group ) : $expanded = 'settings' === $key ? 'wikipress-settings' === $current : in_array( $current, array_keys( $group['items'] ), true ); ?>
							<div class="wikipress-sidebar-group">
								<button class="wikipress-sidebar-link wikipress-sidebar-group-link border-0 bg-transparent w-100 text-start <?php echo $expanded ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#wikipress-group-<?php echo esc_attr( $key ); ?>" aria-expanded="<?php echo $expanded ? 'true' : 'false'; ?>" aria-controls="wikipress-group-<?php echo esc_attr( $key ); ?>">
									<span class="wikipress-sidebar-icon" aria-hidden="true"><i class="<?php echo esc_attr( $group['icon'] ); ?>"></i></span><?php echo esc_html( $group['label'] ); ?><span class="ms-auto text-secondary"><?php echo count( $group['items'] ); ?></span>
								</button>
								<div id="wikipress-group-<?php echo esc_attr( $key ); ?>" class="collapse <?php echo $expanded ? 'show' : ''; ?>">
									<div class="nav flex-column">
										<?php foreach ( $group['items'] as $slug => $item ) : $active_tab = sanitize_key( $_GET['tab'] ?? 'general' ); $is_active = str_starts_with( $slug, 'wikipress-settings&tab=' ) ? ( 'wikipress-settings' === $current && str_ends_with( $slug, $active_tab ) ) : $current === $slug; ?>
											<a class="nav-link <?php echo $is_active ? 'active' : ''; ?>" <?php echo $is_active ? 'aria-current="page"' : ''; ?> href="<?php echo esc_url( admin_url( 'admin.php?page=' . $slug ) ); ?>"><i class="<?php echo esc_attr( $item['icon'] ); ?> me-2" aria-hidden="true"></i><?php echo esc_html( $item['label'] ); ?></a>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</nav>
			</div>
		</aside>
		<?php
	}
}
