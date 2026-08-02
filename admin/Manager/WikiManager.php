<?php
/**
 * TrilB.Dev Plugin - Wiki Manager Module
 *
 * Main coordinator for wiki management interface
 *
 * @package TrilBDev
 * @subpackage Admin\Wiki\Manager
 * @since 1.0.0
 */

namespace MrTrilB\TrilBDevPlugin\Admin\Wiki\Manager;

use MrTrilB\TrilBDevPlugin\Includes\Wiki\API\API as WikiAPI;

class WikiManager {
    private $wiki_api;

    public function __construct() {
        $this->wiki_api = new WikiAPI();
    }

    public function getWikiPage( string $pageId ): array {
        return $this->wiki_api->getPage( $pageId );
    }

    public function createWikiPage( array $payload ): array {
        return $this->wiki_api->createPage( $payload );
    }

    public function updateWikiPage( string $pageId, array $payload ): array {
        return $this->wiki_api->updatePage( $pageId, $payload );
    }

    public function deleteWikiPage( string $pageId ): bool {
        return $this->wiki_api->deletePage( $pageId );
    }
}