<?php
// Development-only stubs for WordPress functions/classes to satisfy static analyzers.
// This file is NOT included at runtime by the plugin.

// Core hook APIs
if (!function_exists('add_action')) { function add_action($hook, $callback, $priority = 10, $accepted_args = 1) {} }
if (!function_exists('add_filter')) { function add_filter($hook, $callback, $priority = 10, $accepted_args = 1) {} }
if (!function_exists('remove_action')) { function remove_action($hook, $callback, $priority = 10) {} }
if (!function_exists('remove_filter')) { function remove_filter($hook, $callback, $priority = 10) {} }

// Template/location
if (!function_exists('locate_template')) { function locate_template($template_names, $load = false, $require_once = true, $args = []) { return ''; } }

// Options API
if (!function_exists('get_option')) { function get_option($option, $default = false) { return $default; } }
if (!function_exists('update_option')) { function update_option($option, $value, $autoload = null) { return true; } }
if (!function_exists('delete_option')) { function delete_option($option) { return true; } }

// Post/Query APIs
if (!class_exists('WP_Query')) {
    class WP_Query {
        public $posts = [];
        public $post_count = 0;
        public $max_num_pages = 0;
        public function __construct($args = []) {}
        public function have_posts() { return false; }
        public function the_post() {}
        public function rewind_posts() {}
        public function get($var, $default = null) { return $default; }
    }
}
if (!function_exists('get_query_var')) { function get_query_var($var, $default = '') { return $default; } }
if (!function_exists('get_the_ID')) { function get_the_ID() { return 0; } }
if (!function_exists('get_the_title')) { function get_the_title($post = 0) { return ''; } }
if (!function_exists('get_permalink')) { function get_permalink($post = 0) { return ''; } }
if (!function_exists('get_the_excerpt')) { function get_the_excerpt($post = null) { return ''; } }
if (!function_exists('get_post_field')) { function get_post_field($field, $post = null, $context = 'display') { return ''; } }
if (!function_exists('get_previous_post')) { function get_previous_post($in_same_term = false, $excluded_terms = '', $taxonomy = 'category') { return null; } }
if (!function_exists('get_next_post')) { function get_next_post($in_same_term = false, $excluded_terms = '', $taxonomy = 'category') { return null; } }
if (!function_exists('get_post_meta')) { function get_post_meta($post_id, $key = '', $single = false) { return $single ? '' : []; } }
if (!function_exists('update_post_meta')) { function update_post_meta($post_id, $meta_key, $meta_value, $prev_value = '') { return true; } }
if (!function_exists('wp_reset_postdata')) { function wp_reset_postdata() {} }

// Taxonomy APIs
if (!class_exists('WP_Error')) { class WP_Error {} }
if (!function_exists('is_wp_error')) { function is_wp_error($thing) { return $thing instanceof WP_Error; } }
if (!defined('OBJECT')) { define('OBJECT', 'OBJECT'); }
if (!function_exists('get_term')) { function get_term($term, $taxonomy = '', $output = OBJECT, $filter = 'raw') { return null; } }
if (!function_exists('get_term_link')) { function get_term_link($term, $taxonomy = '') { return ''; } }
if (!function_exists('get_ancestors')) { function get_ancestors($object_id = 0, $object_type = '', $resource_type = '') { return []; } }
if (!function_exists('wp_get_post_terms')) { function wp_get_post_terms($post_id = 0, $taxonomy = '', $args = []) { return []; } }
if (!function_exists('get_terms')) { function get_terms($args = []) { return []; } }
if (!function_exists('get_the_terms')) { function get_the_terms($post, $taxonomy) { return []; } }

// Conditional tags
if (!function_exists('is_singular')) { function is_singular($post_types = '') { return false; } }
if (!function_exists('is_post_type_archive')) { function is_post_type_archive($post_types = '') { return false; } }
if (!function_exists('is_tax')) { function is_tax($taxonomy = '', $term = '') { return false; } }
if (!function_exists('is_admin')) { function is_admin() { return false; } }

// URLs
if (!function_exists('home_url')) { function home_url($path = '', $scheme = null) { return '/'; } }
if (!function_exists('get_post_type_archive_link')) { function get_post_type_archive_link($post_type) { return '/'; } }
if (!function_exists('paginate_links')) { function paginate_links($args = '') { return ''; } }
if (!function_exists('get_pagenum_link')) { function get_pagenum_link($pagenum = 1, $escape = true, $url = '') { return ''; } }

// Sanitization/Escaping
if (!function_exists('sanitize_html_class')) { function sanitize_html_class($class, $fallback = '') { return $class; } }
if (!function_exists('sanitize_title')) { function sanitize_title($title, $fallback_title = '', $context = 'save') { return $title; } }
if (!function_exists('esc_url')) { function esc_url($url, $protocols = null, $_context = 'display') { return $url; } }
if (!function_exists('esc_html')) { function esc_html($text) { return $text; } }
if (!function_exists('esc_attr')) { function esc_attr($text) { return $text; } }
if (!function_exists('wp_strip_all_tags')) { function wp_strip_all_tags($string, $remove_breaks = false) { return $string; } }
if (!function_exists('sanitize_text_field')) { function sanitize_text_field($str) { return $str; } }
if (!function_exists('sanitize_key')) { function sanitize_key($key) { return $key; } }
if (!function_exists('wp_kses_post')) { function wp_kses_post($data) { return $data; } }
if (!function_exists('wpautop')) { function wpautop($pee, $br = true) { return $pee; } }
if (!function_exists('wp_unslash')) { function wp_unslash($value) { return $value; } }
if (!function_exists('esc_textarea')) { function esc_textarea($text) { return $text; } }
if (!function_exists('wp_json_encode')) { function wp_json_encode($data, $options = 0, $depth = 512) { return json_encode($data, $options); } }

// Widgets
if (!function_exists('register_sidebar')) { function register_sidebar($args = []) {} }
if (!function_exists('register_widget')) { function register_widget($widget_class) {} }
if (!class_exists('WP_Widget')) { class WP_Widget { public function __construct() {} public function widget($args, $instance) {} public function form($instance) {} public function update($new_instance, $old_instance) { return $new_instance; } protected function get_field_id($field_name) { return $field_name; } protected function get_field_name($field_name) { return $field_name; } } }

// Shortcodes
if (!function_exists('add_shortcode')) { function add_shortcode($tag, $callback) {} }
if (!function_exists('shortcode_atts')) { function shortcode_atts($pairs, $atts, $shortcode = '') { return array_merge($pairs, (array)$atts); } }
if (!function_exists('register_block_type')) { function register_block_type($path_or_settings, $args = []) {} }

// Rewrite
if (!function_exists('flush_rewrite_rules')) { function flush_rewrite_rules($hard = true) {} }

// Misc
if (!function_exists('__')) { function __($text, $domain = null) { return $text; } }
if (!function_exists('esc_html__')) { function esc_html__($text, $domain = null) { return $text; } }
if (!function_exists('apply_filters')) { function apply_filters($hook_name, $value) { return $value; } }
if (!function_exists('check_ajax_referer')) { function check_ajax_referer($action = -1, $query_arg = false, $die = true) { return 1; } }
if (!function_exists('wp_send_json_success')) { function wp_send_json_success($data = null, $status_code = null) {} }
if (!function_exists('wp_send_json_error')) { function wp_send_json_error($data = null, $status_code = null) {} }
if (!function_exists('add_meta_box')) { function add_meta_box($id, $title, $callback, $screen = null, $context = 'advanced', $priority = 'default', $callback_args = null) {} }
if (!function_exists('wp_nonce_field')) { function wp_nonce_field($action = -1, $name = '_wpnonce', $referer = true, $echo = true) {} }
if (!function_exists('wp_verify_nonce')) { function wp_verify_nonce($nonce, $action = -1) { return true; } }
if (!function_exists('current_user_can')) { function current_user_can($capability, ...$args) { return true; } }
if (!function_exists('is_ssl')) { function is_ssl() { return false; } }
if (!function_exists('wp_localize_script')) { function wp_localize_script($handle, $object_name, $l10n) {} }
if (!function_exists('admin_url')) { function admin_url($path = '', $scheme = 'admin') { return '/wp-admin/' . ltrim($path, '/'); } }
if (!function_exists('wp_create_nonce')) { function wp_create_nonce($action = -1) { return 'nonce'; } }
if (!function_exists('get_header')) { function get_header($name = null, $args = []) {} }
if (!function_exists('get_footer')) { function get_footer($name = null, $args = []) {} }
if (!function_exists('have_posts')) { function have_posts() { return false; } }
if (!function_exists('the_post')) { function the_post() {} }
if (!function_exists('post_class')) { function post_class($class = '') { echo 'class="post"'; } }
if (!function_exists('the_title')) { function the_title($before = '', $after = '', $echo = true) { if ($echo) echo ''; else return ''; } }
if (!function_exists('get_the_author')) { function get_the_author() { return ''; } }
if (!function_exists('get_the_date')) { function get_the_date($format = '') { return ''; } }
if (!function_exists('get_the_modified_time')) { function get_the_modified_time($format = '') { return ''; } }
if (!function_exists('the_content')) { function the_content($more_link_text = null, $strip_teaser = false) { echo ''; } }
if (!function_exists('the_terms')) { function the_terms($post_id, $taxonomy, $before = '', $sep = '', $after = '') {} }
if (!function_exists('do_shortcode')) { function do_shortcode($content, $ignore_html = false) { return $content; } }
if (!function_exists('comments_template')) { function comments_template($file = '/comments.php', $separate_comments = false) {} }
if (!function_exists('is_active_sidebar')) { function is_active_sidebar($index) { return false; } }
if (!function_exists('dynamic_sidebar')) { function dynamic_sidebar($index = 1) { return false; } }

// Common WP constants
if (!defined('COOKIEPATH')) { define('COOKIEPATH', '/'); }
if (!defined('COOKIE_DOMAIN')) { define('COOKIE_DOMAIN', ''); }
if (!defined('DOING_AUTOSAVE')) { define('DOING_AUTOSAVE', false); }
