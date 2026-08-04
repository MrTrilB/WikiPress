<?php

namespace TrilBDev\WikiPress\Admin\Manager\Content;

use TrilBDev\WikiPress\Admin\Manager\Manager;
use TrilBDev\WikiPress\Assets\Assets;
use TrilBDev\WikiPress\Includes\Core\PostType;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ContentManager extends Manager {
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, [ 'wikipress-manage' ], 'content' );
	}

	public function render(): void {
		$this->header( __( 'Manage Wiki', 'wikipress' ) );
		$wikis = get_posts( [
			'post_type'      => PostType::WIKI,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		] );
		?>
		<div class="row g-4">
			<?php if ( $wikis ) : ?>
				<?php foreach ( $wikis as $wiki ) : ?>
					<?php $this->render_wiki_card( $wiki ); ?>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="col-12">
					<div class="card border-0 shadow-sm">
						<div class="card-body p-4">
							<h2 class="h5"><?php esc_html_e( 'No Wikis created yet', 'wikipress' ); ?></h2>
							<p class="text-secondary mb-3"><?php esc_html_e( 'Create your first Wiki to start organising your knowledge.', 'wikipress' ); ?></p>
							<a class="btn btn-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress-manage' ) ); ?>"><?php esc_html_e( 'Manage Wikis', 'wikipress' ); ?></a>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
		$this->footer();
	}

	private function render_wiki_card( \WP_Post $wiki ): void {
		$page_count = ( new \WP_Query( [
			'post_type'      => PostType::PAGE,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => false,
			'meta_key'       => '_wikipress_wiki_id',
			'meta_value'     => $wiki->ID,
		] ) )->found_posts;
		$image_url = get_the_post_thumbnail_url( $wiki, 'medium' );
		$author_url = get_avatar_url( $wiki->post_author, [ 'size' => 64 ] );
		$description = wp_trim_words( wp_strip_all_tags( $wiki->post_content ), 24 );
		$manage_url = admin_url( 'admin.php?page=wikipress-manage' );
		?>
		<div class="col-12 col-md-6 col-xl-4 d-flex">
			<article class="card wikipress-wiki-card text-start shadow-sm h-100 w-100">
				<div class="card-header fw-semibold"><?php echo esc_html( get_the_title( $wiki ) ); ?></div>
				<div class="card-body d-flex flex-column">
					<?php if ( $image_url ) : ?>
						<img src="<?php echo esc_url( $image_url ); ?>" class="wikipress-wiki-image rounded mx-auto d-block mb-3" alt="<?php echo esc_attr( get_the_title( $wiki ) ); ?>">
					<?php else : ?>
						<div class="wikipress-wiki-image wikipress-wiki-image-placeholder rounded mx-auto d-flex align-items-center justify-content-center mb-3" aria-hidden="true"><span class="dashicons dashicons-book-alt"></span></div>
					<?php endif; ?>
					<p class="card-text text-secondary"><?php echo esc_html( $description ?: __( 'No description provided yet.', 'wikipress' ) ); ?></p>
					<div class="d-flex align-items-center gap-2 mb-3">
						<img src="<?php echo esc_url( $author_url ); ?>" class="wikipress-author-image rounded-circle" alt="">
						<p class="card-text mb-0"><span class="text-secondary"><?php esc_html_e( 'Author:', 'wikipress' ); ?></span> <?php echo esc_html( get_the_author_meta( 'display_name', $wiki->post_author ) ); ?></p>
					</div>
					<p class="card-text mb-2"><span class="text-secondary"><?php esc_html_e( 'Created:', 'wikipress' ); ?></span> <?php echo esc_html( get_the_date( '', $wiki ) ); ?></p>
					<div class="d-flex justify-content-between gap-3 mt-auto">
						<p class="card-text mb-0"><span class="text-secondary"><?php esc_html_e( 'Pages:', 'wikipress' ); ?></span> <?php echo esc_html( number_format_i18n( $page_count ) ); ?></p>
						<p class="card-text mb-0"><span class="text-secondary"><?php esc_html_e( 'Visitors:', 'wikipress' ); ?></span> 0</p>
					</div>
				</div>
				<div class="card-footer text-body-secondary"><a href="<?php echo esc_url( $manage_url ); ?>" class="btn btn-primary"><?php esc_html_e( 'Manage Wiki', 'wikipress' ); ?></a></div>
			</article>
		</div>
		<?php
	}
}
