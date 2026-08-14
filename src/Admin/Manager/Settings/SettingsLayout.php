<?php
/**
 * Settings layout fields.
 *
 * @package TrilBDev
 * @subpackage Admin\Manager\Settings
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Admin\Manager\Settings;

use TrilBDev\WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsLayout {
	/**
	 * Render layout settings fields.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 */
	public function render( array $values, string $section = 'general' ): void {
		$sections = [
			'general' => __( 'General', 'wikipress' ),
			'search' => __( 'Wiki Search', 'wikipress' ),
			'sidebar' => __( 'Wiki Sidebar', 'wikipress' ),
			'page' => __( 'Wiki Page', 'wikipress' ),
		];
		$section = isset( $sections[ $section ] ) ? $section : 'general';
		echo '<nav aria-label="' . esc_attr__( 'Layout settings sections', 'wikipress' ) . '"><div class="nav nav-tabs" id="wikipress-layout-tab" role="tablist">';
		foreach ( $sections as $slug => $label ) {
			$target = 'wikipress-layout-' . $slug;
			echo FormFieldHelper::button( $label, [
				'class' => 'nav-link ' . ( $section === $slug ? 'active' : '' ),
				'attributes' => [
					'id' => $target . '-tab',
					'data-bs-toggle' => 'tab',
					'data-bs-target' => '#' . $target,
					'role' => 'tab',
					'aria-controls' => $target,
					'aria-selected' => $section === $slug ? 'true' : 'false',
					'data-wikipress-layout-tab' => $slug,
				],
			] ) );
		}
		echo '</div></nav><div class="tab-content" id="wikipress-layout-tab-content">';
		foreach ( $sections as $slug => $label ) {
			$target = 'wikipress-layout-' . $slug;
			echo '<div class="tab-pane fade ' . ( $section === $slug ? 'show active' : '' ) . '" id="' . esc_attr( $target ) . '" role="tabpanel" aria-labelledby="' . esc_attr( $target . '-tab' ) . '" tabindex="0"><table class="form-table table align-middle"><tbody>';
			$this->render_fields( $values, $slug );
			echo '</tbody></table></div>';
		}
		echo '</div>';
	}
	/**
	 * Render the fields for a specific section.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @param string $section Section slug.
	 * @return void
	 */
	private function render_fields( array $values, string $section ): void {
		$fields = $this->fields( $section );
		foreach ( $fields as $key => $field ) {
			$key = SanitizationHelper::key( $key );
			$id = 'wikipress-layout-' . $section . '-' . $key;
			$name = 'wikipress_layout[' . $key . ']';
			$type = $field['type'] ?? 'checkbox';
			echo '<tr><th scope="row">' . FormFieldHelper::label( $id, $field['label'], $field ) . '</th><td>';
			if ( 'select' === $type ) {
				echo FormFieldHelper::select( $name, $field['options'], $values[ $key ] ?? $field['default'], [ 'id' => $id ] );
			} elseif ( 'number' === $type ) {
				echo FormFieldHelper::input( $name, (string) ( $values[ $key ] ?? $field['default'] ), [ 'id' => $id, 'type' => 'number', 'min' => $field['min'], 'max' => $field['max'] ] );
			} elseif ( 'text' === $type ) {
				echo FormFieldHelper::input( $name, SanitizationHelper::text( $values[ $key ] ?? $field['default'] ), [ 'id' => $id, 'type' => 'text' ] );
			} else {
				echo FormFieldHelper::checkbox( $name, '1', $field['label'], [ 'id' => $id, 'checked' => ! empty( $values[ $key ] ?? $field['default'] ) ] );
			}
			echo '</td></tr>';
		}
	}
	/**
	 * Get the fields for a specific section.
	 *
	 * @param string $section Section slug.
	 * @return array<string, mixed> Fields for the section.
	 */
	private function fields( string $section ): array {
		$toggle = static fn( string $label, string $description, string $tooltip, bool $default = false ): array => [ 'label' => $label, 'description' => $description, 'tooltip' => $tooltip, 'default' => $default ];
		return match ( $section ) {
			'search' => [
				'show_search' => $toggle( __( 'Enable Wiki Search', 'wikipress' ), __( 'Display search controls throughout the Wiki.', 'wikipress' ), __( 'Visitors can search Wiki pages without browsing the sidebar.', 'wikipress' ), true ),
				'search_placeholder' => [ 'label' => __( 'Search Placeholder', 'wikipress' ), 'description' => __( 'Text shown before a visitor enters a search term.', 'wikipress' ), 'tooltip' => __( 'Keep this short so it fits comfortably in the search field.', 'wikipress' ), 'type' => 'text', 'default' => 'Search the Wiki' ],
				'search_button_text' => [ 'label' => __( 'Search Button Text', 'wikipress' ), 'description' => __( 'Text shown on the search submit button.', 'wikipress' ), 'tooltip' => __( 'Use a clear action such as Search.', 'wikipress' ), 'type' => 'text', 'default' => 'Search' ],
				'search_scope' => [ 'label' => __( 'Search Scope', 'wikipress' ), 'description' => __( 'Choose which parts of Wiki pages are searched.', 'wikipress' ), 'tooltip' => __( 'Searching titles and content gives visitors the broadest results.', 'wikipress' ), 'type' => 'select', 'options' => [ 'all' => __( 'Titles and Content', 'wikipress' ), 'title' => __( 'Titles Only', 'wikipress' ), 'content' => __( 'Content Only', 'wikipress' ) ], 'default' => 'all' ],
				'search_no_results_message' => [ 'label' => __( 'No Results Message', 'wikipress' ), 'description' => __( 'Message shown when a Wiki search returns no results.', 'wikipress' ), 'tooltip' => __( 'Give visitors a useful next step instead of leaving the result area empty.', 'wikipress' ), 'type' => 'text', 'default' => 'No Wiki pages found.' ],
				'search_results_count' => [ 'label' => __( 'Search Results Per Page', 'wikipress' ), 'description' => __( 'Maximum number of results shown for one search.', 'wikipress' ), 'tooltip' => __( 'Use a smaller value for compact result lists.', 'wikipress' ), 'type' => 'number', 'default' => 10, 'min' => 1, 'max' => 50 ],
				'search_min_chars' => [ 'label' => __( 'Minimum Search Characters', 'wikipress' ), 'description' => __( 'Minimum number of characters required before a search is performed.', 'wikipress' ), 'tooltip' => __( 'A small minimum prevents noisy searches from very short terms.', 'wikipress' ), 'type' => 'number', 'default' => 2, 'min' => 1, 'max' => 5 ],
				'search_live_results' => $toggle( __( 'Live Search Results', 'wikipress' ), __( 'Update suggestions while visitors type.', 'wikipress' ), __( 'Live results make large Wikis easier to browse.', 'wikipress' ), true ),
			],
			'sidebar' => [
				'show_sidebar' => $toggle( __( 'Show Wiki Sidebar', 'wikipress' ), __( 'Display the Wiki navigation sidebar on Wiki pages.', 'wikipress' ), __( 'The sidebar is the primary navigation structure for WikiPress.', 'wikipress' ), true ),
				'sidebar_position' => [ 'label' => __( 'Sidebar Position', 'wikipress' ), 'description' => __( 'Choose which side of the page contains the Wiki navigation.', 'wikipress' ), 'tooltip' => __( 'Use the position that best matches your site layout.', 'wikipress' ), 'type' => 'select', 'options' => [ 'left' => __( 'Left', 'wikipress' ), 'right' => __( 'Right', 'wikipress' ) ], 'default' => 'left' ],
				'sidebar_width' => [ 'label' => __( 'Sidebar Width', 'wikipress' ), 'description' => __( 'Set the preferred width of the Wiki navigation sidebar in pixels.', 'wikipress' ), 'tooltip' => __( 'Allow enough room for long category names without crowding the content.', 'wikipress' ), 'type' => 'number', 'default' => 280, 'min' => 180, 'max' => 480 ],
				'sidebar_sticky' => $toggle( __( 'Sticky Sidebar', 'wikipress' ), __( 'Keep the Wiki navigation visible while the page scrolls.', 'wikipress' ), __( 'Sticky navigation is useful for long Wiki pages.', 'wikipress' ), true ),
				'sidebar_show_categories' => $toggle( __( 'Show Categories', 'wikipress' ), __( 'Display Wiki categories in the navigation.', 'wikipress' ), __( 'Disable this when navigation is provided by a custom component.', 'wikipress' ), true ),
				'sidebar_show_category_count' => $toggle( __( 'Show Category Counts', 'wikipress' ), __( 'Display the number of pages in each category.', 'wikipress' ), __( 'Counts help visitors understand the size of each section.', 'wikipress' ) ),
				'sidebar_expand_categories' => $toggle( __( 'Expand Categories', 'wikipress' ), __( 'Show child categories when the sidebar loads.', 'wikipress' ), __( 'Disable this for Wikis with many nested categories.', 'wikipress' ), true ),
				'sidebar_show_page_count' => $toggle( __( 'Show Page Counts', 'wikipress' ), __( 'Display page counts beside Wiki navigation items.', 'wikipress' ), __( 'Page counts provide a quick overview of navigation depth.', 'wikipress' ) ),
			],
			'page' => [
				'page_show_title' => $toggle( __( 'Show Page Title', 'wikipress' ), __( 'Display the Wiki page title above its content.', 'wikipress' ), __( 'Disable this when your page template already renders the title.', 'wikipress' ), true ),
				'show_breadcrumbs' => $toggle( __( 'Show Breadcrumbs', 'wikipress' ), __( 'Show the Wiki hierarchy above the page content.', 'wikipress' ), __( 'Breadcrumbs help visitors understand where the current page belongs.', 'wikipress' ), true ),
				'page_show_toc' => $toggle( __( 'Show Table of Contents', 'wikipress' ), __( 'Display a table of contents generated from page headings.', 'wikipress' ), __( 'The table of contents helps visitors scan long pages.', 'wikipress' ), true ),
				'page_toc_position' => [ 'label' => __( 'Table of Contents Position', 'wikipress' ), 'description' => __( 'Choose where the page table of contents appears.', 'wikipress' ), 'tooltip' => __( 'Place it in the sidebar to keep navigation together or in the content area for a more compact layout.', 'wikipress' ), 'type' => 'select', 'options' => [ 'sidebar' => __( 'Wiki Sidebar', 'wikipress' ), 'content' => __( 'Above Page Content', 'wikipress' ) ], 'default' => 'sidebar' ],
				'toc_min_level' => [ 'label' => __( 'TOC Minimum Heading', 'wikipress' ), 'description' => __( 'The shallowest heading included in the table of contents.', 'wikipress' ), 'tooltip' => __( 'Heading 2 is a useful default for most documentation pages.', 'wikipress' ), 'type' => 'number', 'default' => 2, 'min' => 1, 'max' => 5 ],
				'toc_max_level' => [ 'label' => __( 'TOC Maximum Heading', 'wikipress' ), 'description' => __( 'The deepest heading included in the table of contents.', 'wikipress' ), 'tooltip' => __( 'Avoid including every heading level on very detailed pages.', 'wikipress' ), 'type' => 'number', 'default' => 4, 'min' => 2, 'max' => 6 ],
				'show_last_updated' => $toggle( __( 'Show Last Updated Date', 'wikipress' ), __( 'Display when the Wiki page was last updated.', 'wikipress' ), __( 'This reassures visitors that the page is maintained.', 'wikipress' ), true ),
				'show_author' => $toggle( __( 'Show Page Author', 'wikipress' ), __( 'Display the author of the Wiki page.', 'wikipress' ), __( 'Useful when Wikis are maintained by multiple contributors.', 'wikipress' ) ),
				'show_reading_time' => $toggle( __( 'Show Reading Time', 'wikipress' ), __( 'Display an estimated reading time for the page.', 'wikipress' ), __( 'Reading time helps visitors decide how much time to set aside.', 'wikipress' ) ),
				'reading_time_wpm' => [ 'label' => __( 'Reading Speed', 'wikipress' ), 'description' => __( 'Words per minute used to calculate reading time.', 'wikipress' ), 'tooltip' => __( 'Use a lower value when your audience benefits from a slower estimate.', 'wikipress' ), 'type' => 'number', 'default' => 200, 'min' => 100, 'max' => 400 ],
				'show_feedback' => $toggle( __( 'Show Page Feedback', 'wikipress' ), __( 'Ask visitors whether the Wiki page was helpful.', 'wikipress' ), __( 'Feedback gives Wiki maintainers a simple quality signal.', 'wikipress' ), true ),
				'page_show_navigation' => $toggle( __( 'Show Previous and Next Links', 'wikipress' ), __( 'Add navigation links between related Wiki pages.', 'wikipress' ), __( 'This gives visitors another way to move through a Wiki.', 'wikipress' ), true ),
				'show_related_pages' => $toggle( __( 'Show Related Pages', 'wikipress' ), __( 'Display related Wiki pages below the current page.', 'wikipress' ), __( 'Related pages encourage visitors to continue exploring.', 'wikipress' ), true ),
				'related_pages_count' => [ 'label' => __( 'Related Pages Count', 'wikipress' ), 'description' => __( 'Number of related pages to display.', 'wikipress' ), 'tooltip' => __( 'Keep this low enough that related content does not overwhelm the page.', 'wikipress' ), 'type' => 'number', 'default' => 4, 'min' => 1, 'max' => 12 ],
			],
			default => [
				'show_search' => $toggle( __( 'Enable Wiki Search', 'wikipress' ), __( 'Display the Wiki search interface.', 'wikipress' ), __( 'Search is one of the fastest ways to find content in a large Wiki.', 'wikipress' ), true ),
				'show_breadcrumbs' => $toggle( __( 'Show Breadcrumbs', 'wikipress' ), __( 'Display the current Wiki hierarchy.', 'wikipress' ), __( 'Breadcrumbs connect the current page to its parent structure.', 'wikipress' ), true ),
				'show_sidebar' => $toggle( __( 'Show Wiki Sidebar', 'wikipress' ), __( 'Display the Wiki navigation sidebar.', 'wikipress' ), __( 'The sidebar keeps parent and child Wiki pages discoverable.', 'wikipress' ), true ),
			],
		};
	}
}
