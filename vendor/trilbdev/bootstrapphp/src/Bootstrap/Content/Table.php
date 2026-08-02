<?php

declare(strict_types=1);

namespace BootstrapPHP\Bootstrap\Content;

use BootstrapPHP\Includes\Renderer;

final class Table
{
    public static function render(array $headers, array $rows, array $attributes = [], bool $striped = false, bool $bordered = false): string
    {
        $headerCells = '';
        foreach ($headers as $header) {
            $headerCells .= Renderer::component('th', (string) $header, []);
        }

        $headerRow = Renderer::component('tr', $headerCells, []);
        $thead = Renderer::component('thead', Renderer::component('tr', $headerCells, []), []);

        $bodyRows = '';
        foreach ($rows as $row) {
            $cells = '';
            foreach ($row as $cell) {
                $cells .= Renderer::component('td', (string) $cell, []);
            }
            $bodyRows .= Renderer::component('tr', $cells, []);
        }

        $tbody = Renderer::component('tbody', $bodyRows, []);

        $classes = ['table'];
        if ($striped) {
            $classes[] = 'table-striped';
        }
        if ($bordered) {
            $classes[] = 'table-bordered';
        }

        return Renderer::component('table', $thead . $tbody, [
            'classes' => $classes,
            'attributes' => $attributes,
            'escape' => false,
        ]);
    }
}



