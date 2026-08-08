document.addEventListener('DOMContentLoaded', () => {
	const root = window.wikipressShadowRoot || document;
	const config = window.wikipressSettingsTabs;
	if (!config) return;

	const removeModalBackdrop = (modal) => {
		const scope = modal?.getRootNode?.() || root;
		scope.querySelector?.('.wikipress-plugin-modal-backdrop')?.remove();
		document.body.classList.remove('modal-open');
	};

	const closePluginModal = (modal) => {
		if (!modal) return;
		window.bootstrap?.Modal.getOrCreateInstance(modal).hide();
		modal.classList.remove('show');
		modal.setAttribute('aria-hidden', 'true');
		modal.removeAttribute('aria-modal');
		modal.style.display = 'none';
		removeModalBackdrop(modal);
	};

	const openPluginModal = (trigger) => {
		const targetSelector = trigger.dataset.bsTarget;
		const scope = trigger.getRootNode?.() || root;
		const modal = scope.querySelector?.(targetSelector) || root.querySelector(targetSelector);
		if (!modal) return;

		const modalScope = modal.getRootNode?.() || root;
		modalScope.querySelector?.('.wikipress-plugin-modal-backdrop')?.remove();
		window.bootstrap?.Modal.getOrCreateInstance(modal).show(trigger);
		modal.classList.add('show');
		modal.setAttribute('aria-hidden', 'false');
		modal.setAttribute('aria-modal', 'true');
		modal.style.display = 'block';
		modal.style.zIndex = '1055';

		const backdrop = document.createElement('div');
		backdrop.className = 'modal-backdrop fade show wikipress-plugin-modal-backdrop';
		backdrop.style.zIndex = '1050';
		backdrop.addEventListener('click', () => closePluginModal(modal));
		modalScope.appendChild(backdrop);
		document.body.classList.add('modal-open');
	};

	const togglePlugin = (toggle) => {
		const enabled = toggle.checked;
		toggle.disabled = true;
		const body = new URLSearchParams({ action: 'wikipress_toggle_plugin', nonce: config.pluginNonce, slug: toggle.dataset.pluginSlug || '', enabled: enabled ? '1' : '0' });
		fetch(config.ajaxUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }, body })
			.then((response) => response.json())
			.then((response) => {
				if (!response.success) throw new Error('Unable to save plugin state');
			})
			.catch(() => { toggle.checked = !enabled; })
			.finally(() => { toggle.disabled = false; });
	};

	const savePluginSettings = (button) => {
		const modal = button.closest('.wikipress-plugin-settings-modal');
		const form = modal?.querySelector('[data-plugin-settings-form]');
		if (!modal || !form) return;

		button.disabled = true;
		const body = new URLSearchParams(new FormData(form));
		body.set('action', 'wikipress_save_plugin_settings');
		body.set('nonce', config.pluginSettingsNonce);
		body.set('slug', form.dataset.pluginSlug || '');
		fetch(config.ajaxUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }, body })
			.then((response) => response.json())
			.then((response) => {
				if (!response.success) throw new Error('Unable to save plugin settings');
				closePluginModal(modal);
			})
			.catch(() => { modal.querySelector('.modal-body')?.classList.add('is-invalid'); })
			.finally(() => { button.disabled = false; });
	};

	root.addEventListener('click', (event) => {
		const trigger = event.target.closest?.('[data-bs-toggle="modal"][data-bs-target]');
		if (trigger) {
			event.preventDefault();
			openPluginModal(trigger);
			return;
		}

		const dismiss = event.target.closest?.('.wikipress-plugin-settings-modal [data-bs-dismiss="modal"]');
		if (dismiss) {
			event.preventDefault();
			closePluginModal(dismiss.closest('.wikipress-plugin-settings-modal'));
			return;
		}

		const save = event.target.closest?.('[data-plugin-settings-save]');
		if (save) savePluginSettings(save);
	}, true);

	root.addEventListener('change', (event) => {
		const toggle = event.target.closest?.('[data-wikipress-plugin-toggle]');
		if (toggle) togglePlugin(toggle);
	}, true);
});
