(() => {
    'use strict';

    function updateInternalWikiFields(container) {
        var enabled = container.querySelector('[data-internal-wiki-toggle]').checked;
        var accessType = container.querySelector('[data-internal-wiki-access-type]').value;

        container.querySelector('[data-internal-wiki-options]').hidden = !enabled;
        container.querySelector('[data-internal-wiki-roles]').hidden = !enabled || accessType !== 'roles';
        container.querySelector('[data-internal-wiki-permissions]').hidden = !enabled || accessType !== 'permissions';
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-internal-wiki-fields]').forEach((container) => {
            if (!container.querySelector('[data-internal-wiki-toggle]')) {
                return;
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