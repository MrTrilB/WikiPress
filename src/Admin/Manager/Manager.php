<?php

namespace TrilBDev\WikiPress\Admin\Manager;

use TrilBDev\WikiPress\Assets\Assets;
use TrilBDev\WikiPress\Admin\Manager\UI\Footer;
use TrilBDev\WikiPress\Admin\Manager\UI\Header;
use TrilBDev\WikiPress\Admin\Manager\UI\Sidebar;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Manager {
    /**
     * Register one asset bundle for a group of admin pages.
     *
     * @param Assets $assets Asset registry.
     * @param array<int, string> $pages Admin page slugs.
     * @param string $bundle Compiled bundle name.
     * @return void
     */
    protected function register_page_assets( Assets $assets, array $pages, string $bundle ): void {
        foreach ( $pages as $page ) {
            $assets->register_page( $page, $this->assets( $bundle ) );
        }
    }

    /**
     * Build the asset definition for an admin bundle.
     *
     * @param string $bundle Compiled bundle name.
     * @return array<string, array<int, array<string, mixed>>> Asset definition.
     */
    protected function assets( string $bundle ): array {
        return [
            'styles'  => [ [ 'handle' => 'wikipress-admin-' . $bundle, 'src' => WIKIPRESS_URL . 'src/Assets/dist/css/admin.' . $bundle . '.css' ] ],
            'scripts' => [ [ 'handle' => 'wikipress-admin-' . $bundle, 'src' => WIKIPRESS_URL . 'src/Assets/dist/js/admin.' . $bundle . '.js', 'deps' => [ 'wikipress-bootstrap' ], 'in_footer' => true ] ],
        ];
    }

    /**
     * Render the shared admin page header.
     *
     * @param string $title Page title.
     * @return void
     */
    protected function header( string $title ): void {
        echo '<div class="wrap wikipress-admin">';
        Header::render();
        ?>
        <main class="wikipress-admin-main">
            <div class="container-fluid wikipress-shell px-3 px-lg-4 py-4">
                <div class="row g-4">
                    <?php Sidebar::render(); ?>
                    <section class="col-12 col-lg-9 col-xl-10" aria-labelledby="wikipress-page-title">
                        <div class="wikipress-page-heading d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
                            <div>
                                <p class="text-uppercase small fw-semibold text-primary mb-2"><?php esc_html_e( 'WikiPress workspace', 'wikipress' ); ?></p>
                                <h1 class="h2 mb-0" id="wikipress-page-title"><?php echo esc_html( $title ); ?></h1>
                            </div>
                        </div>
        <?php
    }

    /**
     * Render the shared admin page footer.
     *
     * @return void
     */
    protected function footer(): void {
        Footer::render();
        echo '</div>';
    }

    /**
     * Render a dashboard statistic card.
     *
     * @param string $label Card label.
     * @param mixed $value Card value.
     * @param string $slug Destination admin page slug.
     * @return void
     */
    protected function card( string $label, $value, string $slug ): void {
        printf(
            '<div class="col-md-6 col-xl-3 mb-4"><div class="card wikipress-dashboard-card h-100 shadow-sm"><div class="card-body"><h2 class="h6 text-muted">%s</h2><p class="display-6 mb-0"><a class="text-decoration-none" href="%s">%s</a></p></div></div></div>',
            esc_html( $label ),
            esc_url( admin_url( 'admin.php?page=' . $slug ) ),
            esc_html( (string) $value )
        );
    }

}
