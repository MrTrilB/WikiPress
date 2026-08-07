document.addEventListener('DOMContentLoaded', () => {
  const root = window.wikipressShadowRoot || document;
  const panel = root.querySelector('#wikipress-settings-panel');
  const config = window.wikipressSettingsTabs;
  if (!panel || !config) return;

  const stateFromHash = () => {
    const hash = window.location.hash.replace(/^#/, '') || panel.dataset.currentTab || 'general';
    if (hash.indexOf('layout-') === 0) return { tab: 'layout', section: hash.replace('layout-', '') || 'general' };
    return { tab: hash, section: 'general' };
  };

  const setActive = (tab, section) => {
    root.querySelectorAll('[data-wikipress-settings-tab]').forEach((link) => {
      const active = link.dataset.wikipressSettingsTab === tab && (!link.dataset.wikipressSettingsSection || link.dataset.wikipressSettingsSection === section);
      link.classList.toggle('active', active);
      link.setAttribute('aria-selected', active ? 'true' : 'false');
      if (active) link.setAttribute('aria-current', 'page');
      else link.removeAttribute('aria-current');
    });
  };

  const bindForms = () => root.querySelectorAll('.wikipress-settings-form, .wikipress-import-form').forEach((form) => {
    form.addEventListener('submit', () => {
      const submit = form.querySelector('[type="submit"]');
      if (submit) submit.disabled = true;
    });
  });

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
        window.bootstrap?.Modal.getOrCreateInstance(modal).hide();
      })
      .catch(() => { modal.querySelector('.modal-body')?.classList.add('is-invalid'); })
      .finally(() => { button.disabled = false; });
  };

  const openPluginModal = (trigger) => {
    const targetSelector = trigger.dataset.bsTarget;
    const scope = trigger.getRootNode?.() || root;
    const modal = scope.querySelector?.(targetSelector) || root.querySelector(targetSelector);
    if (!modal || !window.bootstrap?.Modal) return false;

    window.bootstrap.Modal.getOrCreateInstance(modal).show(trigger);
    return true;
  };

  const bindPluginModals = () => root.querySelectorAll('[data-bs-toggle="modal"][data-bs-target]').forEach((trigger) => {
    if (trigger.dataset.wikipressModalBound) return;

    trigger.dataset.wikipressModalBound = 'true';
    trigger.addEventListener('click', (event) => {
      event.preventDefault();
      openPluginModal(trigger);
    });
  });

  const activateLayoutTab = (button) => {
    const target = root.querySelector(button.dataset.bsTarget);
    if (!target) return;

    const current = root.querySelector('#wikipress-layout-tab .nav-link.active');
    const currentPane = root.querySelector('#wikipress-layout-tab-content .tab-pane.active');
    if (current === button && currentPane === target) return;

    root.querySelectorAll('#wikipress-layout-tab .nav-link').forEach((tab) => {
      const active = tab === button;
      tab.classList.toggle('active', active);
      tab.setAttribute('aria-selected', active ? 'true' : 'false');
    });

    if (currentPane) {
      currentPane.classList.remove('show');
      window.setTimeout(() => currentPane.classList.remove('active'), 150);
    }

    target.classList.add('active');
    requestAnimationFrame(() => target.classList.add('show'));
  };

  const loadTab = (tab, section, updateHash = true) => {
    const currentContent = panel.querySelector('.wikipress-settings-tab-content');
    if (currentContent) currentContent.classList.add('is-loading');
    panel.setAttribute('aria-busy', 'true');
    const body = new URLSearchParams({ action: 'wikipress_load_settings_tab', nonce: config.nonce, tab, layout_section: section });
    fetch(config.ajaxUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }, body })
      .then((response) => response.json())
      .then((response) => {
        if (!response.success || !response.data.html) throw new Error('Unable to load settings tab');
        panel.innerHTML = response.data.html;
        panel.dataset.currentTab = response.data.tab;
        panel.dataset.currentSection = response.data.layout_section;
        setActive(response.data.tab, response.data.layout_section);
        if (updateHash) window.history.pushState({}, '', `${window.location.pathname}${window.location.search}#${response.data.tab === 'layout' ? `layout-${response.data.layout_section}` : response.data.tab}`);
        bindForms();
        bindPluginModals();
        const nextContent = panel.querySelector('.wikipress-settings-tab-content');
        if (nextContent) requestAnimationFrame(() => nextContent.classList.remove('is-loading'));
      })
      .catch(() => { panel.classList.remove('is-loading'); })
      .finally(() => panel.removeAttribute('aria-busy'));
  };

  root.addEventListener('click', (event) => {
    const modalTrigger = event.target.closest?.('[data-bs-toggle="modal"][data-bs-target]');
    if (modalTrigger) {
      event.preventDefault();
      openPluginModal(modalTrigger);
      return;
    }

    const layoutButton = event.target.closest?.('[data-wikipress-layout-tab]');
    if (layoutButton) {
      event.preventDefault();
      activateLayoutTab(layoutButton);
      window.history.pushState({}, '', `${window.location.pathname}${window.location.search}#layout-${layoutButton.dataset.wikipressLayoutTab}`);
      panel.dataset.currentTab = 'layout';
      panel.dataset.currentSection = layoutButton.dataset.wikipressLayoutTab;
      return;
    }

    const link = event.target.closest?.('#wikipress-settings-panel [data-wikipress-settings-tab]');
    if (!link) return;
    event.preventDefault();
    event.stopPropagation();
    loadTab(link.dataset.wikipressSettingsTab, link.dataset.wikipressSettingsSection || 'general');
  }, true);
  root.addEventListener('change', (event) => {
    const toggle = event.target.closest?.('[data-wikipress-plugin-toggle]');
    if (toggle) togglePlugin(toggle);
  }, true);
  root.addEventListener('click', (event) => {
    const saveButton = event.target.closest?.('[data-plugin-settings-save]');
    if (saveButton) savePluginSettings(saveButton);
  }, true);
  const navigateFromHash = () => {
    const state = stateFromHash();
    if ('layout' === state.tab) {
      const button = root.querySelector(`[data-wikipress-layout-tab="${state.section}"]`);
      if (button) {
        activateLayoutTab(button);
        panel.dataset.currentTab = 'layout';
        panel.dataset.currentSection = state.section;
      }
      return;
    }
    loadTab(state.tab, state.section, false);
  };
  window.addEventListener('popstate', navigateFromHash);
  window.addEventListener('hashchange', navigateFromHash);

  const initial = stateFromHash();
  setActive(initial.tab, initial.section);
  if ('layout' === initial.tab) {
    const button = root.querySelector(`[data-wikipress-layout-tab="${initial.section}"]`);
    if (button) activateLayoutTab(button);
  }
  if (window.location.hash && 'layout' !== initial.tab && (initial.tab !== panel.dataset.currentTab || initial.section !== panel.dataset.currentSection)) loadTab(initial.tab, initial.section, false);
  bindForms();
  bindPluginModals();
});