document.addEventListener('DOMContentLoaded', () => {
    const root = document;
    const config = window.wikipressSettingsTabs;
    if (!config) return;

    //
    // --- MODAL HANDLING (Bootstrap-native) ---
    //

    const openPluginModal = (trigger) => {
        const selector = trigger.dataset.bsTarget;
        const scope = trigger.getRootNode?.() || root;
        const modal = scope.querySelector(selector) || root.querySelector(selector);
        if (!modal) return;

        const instance = bootstrap.Modal.getOrCreateInstance(modal);
        instance.show();
    };

    const closePluginModal = (modal) => {
        const instance = bootstrap.Modal.getOrCreateInstance(modal);
        return new Promise((resolve) => {
            const finish = () => {
                if (!root.querySelector('.modal.show')) {
                    root.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');
                }
                resolve();
            };

            modal.addEventListener('hidden.bs.modal', finish, { once: true });
            instance.hide();
        });
    };

    const showPluginNotice = (message, type = 'success') => {
        const container = root.querySelector('#wikipress-settings-panel .wikipress-settings-tab-content');
        if (!container) return;

        container.querySelectorAll('[data-wikipress-plugin-notice]').forEach((notice) => notice.remove());

        const notice = document.createElement('div');
        notice.className = `alert alert-${type} alert-dismissible fade show mb-4`;
        notice.setAttribute('role', 'alert');
        notice.dataset.wikipressPluginNotice = 'true';
        notice.append(document.createTextNode(message));

        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'btn-close';
        close.setAttribute('data-bs-dismiss', 'alert');
        close.setAttribute('aria-label', 'Close');
        notice.append(close);
        container.prepend(notice);
    };

    //
    // --- PLUGIN TOGGLE ---
    //

    const togglePlugin = (toggle) => {
        const enabled = toggle.checked;
        toggle.disabled = true;

        const body = new URLSearchParams({
            action: 'wikipress_toggle_plugin',
            nonce: config.pluginNonce,
            slug: toggle.dataset.pluginSlug || '',
            enabled: enabled ? '1' : '0'
        });

        fetch(config.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body
        })
        .then((response) => response.json())
        .then((response) => {
            if (!response.success) throw new Error('Unable to save plugin state');
        })
        .catch(() => {
            toggle.checked = !enabled;
        })
        .finally(() => {
            toggle.disabled = false;
        });
    };

    //
    // --- SAVE PLUGIN SETTINGS ---
    //

    const savePluginSettings = (button) => {
        const modal = button.closest('.wikipress-plugin-settings-modal');
        const form = modal?.querySelector('[data-plugin-settings-form]');
        if (!modal || !form) return;

        button.disabled = true;

        const body = new URLSearchParams(new FormData(form));
        body.set('action', 'wikipress_save_plugin_settings');
        body.set('nonce', config.pluginSettingsNonce);
        body.set('slug', form.dataset.pluginSlug || '');

        fetch(config.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body
        })
        .then((response) => response.json())
        .then((response) => {
            if (!response.success) {
                throw new Error(response.data?.message || 'Unable to save plugin settings');
            }
            return closePluginModal(modal).then(() => {
                showPluginNotice(response.data?.message || 'Plugin settings saved successfully.');
            });
        })
        .catch((error) => {
            closePluginModal(modal).then(() => {
                showPluginNotice(error.message || 'Unable to save plugin settings.', 'danger');
            });
        })
        .finally(() => {
            button.disabled = false;
        });
    };

    //
    // --- EVENT LISTENERS ---
    //

    root.addEventListener('click', (event) => {
        // Open modal
        const trigger = event.target.closest?.('[data-bs-toggle="modal"][data-bs-target]');
        if (trigger) {
            event.preventDefault();
            event.stopPropagation();
            openPluginModal(trigger);
            return;
        }

        // Close modal
        const dismiss = event.target.closest?.('.wikipress-plugin-settings-modal [data-bs-dismiss="modal"]');
        if (dismiss) {
            event.preventDefault();
            const modal = dismiss.closest('.wikipress-plugin-settings-modal');
            closePluginModal(modal);
            return;
        }

        // Save settings
        const save = event.target.closest?.('[data-plugin-settings-save]');
        if (save) {
            savePluginSettings(save);
        }
    }, true);

    root.addEventListener('change', (event) => {
        const toggle = event.target.closest?.('[data-wikipress-plugin-toggle]');
        if (toggle) {
            togglePlugin(toggle);
        }
    }, true);
});
