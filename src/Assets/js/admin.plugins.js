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

        const existingBackdrop = root.querySelector('.modal-backdrop');
        if (existingBackdrop && !root.querySelector('.modal.show')) {
            existingBackdrop.remove();
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }

        const instance = bootstrap.Modal.getOrCreateInstance(modal);
        instance.show();
    };

    const closePluginModal = (modal) => {
        if (!modal) {
            return Promise.resolve();
        }

        const instance = bootstrap.Modal.getOrCreateInstance(modal);
        return new Promise((resolve) => {
            const finish = () => {
                if (!root.querySelector('.modal.show')) {
                    root.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');
                }
                instance.dispose();
                resolve();
            };

            modal.addEventListener('hidden.bs.modal', finish, { once: true });
            instance.hide();
        });
    };

    const showPluginNotice = (alertMarkup) => {
        const container = root.querySelector('#wikipress-settings-panel .wikipress-settings-tab-content');
        if (!container || !alertMarkup) return;

        container.querySelectorAll('[data-wikipress-alert]').forEach((notice) => notice.remove());
        container.insertAdjacentHTML('afterbegin', alertMarkup);
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
                const error = new Error(response.data?.message || 'Unable to save plugin settings');
                error.alert = response.data?.alert;
                throw error;
            }
            return closePluginModal(modal).then(() => {
                showPluginNotice(response.data?.alert);
            });
        })
        .catch((error) => {
            closePluginModal(modal).then(() => {
                showPluginNotice(error.alert);
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
