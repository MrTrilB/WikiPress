document.addEventListener('DOMContentLoaded', () => {
  const panel = document.querySelector('#wikipress-settings-panel');
  const config = window.wikipressSettingsTabs;
  if (!panel || !config) return;

  const stateFromHash = () => {
    const hash = window.location.hash.replace(/^#/, '') || panel.dataset.currentTab || 'general';
    if (hash.indexOf('layout-') === 0) return { tab: 'layout', section: hash.replace('layout-', '') || 'general' };
    return { tab: hash, section: 'general' };
  };

  const setActive = (tab, section) => {
    document.querySelectorAll('[data-wikipress-settings-tab]').forEach((link) => {
      const active = link.dataset.wikipressSettingsTab === tab && (!link.dataset.wikipressSettingsSection || link.dataset.wikipressSettingsSection === section);
      link.classList.toggle('active', active);
      link.setAttribute('aria-selected', active ? 'true' : 'false');
      if (active) link.setAttribute('aria-current', 'page');
      else link.removeAttribute('aria-current');
    });
  };

  const bindForms = () => document.querySelectorAll('.wikipress-settings-form, .wikipress-import-form').forEach((form) => {
    form.addEventListener('submit', () => {
      const submit = form.querySelector('[type="submit"]');
      if (submit) submit.disabled = true;
    });
  });

  const loadTab = (tab, section, updateHash = true) => {
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
      })
      .catch(() => { window.location.reload(); })
      .finally(() => panel.removeAttribute('aria-busy'));
  };

  document.addEventListener('click', (event) => {
    const link = event.target.closest('#wikipress-settings-panel [data-wikipress-settings-tab]');
    if (!link) return;
    event.preventDefault();
    event.stopPropagation();
    loadTab(link.dataset.wikipressSettingsTab, link.dataset.wikipressSettingsSection || 'general');
  }, true);
  window.addEventListener('popstate', () => { const state = stateFromHash(); loadTab(state.tab, state.section, false); });
  window.addEventListener('hashchange', () => { const state = stateFromHash(); loadTab(state.tab, state.section, false); });

  const initial = stateFromHash();
  setActive(initial.tab, initial.section);
  if (window.location.hash && (initial.tab !== panel.dataset.currentTab || initial.section !== panel.dataset.currentSection)) loadTab(initial.tab, initial.section, false);
  bindForms();
});