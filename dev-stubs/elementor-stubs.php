<?php
/**
 * Custom Elementor Stubs for TrilB.Dev Theme
 * This file provides stub definitions for Elementor classes to resolve PHP language server errors
 * DO NOT LOAD THIS FILE AT RUNTIME - Only for static analysis
 */

namespace Elementor {

    // Prevent loading if Elementor is already loaded
    if (defined('ELEMENTOR_VERSION') || class_exists('Elementor\\Plugin')) {
        return;
    }

    class Widget_Base {
        public function __construct() {}
        public function get_name() { return ''; }
        public function get_title() { return ''; }
        public function get_icon() { return ''; }
        public function get_categories() { return []; }
        public function get_keywords() { return []; }

        protected function start_controls_section(string $id, array $args = []): void {}
        protected function end_controls_section(): void {}
        protected function add_control(string $id, array $args = []): void {}
        protected function add_responsive_control(string $id, array $args = []): void {}
        protected function add_group_control(string $id, array $args = []): void {}
        protected function add_link_attributes(string $element, array $url_control, bool $overwrite = false): void {}
        protected function start_controls_tabs(string $tabs_id): void {}
        protected function start_controls_tab(string $id, array $args = []): void {}
        protected function end_controls_tab(): void {}
        protected function end_controls_tabs(): void {}

        public function render(): void {}
        public function content_template(): void {}

        public function get_settings(string $key = '') { return null; }
        public function set_settings(string $key, $value): void {}
        public function get_settings_for_display(string $key = '') { return null; }

        protected function add_render_attribute(string $element, string|array $key_or_attributes, ?string $value = null): void {}
        protected function get_render_attribute_string(string $element): string { return ''; }
        protected function parse_text_editor(string $content): string { return $content; }
        public function get_id(): string { return ''; }
    }

    class Controls_Manager {
        const TEXT = 'text';
        const TEXTAREA = 'textarea';
        const WYSIWYG = 'wysiwyg';
        const SELECT = 'select';
        const SELECT2 = 'select2';
        const SWITCHER = 'switcher';
        const SLIDER = 'slider';
        const NUMBER = 'number';
        const COLOR = 'color';
        const MEDIA = 'media';
        const ICONS = 'icons';
        const ICON = 'icon';
        const IMAGE_DIMENSIONS = 'image_dimensions';
        const HIDDEN = 'hidden';
        const URL = 'url';
        const REPEATER = 'repeater';
        const DATE_TIME = 'date_time';
        const GALLERY = 'gallery';
        const CODE = 'code';
        const FONT = 'font';
        const BOX_SHADOW = 'box_shadow';
        const TEXT_SHADOW = 'text_shadow';
        const BORDER = 'border';
        const TYPOGRAPHY = 'typography';
        const DIMENSIONS = 'dimensions';
        const BACKGROUND = 'background';
        const ANIMATION = 'animation';
        const HOVER_ANIMATION = 'hover_animation';
        const EXIT_ANIMATION = 'exit_animation';
        const TAB_CONTENT = 'content';
        const TAB_STYLE = 'style';
        const TAB_ADVANCED = 'advanced';
        const HEADING = 'heading';
        const CHOOSE = 'choose';
    }

    class Utils {
        public static function get_placeholder_image_src(): string { return ''; }
        public static function is_empty($value): bool { return empty($value); }
        public static function get_create_new_post_url(string $post_type = 'post', string $template_name = ''): string { return ''; }
        public static function get_edit_link(int $post_id): string { return ''; }
        public static function get_wp_editor_config(array $config = []): array { return []; }
        public static function generate_random_string(int $length = 10): string { return ''; }
        public static function get_super_global_value($super_global, string $key): mixed { return null; }
        public static function do_shortcode(string $content): string { return $content; }
    }

    class Icons_Manager {
        public static function render_icon($icon, array $attributes = [], string $tag = 'i'): void {}
        public static function get_icon_manager_tabs(): array { return []; }
        public static function enqueue_shim(): void {}
        public static function is_migration_allowed(): bool { return true; }
    }

    class Group_Control_Base {
        public function __construct() {}
        protected static function get_type(): string { return ''; }
        protected function init_fields(): void {}
        protected function get_default_options(): array { return []; }
    }

    class Group_Control_Typography extends Group_Control_Base {
        public static function get_type(): string { return 'typography'; }
    }

    class Group_Control_Background extends Group_Control_Base {
        public static function get_type(): string { return 'background'; }
    }

    class Group_Control_Border extends Group_Control_Base {
        public static function get_type(): string { return 'border'; }
    }

    class Group_Control_Box_Shadow extends Group_Control_Base {
        public static function get_type(): string { return 'box_shadow'; }
    }

    class Group_Control_Text_Shadow extends Group_Control_Base {
        public static function get_type(): string { return 'text_shadow'; }
    }

    class Group_Control_Image_Size extends Group_Control_Base {
        public static function get_type(): string { return 'image_size'; }
        public static function get_attachment_image_html(array $settings, string $image_size_key = 'image', string $image_key = 'image'): string { return ''; }
        public static function get_attachment_image_src(int $attachment_id, string $image_size_key, array $settings = []): string { return ''; }
    }

    class Control_Media {
        public static function get_image_alt(array $image_settings): string { return ''; }
    }

    class Base_Data_Control {
        public function __construct() {}
        public function get_type(): string { return ''; }
        public function get_value(array $control, array $settings): mixed { return null; }
        public function get_default_value(): mixed { return null; }
        public function get_default_settings(): array { return []; }
        public function get_settings(array $control): array { return []; }
        public function get_control_uid(string $input_type = 'default'): string { return ''; }
        public function print_template(): void {}
        public function content_template(): void {}
    }

    class Repeater {
        public function __construct() {}
        public function add_control(string $id, array $args = []): void {}
        public function get_fields(): array { return []; }
        public function get_field_key(string $key): string { return ''; }
        public function get_controls(): array { return []; }
    }

    class Schemes_Manager {
        const COLOR_1 = 'color_1';
        const COLOR_2 = 'color_2';
        const COLOR_3 = 'color_3';
        const COLOR_4 = 'color_4';
        const TYPOGRAPHY_1 = 'typography_1';
        const TYPOGRAPHY_2 = 'typography_2';
        const TYPOGRAPHY_3 = 'typography_3';
        const TYPOGRAPHY_4 = 'typography_4';
    }

    class Control_Select2 extends Base_Data_Control {
        public function __construct() {}
    }

    class Select2 extends Base_Data_Control {
        public function __construct() {}
    }

    class Experiments_Manager {
        public function is_feature_active(string $feature): bool { return false; }
    }

    class Frontend {
        public function get_builder_content_for_display(int $post_id): string { return ''; }
    }

    class Documents_Manager {
        public function get(int $post_id) { return null; }
    }

    class Plugin {
        public static function instance(): self { return new self(); }
        public function controls_manager(): Controls_Manager { return new Controls_Manager(); }
        public function icons_manager(): Icons_Manager { return new Icons_Manager(); }
        public function schemes_manager(): Schemes_Manager { return new Schemes_Manager(); }
        public $experiments;
        public $frontend;
        public $documents;
        public $widgets_manager;
        public $controls_manager;
        public static $instance;
        public $kits_manager;
    }

    // Helper function
    function plugin(): Plugin { return Plugin::instance(); }

    // Global function - only declare if Elementor Pro hasn't loaded
    if (!function_exists('elementor_theme_do_location') && !defined('ELEMENTOR_PRO_VERSION')) {
        function elementor_theme_do_location(string $location): bool { return false; }
    }
}

namespace {
    // Breadcrumb plugin stubs
    if (!function_exists('yoast_breadcrumb')) {
        function yoast_breadcrumb($prefix = '', $suffix = '', $display = true) {
            return '';
        }
    }
    if (!function_exists('breadcrumb_navxt_display')) {
        function breadcrumb_navxt_display($return = false, $linked = true, $reverse = false, $force = false) {
            return '';
        }
    }
}

namespace Elementor\Modules {
    class Library {
        public static function get_library_data(array $args = []): array { return []; }
    }
}

namespace Elementor\Includes {
    class Utils extends \Elementor\Utils {}
}

namespace ElementorPro\Modules\ThemeBuilder\Classes {
    class Locations_Manager {
        public function register_all_core_location(): void {}
    }
}