<?php

namespace TrilBDev\WikiPress\Includes\Functions\Admin;

use TrilBDev\WikiPress\Includes\Core\PostType;
use TrilBDev\WikiPress\Includes\Core\Taxonomy;
use TrilBDev\WikiPress\Includes\Functions\Helpers\AjaxHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\PostHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\QueryHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\SanitizationHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\TaxonomyHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class FunctionsWiki {
	public function save_wiki(): string {
		if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' ) || 'create_wiki' !== ( $_POST['wikipress_action'] ?? '' ) ) {
			return '';
		}
		if ( ! current_user_can( 'publish_posts' ) || ! check_admin_referer( 'wikipress_create_wiki', 'wikipress_create_wiki_nonce' ) ) {
			return '<div class="notice notice-error"><p>' . esc_html__( 'You are not authorized to create a Wiki.', 'wikipress' ) . '</p></div>';
		}

		$input = wp_unslash( $_POST['wikipress_wiki'] ?? [] );
		$input = is_array( $input ) ? $input : [];
		$input['categories'] = is_array( $input['categories'] ?? null ) ? $input['categories'] : [];
		$input['tags'] = is_array( $input['tags'] ?? null ) ? $input['tags'] : [];
		if ( '' === SanitizationHelper::text( $input['name'] ?? '' ) ) {
			return '<div class="notice notice-error"><p>' . esc_html__( 'Wiki Name is required.', 'wikipress' ) . '</p></div>';
		}
		$new_category = SanitizationHelper::text( $input['new_category'] ?? '' );
		if ( '' !== $new_category ) {
			$category_result = wp_insert_term( $new_category, Taxonomy::CATEGORY, [ 'parent' => SanitizationHelper::integer( $input['new_category_parent'] ?? 0 ) ] );
			if ( ! is_wp_error( $category_result ) ) {
				$input['categories'][] = $category_result['term_id'];
			}
		}
		$payload = [
			'title' => SanitizationHelper::text( $input['name'] ?? '' ),
			'slug' => SanitizationHelper::slug( $input['slug'] ?? '' ),
			'excerpt' => wp_kses_post( (string) ( $input['excerpt'] ?? '' ) ),
			'content' => wp_kses_post( (string) ( $input['description'] ?? '' ) ),
			'categories' => TaxonomyHelper::ids( $input['categories'] ?? [] ),
			'tags' => TaxonomyHelper::resolve_ids( $input['tags'] ?? [], Taxonomy::TAG, true ),
			'status' => 'publish',
			'navigation' => SanitizationHelper::one_of( SanitizationHelper::key( $input['navigation'] ?? 'horizontal', 'horizontal' ), [ 'horizontal', 'vertical' ], 'horizontal' ),
			'thumbnail_id' => SanitizationHelper::integer( $input['thumbnail_id'] ?? 0 ),
			'logo_id' => SanitizationHelper::integer( $input['logo_id'] ?? 0 ),
		];
		$payload = apply_filters( 'wikipress_wiki_payload', $payload, null );
		if ( '' === $payload['title'] ) {
			return '<div class="notice notice-error"><p>' . esc_html__( 'Wiki Name is required.', 'wikipress' ) . '</p></div>';
		}

		$post_id = wp_insert_post( [
			'post_type' => PostType::WIKI,
			'post_title' => $payload['title'],
			'post_name' => $payload['slug'],
			'post_excerpt' => $payload['excerpt'],
			'post_content' => $payload['content'],
			'post_status' => $payload['status'],
			'post_author' => get_current_user_id(),
		], true );
		if ( is_wp_error( $post_id ) ) {
			return '<div class="notice notice-error"><p>' . esc_html( $post_id->get_error_message() ) . '</p></div>';
		}

		wp_set_post_terms( $post_id, $payload['categories'], Taxonomy::CATEGORY, false );
		wp_set_post_terms( $post_id, $payload['tags'], Taxonomy::TAG, false );
		if ( ! empty( $payload['thumbnail_id'] ) ) {
			set_post_thumbnail( $post_id, $payload['thumbnail_id'] );
		}
		if ( ! empty( $payload['logo_id'] ) ) {
			update_post_meta( $post_id, '_wikipress_logo_id', $payload['logo_id'] );
		}
		update_post_meta( $post_id, '_wikipress_navigation_style', $payload['navigation'] );
		do_action( 'wikipress_wiki_saved', $post_id, $payload );
		wp_safe_redirect( admin_url( 'admin.php?page=wikipress-manage' ) );
		exit;
	}

	public function save_wiki_settings(): void {
		$wiki_id = SanitizationHelper::integer( $_POST['wiki_id'] ?? 0 );
		if ( ! AjaxHelper::authorized( 'wikipress_manage_wiki', 'edit_post', $wiki_id ) || ! PostHelper::is_wiki( $wiki_id ) ) {
			AjaxHelper::unauthorized( __( 'You are not authorized to update this Wiki.', 'wikipress' ) );
		}
		$settings = wp_unslash( $_POST['settings'] ?? [] );
		$settings = is_string( $settings ) ? json_decode( $settings, true ) : $settings;
		$settings = is_array( $settings ) ? $settings : [];
		$name = SanitizationHelper::text( $settings['name'] ?? '' );
		if ( '' === $name ) {
			AjaxHelper::error( [ 'message' => __( 'Wiki Name is required.', 'wikipress' ) ] );
		}
		$updated = wp_update_post( [ 'ID' => $wiki_id, 'post_title' => $name, 'post_name' => SanitizationHelper::slug( $settings['slug'] ?? $name, $name ) ], true );
		if ( is_wp_error( $updated ) ) {
			AjaxHelper::error( [ 'message' => $updated->get_error_message() ] );
		}
		$navigation = SanitizationHelper::one_of( SanitizationHelper::key( $settings['navigation'] ?? 'horizontal', 'horizontal' ), [ 'horizontal', 'vertical' ], 'horizontal' );
		update_post_meta( $wiki_id, '_wikipress_navigation_style', $navigation );
		AjaxHelper::success( [ 'message' => __( 'Wiki settings saved.', 'wikipress' ) ] );
	}

	public function delete_wiki(): void {
		$wiki_id = SanitizationHelper::integer( $_POST['wiki_id'] ?? 0 );
		if ( ! AjaxHelper::authorized( 'wikipress_manage_wiki', 'delete_post', $wiki_id ) || ! PostHelper::is_wiki( $wiki_id ) ) {
			AjaxHelper::unauthorized( __( 'You are not authorized to delete this Wiki.', 'wikipress' ) );
		}
		$pages = QueryHelper::posts( [ 'post_type' => PostType::PAGE, 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_wikipress_wiki_id', 'meta_value' => $wiki_id ] )->posts;
		foreach ( $pages as $page_id ) {
			wp_delete_post( $page_id, true );
		}
		wp_delete_post( $wiki_id, true );
		AjaxHelper::success( [ 'message' => __( 'Wiki deleted.', 'wikipress' ) ] );
	}

	public function delete_wiki_page(): void {
		$page_id = SanitizationHelper::integer( $_POST['page_id'] ?? 0 );
		if ( ! AjaxHelper::authorized( 'wikipress_manage_wiki', 'delete_post', $page_id ) || ! PostHelper::is_wiki_page( $page_id ) ) {
			AjaxHelper::unauthorized( __( 'You are not authorized to delete this Wiki Page.', 'wikipress' ) );
		}
		wp_delete_post( $page_id, true );
		AjaxHelper::success( [ 'message' => __( 'Wiki Page deleted.', 'wikipress' ) ] );
	}

	public function save_wiki_term(): void {
		$wiki_id = SanitizationHelper::integer( $_POST['wiki_id'] ?? 0 );
		$term_id = SanitizationHelper::integer( $_POST['term_id'] ?? 0 );
		$taxonomy = SanitizationHelper::key( $_POST['taxonomy'] ?? '' );
		if ( ! AjaxHelper::authorized( 'wikipress_manage_wiki', 'edit_post', $wiki_id ) || ! PostHelper::is_wiki( $wiki_id ) || ! in_array( $taxonomy, [ Taxonomy::CATEGORY, Taxonomy::TAG ], true ) ) {
			AjaxHelper::unauthorized( __( 'You are not authorized to manage Wiki terms.', 'wikipress' ) );
		}
		$args = [ 'slug' => SanitizationHelper::slug( $_POST['slug'] ?? '' ), 'description' => SanitizationHelper::textarea( $_POST['description'] ?? '' ) ];
		$name = SanitizationHelper::text( $_POST['name'] ?? '' );
		$result = $term_id ? wp_update_term( $term_id, $taxonomy, array_merge( [ 'name' => $name ], $args ) ) : wp_insert_term( $name, $taxonomy, $args );
		if ( is_wp_error( $result ) ) {
			AjaxHelper::error( [ 'message' => $result->get_error_message() ] );
		}
		$term_id = $term_id ?: SanitizationHelper::integer( $result['term_id'] ?? 0 );
		$term_ids = TaxonomyHelper::ids( TaxonomyHelper::terms( $taxonomy, $wiki_id ) );
		if ( ! in_array( $term_id, $term_ids, true ) ) {
			$term_ids[] = $term_id;
		}
		wp_set_post_terms( $wiki_id, $term_ids, $taxonomy, false );
		AjaxHelper::success( [ 'message' => __( 'Term saved.', 'wikipress' ) ] );
	}

	public function delete_wiki_term(): void {
		$wiki_id = SanitizationHelper::integer( $_POST['wiki_id'] ?? 0 );
		$term_id = SanitizationHelper::integer( $_POST['term_id'] ?? 0 );
		$taxonomy = SanitizationHelper::key( $_POST['taxonomy'] ?? '' );
		if ( ! AjaxHelper::authorized( 'wikipress_manage_wiki', 'edit_post', $wiki_id ) || ! PostHelper::is_wiki( $wiki_id ) || ! in_array( $taxonomy, [ Taxonomy::CATEGORY, Taxonomy::TAG ], true ) ) {
			AjaxHelper::unauthorized( __( 'You are not authorized to manage Wiki terms.', 'wikipress' ) );
		}
		$term_ids = array_diff( TaxonomyHelper::ids( TaxonomyHelper::terms( $taxonomy, $wiki_id ) ), [ $term_id ] );
		wp_set_post_terms( $wiki_id, $term_ids, $taxonomy, false );
		AjaxHelper::success( [ 'message' => __( 'Term removed from this Wiki.', 'wikipress' ) ] );
	}
}
