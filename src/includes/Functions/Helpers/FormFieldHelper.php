<?php
/**
 * Bootstrap form field rendering helpers.
 *
 * @package WikiPress
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Functions\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class FormFieldHelper {
    /**
     * Render a text input.
     *
     * @param string $name       The name attribute for the input.
     * @param string $value      The value attribute for the input.
     * @param array  $attributes Additional attributes for the input.
     * @return string The HTML markup for the text input.
     */
    public static function text_input( string $name, string $value = '', array $attributes = [] ): string {

        return self::input( $name, $value, $attributes + [ 'type' => 'text' ] );

    }

    /**
     * Render a password input.
     *
     * @param string $name       The name attribute for the input.
     * @param string $value      The value attribute for the input.
     * @param array  $attributes Additional attributes for the input.
     * @return string The HTML markup for the password input.
     */
    public static function input( string $name, $value = '', array $options = [] ): string {

        $type = sanitize_key( (string) ( $options['type'] ?? 'text' ) );

        $class = self::classes( [ 'form-control', $options['class'] ?? '', self::validation_class( $options ) ] );

        if ( 'color' === $type ) {

            $class = self::classes( [ $class, 'form-control-color' ] );

        }

        $attributes = array_merge( $options['attributes'] ?? [], $options );

        unset( $attributes['attributes'], $attributes['class'], $attributes['type'], $attributes['value'], $attributes['validation'] );

        $attributes['class'] = $class;
        $attributes['type'] = $type;
        $attributes['name'] = $name;

        if ( 'file' !== $type ) {

            $attributes['value'] = $value;

        }

        return '<input ' . self::attributes_to_string( $attributes ) . ' />' . self::feedback( $options );

    }
    /**
     * Render a textarea.
     *
     * @param string $name       The name attribute for the textarea.
     * @param string $value      The value attribute for the textarea.
     * @param array  $attributes Additional attributes for the textarea.
     * @return string The HTML markup for the textarea.
     */
    public static function textarea( string $name, string $value = '', array $options = [] ): string {

        $attributes = array_merge( $options['attributes'] ?? [], $options );

        unset( $attributes['attributes'], $attributes['class'], $attributes['value'], $attributes['validation'] );

        $attributes['class'] = self::classes( [ 'form-control', $options['class'] ?? '', self::validation_class( $options ) ] );

        $attributes['name'] = $name;

        return '<textarea ' . self::attributes_to_string( $attributes ) . '>' . esc_textarea( $value ) . '</textarea>' . self::feedback( $options );

    }

    /**
     * Render a select dropdown.
     *
     * @param string $name       The name attribute for the select.
     * @param array  $options    The options for the select.
     * @param mixed  $selected   The selected value(s) for the select.
     * @param array  $attributes Additional attributes for the select.
     * @return string The HTML markup for the select dropdown.
     */
    public static function select( string $name, array $options = [], $selected = [], array $attributes = [] ): string {

        $selected = array_map( 'strval', (array) $selected );
        $class = $attributes['class'] ?? '';
        $validation = $attributes['validation'] ?? [];
        $attributes = array_merge( $attributes['attributes'] ?? [], $attributes );
        unset( $attributes['attributes'], $attributes['class'], $attributes['options'], $attributes['selected'], $attributes['validation'] );
        $attributes['class'] = self::classes( [ 'form-select', $class, self::validation_class( [ 'validation' => $validation ] ) ] );
        $attributes['name'] = $name;
        $html = '<select ' . self::attributes_to_string( $attributes ) . '>';

        foreach ( $options as $key => $option ) {

            if ( is_array( $option ) && isset( $option['options'] ) ) {

                $html .= '<optgroup label="' . esc_attr( $option['label'] ?? $key ) . '"' . ( ! empty( $option['disabled'] ) ? ' disabled' : '' ) . '>';
                $html .= self::option_list( $option['options'], $selected ) . '</optgroup>';
                continue;

            }

            $value = is_array( $option ) ? (string) ( $option['value'] ?? $key ) : (string) $key;
            $label = is_array( $option ) ? (string) ( $option['label'] ?? $value ) : (string) $option;
            $html .= self::option( $value, $label, in_array( $value, $selected, true ), is_array( $option ) && ! empty( $option['disabled'] ) );

        }

        return $html . '</select>' . self::feedback( $attributes );

    }
    /**
     * Render a checkbox input.
     *
     * @param string $name       The name attribute for the checkbox.
     * @param string $value      The value attribute for the checkbox.
     * @param string $label      The label text for the checkbox.
     * @param array  $attributes Additional attributes for the checkbox.
     * @return string The HTML markup for the checkbox input.
     */
    public static function checkbox( string $name, string $value = '1', string $label = '', array $options = [] ): string {

        return self::check( 'checkbox', $name, $value, $label, $options );

    }
    /**
     * Render a radio input.
     *
     * @param string $name       The name attribute for the radio button.
     * @param string $value      The value attribute for the radio button.
     * @param string $label      The label text for the radio button.
     * @param array  $attributes Additional attributes for the radio button.
     * @return string The HTML markup for the radio input.
     */
    public static function radio( string $name, string $value, string $label = '', array $options = [] ): string {

        return self::check( 'radio', $name, $value, $label, $options );

    }
    /**
     * Render a switch input (checkbox styled as a switch).
     *
     * @param string $name       The name attribute for the switch.
     * @param string $value      The value attribute for the switch.
     * @param string $label      The label text for the switch.
     * @param array  $attributes Additional attributes for the switch.
     * @return string The HTML markup for the switch input.
     */
    public static function switch( string $name, string $value = '1', string $label = '', array $options = [] ): string {

        $options['switch'] = true;

        return self::check( 'checkbox', $name, $value, $label, $options );

    }
    /**
     * Render a checkbox or radio input with label.
     *
     * @param string $type       The type of input ('checkbox' or 'radio').
     * @param string $name       The name attribute for the input.
     * @param string $value      The value attribute for the input.
     * @param string $label      The label text for the input.
     * @param array  $attributes Additional attributes for the input.
     * @return string The HTML markup for the checkbox or radio input.
     */
    public static function check( string $type, string $name, string $value, string $label = '', array $options = [] ): string {

        $type = in_array( $type, [ 'checkbox', 'radio' ], true ) ? $type : 'checkbox';
        $id = (string) ( $options['id'] ?? sanitize_title( $name . '-' . $value ) );
        $wrapper = self::classes( [ 'form-check', ! empty( $options['inline'] ) ? 'form-check-inline' : '', ! empty( $options['switch'] ) ? 'form-switch' : '', ! empty( $options['reverse'] ) ? 'form-check-reverse' : '', $options['wrapper_class'] ?? '' ] );
        $attributes = array_merge( $options['attributes'] ?? [], $options );
        unset( $attributes['attributes'], $attributes['class'], $attributes['wrapper_class'], $attributes['inline'], $attributes['switch'], $attributes['reverse'], $attributes['checked'], $attributes['id'], $attributes['type'], $attributes['value'], $attributes['validation'] );
        $attributes['class'] = self::classes( [ 'form-check-input', $options['class'] ?? '', self::validation_class( $options ) ] );
        $attributes['id'] = $id;
        $attributes['type'] = $type;
        $attributes['name'] = $name;
        $attributes['value'] = $value;

        if ( ! empty( $options['checked'] ) ) {

            $attributes['checked'] = true;

        }

        if ( ! empty( $options['switch'] ) ) {

            $attributes['role'] = 'switch';

        }

        $html = '<div class="' . esc_attr( $wrapper ) . '"><input ' . self::attributes_to_string( $attributes ) . ' />';

        if ( '' !== $label ) {

            $html .= '<label class="form-check-label" for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';

        }

        return $html . self::feedback( $options ) . '</div>';

    }
    /**
     * Render a range input with optional output display.
     *
     * @param string $name       The name attribute for the range input.
     * @param int    $value      The value attribute for the range input.
     * @param array  $attributes Additional attributes for the range input.
     * @return string The HTML markup for the range input.
     */
    public static function range( string $name, $value = 0, array $options = [] ): string {

        $show_output = ! empty( $options['output'] );
        $options['type'] = 'range';
        $options['class'] = self::classes( [ 'form-range', $options['class'] ?? '' ] );
        unset( $options['output'] );
        $html = self::input( $name, $value, $options );

        if ( $show_output ) {

            $id = (string) ( $options['id'] ?? sanitize_title( $name ) );
            $html .= '<output for="' . esc_attr( $id ) . '" id="' . esc_attr( $id . '-output' ) . '">' . esc_html( (string) $value ) . '</output>';

        }

        return $html;

    }
    /**
     * Render a datalist input with options.
     *
     * @param string $name       The name attribute for the input.
     * @param array  $values     The values for the datalist options.
     * @param string $value      The value attribute for the input.
     * @param array  $attributes Additional attributes for the input.
     * @return string The HTML markup for the datalist input.
     */
    public static function datalist( string $name, array $values, $value = '', array $options = [] ): string {

        $id = (string) ( $options['list'] ?? sanitize_title( $name ) . '-options' );
        $options['list'] = $id;
        $html = self::input( $name, $value, $options ) . '<datalist id="' . esc_attr( $id ) . '">';

        foreach ( $values as $option ) {

            $option_value = is_array( $option ) ? ( $option['value'] ?? '' ) : $option;
            $label = is_array( $option ) && isset( $option['label'] ) ? esc_html( $option['label'] ) : '';
            $html .= '<option value="' . esc_attr( $option_value ) . '">' . $label . '</option>';

        }

        return $html . '</datalist>';

    }

    /**
     * Render a select option.
     *
     * @param string $value    The value attribute for the option.
     * @param string $label    The label text for the option.
     * @param bool   $selected Whether the option is selected.
     * @param bool   $disabled Whether the option is disabled.
     * @return string The HTML markup for the select option.
     */
    public static function bootstrap_search( string $name, array $options = [] ): string {

        $id = (string) ( $options['id'] ?? sanitize_title( $name ) . '-search' );
        $hidden_name = (string) ( $options['hidden_name'] ?? $name );
        $multi_select = ! empty( $options['multi_select'] );

        if ( $multi_select && ! str_ends_with( $hidden_name, '[]' ) ) {

            $hidden_name .= '[]';

        }

        $config = [
            'threshold' => max( 0, (int) ( $options['threshold'] ?? 2 ) ),
            'maximumItems' => max( 0, (int) ( $options['maximum_items'] ?? 5 ) ),
            'highlightTyped' => ! isset( $options['highlight_typed'] ) || (bool) $options['highlight_typed'],
            'inputLabel' => (string) ( $options['input_label'] ?? 'label' ),
            'dropdownLabel' => (string) ( $options['dropdown_label'] ?? 'label' ),
            'value' => (string) ( $options['value'] ?? 'value' ),
            'showValue' => ! empty( $options['show_value'] ),
            'showValueBeforeLabel' => ! empty( $options['show_value_before_label'] ),
            'remoteData' => $options['remote_data'] ?? null,
            'remoteDataHttpMethod' => strtoupper( (string) ( $options['remote_data_http_method'] ?? 'GET' ) ),
            'data' => array_values( $options['data'] ?? [] ),
            'multiSelect' => $multi_select,
            'dropdownClass' => (string) ( $options['dropdown_class'] ?? '' ),
            'selectedItems' => array_values( $options['selected_items'] ?? [] ),
        ];

        $attributes = array_merge( $options['attributes'] ?? [], $options );
        unset( $attributes['attributes'], $attributes['id'], $attributes['class'], $attributes['type'], $attributes['value'], $attributes['data'], $attributes['selected_items'], $attributes['remote_data'], $attributes['remote_data_http_method'], $attributes['multi_select'], $attributes['hidden_name'], $attributes['input_label'], $attributes['dropdown_label'], $attributes['maximum_items'], $attributes['highlight_typed'], $attributes['show_value'], $attributes['show_value_before_label'], $attributes['threshold'], $attributes['dropdown_class'], $attributes['validation'] );
        $attributes['id'] = $id;
        $attributes['type'] = 'text';
        $attributes['class'] = self::classes( [ 'form-control', $options['class'] ?? '', self::validation_class( $options ) ] );
        $attributes['autocomplete'] = 'off';
        $attributes['data-bootstrap-search'] = wp_json_encode( $config );
        $html = '<input ' . self::attributes_to_string( $attributes ) . ' />';
        $selected = $config['selectedItems'];
        $first_selected = $selected[0] ?? '';
        $hidden_value = $multi_select ? '' : (string) ( is_array( $first_selected ) ? ( $first_selected[ $config['value'] ] ?? '' ) : $first_selected );
        $hidden_attributes = [ 'type' => 'hidden', 'name' => $hidden_name, 'value' => $hidden_value, 'data-bootstrap-search-value' => $id ];
        $html .= '<input ' . self::attributes_to_string( $hidden_attributes ) . ' />';

        return $html . self::feedback( $options );

    }
    /**
     * Render a select option list.
     *
     * @param array $options  The options for the select.
     * @param array $selected The selected value(s) for the select.
     * @return string The HTML markup for the select option list.
     */
    public static function bootstrap_search_multiselect( string $name, array $options = [] ): string {

        $options['multi_select'] = true;

        return self::bootstrap_search( $name, $options );

    }
    /**
     * Render a select option.
     *
     * @param string $value    The value attribute for the option.
     * @param string $label    The label text for the option.
     * @param bool   $selected Whether the option is selected.
     * @param bool   $disabled Whether the option is disabled.
     * @return string The HTML markup for the select option.
     */
    public static function bootstrap_search_ajax( string $name, string $remote_url, array $options = [] ): string {

        $options['remote_data'] = $remote_url;

        return self::bootstrap_search( $name, $options );

    }
    /**
     * Render a select option.
     *
     * @param string $value    The value attribute for the option.
     * @param string $label    The label text for the option.
     * @param bool   $selected Whether the option is selected.
     * @param bool   $disabled Whether the option is disabled.
     * @return string The HTML markup for the select option.
     */
    public static function label( string $for, string $text, array $options = [] ): string {

        $attributes = array_merge( $options['attributes'] ?? [], $options );
        unset( $attributes['attributes'], $attributes['class'] );
        $attributes['class'] = self::classes( [ 'form-label', $options['class'] ?? '' ] );
        $attributes['for'] = $for;

        return '<label ' . self::attributes_to_string( $attributes ) . '>' . esc_html( $text ) . '</label>';

    }
    /**
     * Render a select option.
     *
     * @param string $value    The value attribute for the option.
     * @param string $label    The label text for the option.
     * @param bool   $selected Whether the option is selected.
     * @param bool   $disabled Whether the option is disabled.
     * @return string The HTML markup for the select option.
     */
    public static function form_text( string $text, array $options = [] ): string {

        $attributes = $options['attributes'] ?? [];
        $attributes['class'] = self::classes( [ 'form-text', $options['class'] ?? '' ] );

        if ( isset( $options['id'] ) ) {

            $attributes['id'] = $options['id'];

        }

        return '<div ' . self::attributes_to_string( $attributes ) . '>' . esc_html( $text ) . '</div>';

    }
    /**
     * Render validation feedback for a form field.
     *
     * @param array $options The options for the form field.
     * @return string The HTML markup for the validation feedback.
     */
    public static function feedback( array $options = [] ): string {

        $validation = $options['validation'] ?? [];

        if ( is_string( $validation ) ) {

            $validation = [ 'state' => 'invalid', 'message' => $validation ];

        }

        if ( empty( $validation['message'] ) ) {

            return '';

        }

        $state = 'valid' === ( $validation['state'] ?? 'invalid' ) ? 'valid' : 'invalid';
        $class = self::classes( [ $state . '-feedback', ! empty( $validation['tooltip'] ) ? $state . '-tooltip' : '' ] );
        $attributes = [ 'class' => $class ];

        if ( isset( $validation['id'] ) ) {

            $attributes['id'] = $validation['id'];

        }

        return '<div ' . self::attributes_to_string( $attributes ) . '>' . esc_html( $validation['message'] ) . '</div>';

    }
    /**
     * Render an input group with optional size and validation feedback.
     *
     * @param string $content The content of the input group.
     * @param array  $options The options for the input group.
     * @return string The HTML markup for the input group.
     */
    public static function input_group( string $content, array $options = [] ): string {

        $attributes = $options['attributes'] ?? [];
        $attributes['class'] = self::classes( [ 'input-group', ! empty( $options['size'] ) ? 'input-group-' . $options['size'] : '', ! empty( $options['validation'] ) ? 'has-validation' : '', $options['class'] ?? '' ] );

        return '<div ' . self::attributes_to_string( $attributes ) . '>' . $content . self::feedback( $options ) . '</div>';

    }

    /**
     * Render a floating label input group.
     *
     * @param string $control The input control HTML.
     * @param string $label   The label text for the floating label.
     * @param array  $options The options for the floating label.
     * @return string The HTML markup for the floating label input group.
     */
    public static function floating( string $control, string $label, array $options = [] ): string {

        $attributes = $options['attributes'] ?? [];
        $attributes['class'] = self::classes( [ 'form-floating', $options['class'] ?? '' ] );

        return '<div ' . self::attributes_to_string( $attributes ) . '>' . $control . self::label( (string) ( $options['for'] ?? '' ), $label, [ 'class' => $options['label_class'] ?? '' ] ) . '</div>';

    }

    /**
     * Render a form opening tag with optional attributes.
     *
     * @param string $action  The action URL for the form.
     * @param string $method  The HTTP method for the form (default: 'post').
     * @param array  $options Additional attributes for the form tag.
     * @return string The HTML markup for the form opening tag.
     */
    public static function form_open( string $action = '', string $method = 'post', array $options = [] ): string {

        $attributes = array_merge( $options['attributes'] ?? [], $options );
        unset( $attributes['attributes'], $attributes['class'], $attributes['action'], $attributes['method'], $attributes['validation'] );
        $attributes['action'] = $action;
        $attributes['method'] = strtolower( $method );
        $attributes['class'] = self::classes( [ $options['class'] ?? '', ! empty( $options['validation'] ) ? 'needs-validation' : '' ] );

        return '<form ' . self::attributes_to_string( $attributes ) . '>';

    }
    /**
     * Render a form closing tag.
     *
     * @return string The HTML markup for the form closing tag.
     */
    public static function form_close(): string {

        return '</form>';

    }
    /**
     * Convert an associative array of attributes to a string for HTML output.
     *
     * @param array $attributes The associative array of attributes.
     * @return string The string representation of the attributes.
     */
    public static function attributes_to_string( array $attributes ): string {

        $output = [];

        foreach ( $attributes as $key => $value ) {

            if ( null === $value || false === $value ) {

                continue;

            }

            $key = sanitize_key( (string) $key );

            if ( '' === $key ) {

                continue;

            }

            $output[] = true === $value ? esc_attr( $key ) : esc_attr( $key ) . '="' . esc_attr( (string) $value ) . '"';

        }

        return implode( ' ', $output );

    }
    /**
     * Normalize and sanitize an array of CSS classes.
     *
     * @param array $classes The array of CSS classes.
     * @return string The normalized and sanitized CSS classes as a space-separated string.
     */
    private static function classes( array $classes ): string {

        $normalized = [];

        foreach ( $classes as $class_list ) {

            foreach ( preg_split( '/\s+/', trim( (string) $class_list ) ) as $class ) {

                if ( '' !== $class ) {

                    $normalized[] = sanitize_html_class( $class );

                }
            }
        }

        return implode( ' ', array_filter( array_unique( $normalized ) ) );

    }
    /**
     * Determine the validation class based on the provided options.
     *
     * @param array $options The options for the form field.
     * @return string The validation class ('is-valid', 'is-invalid', or an empty string).
     */
    private static function validation_class( array $options ): string {

        $validation = $options['validation'] ?? [];

        if ( is_string( $validation ) ) {

            return 'is-invalid';

        }

        if ( ! empty( $validation['state'] ) && in_array( $validation['state'], [ 'valid', 'invalid' ], true ) ) {

            return 'is-' . $validation['state'];

        }

        return '';

    }
    /**
     * Render a list of select options.
     *
     * @param array $options  The options for the select.
     * @param array $selected The selected value(s) for the select.
     * @return string The HTML markup for the select option list.
     */
    private static function option_list( array $options, array $selected ): string {

        $html = '';

        foreach ( $options as $key => $option ) {

            $value = is_array( $option ) ? (string) ( $option['value'] ?? $key ) : (string) $key;
            $label = is_array( $option ) ? (string) ( $option['label'] ?? $value ) : (string) $option;
            $html .= self::option( $value, $label, in_array( $value, $selected, true ), is_array( $option ) && ! empty( $option['disabled'] ) );

        }

        return $html;

    }
    /**
     * Render a select option.
     *
     * @param string $value    The value attribute for the option.
     * @param string $label    The label text for the option.
     * @param bool   $selected Whether the option is selected.
     * @param bool   $disabled Whether the option is disabled.
     * @return string The HTML markup for the select option.
     */
    private static function option( string $value, string $label, bool $selected, bool $disabled ): string {

        return '<option value="' . esc_attr( $value ) . '"' . ( $selected ? ' selected' : '' ) . ( $disabled ? ' disabled' : '' ) . '>' . esc_html( $label ) . '</option>';
        
    }
}
