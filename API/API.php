<?php
/**
 * TrilB.Dev Plugin - Wiki API
 *
 * Delegates Wiki page operations to the shared Pages service.
 *
 * @package TrilBDev
 * @subpackage Includes\Wiki\API
 * @since 1.0.0
 */

namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\API;

use MrTrilB\TrilBDevPlugin\Includes\Wiki\Pages\Pages;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class API {
    public function listPages( array $query_args = [] ): array {
        return Pages::list_pages( $query_args );
    }

    public function getPage( string $pageId ): array {
        return Pages::get_by_id( absint( $pageId ) );
    }

    public function createPage( array $payload ): array {
        return Pages::create_from_payload( $payload );
    }

    public function updatePage( string $pageId, array $payload ): array {
        return Pages::update_from_payload( absint( $pageId ), $payload );
    }

    public function deletePage( string $pageId ): bool {
        return Pages::delete_by_id( absint( $pageId ) );
    }

    public function getPageBySlug( string $slug ): array {
        return Pages::get_by_slug( sanitize_title( $slug ) );
    }
}
