<?php

namespace TrilBDev\WikiPress\Admin\Manager\UI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Header {
	public static function render(): void {
		$links = [
			__( 'Documentation', 'wikipress' ),
			__( 'Community', 'wikipress' ),
			__( 'Extensions', 'wikipress' ),
			__( 'Support', 'wikipress' ),
			__( 'Roadmap', 'wikipress' ),
			__( 'Account', 'wikipress' ),
		];
		?>
		<header class="wikipress-header border-bottom">
			<nav class="navbar navbar-expand-lg" aria-label="<?php esc_attr_e( 'WikiPress header navigation', 'wikipress' ); ?>">
				<div class="container-fluid wikipress-shell px-3 px-lg-4">
					<a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress' ) ); ?>">
						<img src="<?php echo esc_url( WIKIPRESS_ASSETS_URL . '/Images/Logo/WikiPressLogo.svg' ); ?>" height="300" alt="">
					</a>
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#wikipress-header-menu" aria-controls="wikipress-header-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle header navigation', 'wikipress' ); ?>">
						<span class="navbar-toggler-icon"></span>
					</button>
					<div class="collapse navbar-collapse" id="wikipress-header-menu">
						<ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
							<?php foreach ( $links as $link ) : ?>
								<li class="nav-item"><a class="nav-link" href="#"><?php echo esc_html( $link ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</nav>
		</header>
		<?php
	}
}
