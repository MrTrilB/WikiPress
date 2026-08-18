<?php
/**
 * Render the Internal Wiki fields.
 *
 * @package WikiPress
 * @subpackage Includes\Plugins\InternalWiki\Includes\Templates
 */
namespace WikiPress\Includes\Plugins\InternalWiki\Includes\Templates;

use WikiPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class InternalWikiFields {
    /**
     * Render the Internal Wiki form fields.
     *
     * @param array<string, mixed> $data Field data.
     * @return string Rendered fields.
     */
    public static function render( array $data ): string {
        $enabled = ! empty( $data['enabled'] );
        $access_type = (string) ( $data['access_type'] ?? 'logged_in_user' );
        $access_types = (array) ( $data['access_types'] ?? [] );
        $role_options = (array) ( $data['role_options'] ?? [] );
        $roles = (array) ( $data['roles'] ?? [] );
        $permission_items = (array) ( $data['permission_items'] ?? [] );
        $selected_permission_items = (array) ( $data['selected_permission_items'] ?? [] );

        return '<div class="wikipress-internal-wiki-fields row g-3" data-internal-wiki-fields>'
            . '<div class="col-12">'
            . wp_kses_post( FormFieldHelper::switch( 
                'internal_wiki_enabled', 
                '1', 
                __( 
                    'Make this Wiki Internal', 
                    'wikipress' 
                ), 
                [ 
                    'id' => 'wikipress-internal-enabled', 
                    'checked' => $enabled, 
                    'attributes' => [ 
                        'data-internal-wiki-toggle' => true 
                    ] 
                ] 
            ) )
            . '</div>'
            . '<div class="col-md-4" data-internal-wiki-options>'
            . wp_kses_post( FormFieldHelper::label( 
                'wikipress-internal-access-type', 
                __( 
                    'Limit access by', 
                    'wikipress' 
                ) 
            ) )
            . wp_kses_post( FormFieldHelper::select( 
                'internal_wiki_access_type', 
                $access_types, 
                $access_type, 
                [ 
                    'id' => 'wikipress-internal-access-type', 
                    'attributes' => [ 
                        'data-internal-wiki-access-type' => true 
                    ] 
                ] 
            ) )
            . '</div>'
            . '<div class="col-md-4' . ( 
                'roles' === $access_type ? '' : ' d-none' 
                ) . '" data-internal-wiki-roles>'
            . wp_kses_post( FormFieldHelper::label( 
                'wikipress-internal-roles', 
                __( 
                    'Roles', 
                    'wikipress' 
                ) 
            ) )
            . wp_kses_post( FormFieldHelper::bootstrap_multiselect( 
                'internal_wiki_role[]', 
                [ 
                    'id' => 'wikipress-internal-roles', 
                    'data' => $role_options, 
                    'selected' => $roles, 
                    'attributes' => [ 
                        'data-internal-wiki-roles-select' => true 
                    ] 
                ] 
            ) )
            . '</div>'
            . '<div class="col-md-4' . ( 
                'permissions' === $access_type ? '' : ' d-none' 
                ) . '" data-internal-wiki-permissions>'
            . wp_kses_post( FormFieldHelper::label( 
                'wikipress-internal-permissions-search', 
                __( 
                    'Permissions', 
                    'wikipress' 
                ) 
            ) )
            . wp_kses_post( FormFieldHelper::bootstrap_multiselect( 
                'internal_wiki_permissions[]', 
                [ 
                    'id' => 'wikipress-internal-permissions-search', 
                    'data' => $permission_items, 
                    'selected' => array_column( 
                        $selected_permission_items, 
                        'value' 
                    ) 
                ] 
            ) )
            . '</div>'
            . '</div>';
    }
}
