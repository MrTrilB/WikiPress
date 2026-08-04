(() => {
    'use strict';

    function updateInternalWikiFields(container) {
        var enabled = container.querySelector('[data-internal-wiki-toggle]').checked;
        var accessType = container.querySelector('[data-internal-wiki-access-type]').value;

        container.querySelector('[data-internal-wiki-options]').hidden = !enabled;
        container.querySelector('[data-internal-wiki-roles]').hidden = !enabled || accessType !== 'roles';
        container.querySelector('[data-internal-wiki-permissions]').hidden = !enabled || accessType !== 'permissions';
    }

    function syncPermissions(input, selectedItems) {
        var values = input.closest('[data-internal-wiki-permissions]').querySelector('[data-bootstrap-search-values]');
        values.replaceChildren();
        selectedItems.forEach((item) => {
            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'internal_wiki_permissions[]';
            hidden.value = item.value;
            values.appendChild(hidden);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-internal-wiki-fields]').forEach((container) => {
            var input = container.querySelector('[data-bootstrap-search="permissions"]');
            if (!input) {
                return;
            }
            var data = JSON.parse(input.dataset.items || '[]');
            var selectedItems = JSON.parse(input.dataset.selectedItems || '[]');

            if (window.BootstrapSearch) {
                new window.BootstrapSearch(input, {
                    data: data,
                    multiSelect: true,
                    selectedItems: selectedItems,
                    maximumItems: 0,
                    onSelectItem: (items) => syncPermissions(input, items)
                });
                syncPermissions(input, selectedItems);
            }

            container.addEventListener('change', (event) => {
                if (event.target.matches('[data-internal-wiki-toggle], [data-internal-wiki-access-type]')) {
                    updateInternalWikiFields(container);
                }
            });

            updateInternalWikiFields(container);
        });
    });
})();