const getTaxonomyOptions = async (field, search = '') => {
  const endpoint = field.dataset.wikipressTaxonomyEndpoint;
  if (!endpoint) return [];

  const url = new URL(endpoint, window.location.href);
  url.searchParams.set('per_page', '100');
  url.searchParams.set('orderby', 'name');
  url.searchParams.set('order', 'asc');
  if (search) url.searchParams.set('search', search);

  const response = await fetch(url.toString(), { headers: { Accept: 'application/json' } });
  if (!response.ok) throw new Error(`Taxonomy request failed: ${response.status}`);

  const terms = await response.json();
  return Array.isArray(terms) ? terms.map((term) => ({ value: String(term.id), text: term.name })) : [];
};

const getTaxonomySource = (field) => ({
  data: (callback) => callback(Array.from(field.options).map((option) => ({
    value: option.value,
    text: option.textContent,
    selected: option.selected,
    disabled: option.disabled,
    hidden: option.hidden,
    title: option.title,
    icon: option.dataset.icon,
  }))),
  search: (callback, ...parameters) => {
    getTaxonomyOptions(field, parameters[1]).then(callback).catch(() => callback([]));
  },
  ...(field.dataset.wikipressTaxonomyCreate === 'true' ? {
    create: async (callback, searchValue) => {
      const response = await fetch(field.dataset.wikipressTaxonomyEndpoint, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          'X-WP-Nonce': field.dataset.wikipressRestNonce || '',
        },
        body: JSON.stringify({ name: searchValue }),
      });
      if (!response.ok) return;

      const term = await response.json();
      callback({ text: term.name, value: String(term.id) });
    },
  } : {}),
});

const initializeTaxonomyPickers = (root) => {
  root.querySelectorAll('[data-wikipress-taxonomy-endpoint]').forEach((field) => {
    window.wikipressBootstrapSelect?.initialize(field, { source: getTaxonomySource(field) });
  });
};

document.addEventListener('DOMContentLoaded', () => {
  const root = document;
  initializeTaxonomyPickers(root);
  root.querySelectorAll('.wikipress-inline-form').forEach((form) => {
    form.addEventListener('submit', () => {
      const submit = form.querySelector('[type="submit"]');
      if (submit) submit.disabled = true;
    });
  });

  root.querySelectorAll('[data-wikipress-media-picker]').forEach((button) => {
    button.addEventListener('click', () => {
      if (!window.wp?.media) return;

      const targetId = button.dataset.mediaTarget;
      const target = root.querySelector(`#${targetId}`);
      const preview = root.querySelector(`[data-media-preview="${targetId}"]`);
      const clear = root.querySelector(`[data-wikipress-media-clear][data-media-target="${targetId}"]`);
      if (!target || !preview) return;

      const frame = window.wp.media({
        title: 'Choose Wiki image',
        button: { text: 'Use image' },
        multiple: false,
        library: { type: 'image' },
      });
      frame.on('select', () => {
        const attachment = frame.state().get('selection').first().toJSON();
        target.value = attachment.id || '';
        preview.replaceChildren();
        if (attachment.url) {
          const image = document.createElement('img');
          image.className = 'img-fluid rounded';
          image.src = attachment.url;
          image.alt = '';
          preview.appendChild(image);
        }
        clear?.classList.toggle('d-none', !target.value);
      });
      frame.open();
    });
  });

  root.querySelectorAll('[data-wikipress-media-clear]').forEach((button) => {
    button.addEventListener('click', () => {
      const targetId = button.dataset.mediaTarget;
      const target = root.querySelector(`#${targetId}`);
      const preview = root.querySelector(`[data-media-preview="${targetId}"]`);
      if (target) target.value = '';
      if (preview) preview.replaceChildren();
      button.classList.add('d-none');
    });
  });

  const request = async (action, values) => {
    const data = new FormData();
    data.append('action', action);
    data.append('nonce', window.wikipressWikiManager?.nonce || '');
    Object.entries(values).forEach(([key, value]) => {
      data.append(key, value);
    });
    const response = await fetch(window.wikipressWikiManager?.ajaxUrl || '', { method: 'POST', body: data });
    const json = await response.json();
    if (!json.success) throw new Error(json.data?.message || 'Request failed');
    return json;
  };

  root.addEventListener('click', async (event) => {
    const deleteWiki = event.target.closest('[data-wikipress-delete-wiki]');
    const deletePage = event.target.closest('[data-wikipress-delete-page]');
    const deleteTerm = event.target.closest('[data-wikipress-delete-term]');
    const addTerm = event.target.closest('[data-wikipress-add-term]');
    const editTerm = event.target.closest('[data-wikipress-edit-term]');
    const cancelTerm = event.target.closest('[data-wikipress-cancel-term]');
    const saveSettings = event.target.closest('[data-wikipress-save-wiki-settings]');
    const saveTerm = event.target.closest('[data-wikipress-save-term]');
    try {
      if (deleteWiki && window.confirm('Delete this Wiki and all of its Wiki Pages?')) {
        await request('wikipress_delete_wiki', { wiki_id: deleteWiki.dataset.wikipressDeleteWiki });
        window.location.reload();
      } else if (deletePage && window.confirm('Delete this Wiki Page?')) {
        await request('wikipress_delete_wiki_page', { page_id: deletePage.dataset.wikipressDeletePage });
        window.location.reload();
      } else if (deleteTerm && window.confirm('Remove this term from the Wiki?')) {
        await request('wikipress_delete_wiki_term', { wiki_id: deleteTerm.closest('.modal')?.id.match(/(\d+)$/)?.[1] || '', term_id: deleteTerm.dataset.wikipressDeleteTerm, taxonomy: deleteTerm.dataset.wikipressTaxonomy });
        window.location.reload();
      } else if (addTerm) {
        const form = addTerm.closest('.tab-pane')?.querySelector('[data-wikipress-term-form]');
        if (form) bootstrap.Collapse.getOrCreateInstance(form).show();
      } else if (editTerm) {
        const form = editTerm.closest('.tab-pane')?.querySelector('[data-wikipress-term-form]');
        if (form) {
          form.dataset.wikipressTermId = editTerm.dataset.wikipressEditTerm;
          form.querySelector('[data-wikipress-term-name]').value = editTerm.dataset.wikipressTermName || '';
          form.querySelector('[data-wikipress-term-slug]').value = editTerm.dataset.wikipressTermSlug || '';
          bootstrap.Collapse.getOrCreateInstance(form).show();
        }
      } else if (cancelTerm) {
        const form = cancelTerm.closest('[data-wikipress-term-form]');
        if (form) bootstrap.Collapse.getOrCreateInstance(form).hide();
      } else if (saveSettings) {
        const modal = saveSettings.closest('.modal');
        const values = Object.fromEntries(new FormData(modal.querySelector('.modal-body')).entries());
        values.name = modal.querySelector('[name="wikipress_wiki_settings[name]"]')?.value || '';
        values.slug = modal.querySelector('[name="wikipress_wiki_settings[slug]"]')?.value || '';
        values.navigation = modal.querySelector('[name="wikipress_wiki_settings[navigation]"]')?.value || 'horizontal';
        await request('wikipress_save_wiki_settings', { wiki_id: saveSettings.dataset.wikipressSaveWikiSettings, settings: JSON.stringify(values) });
        window.location.reload();
      } else if (saveTerm) {
        const form = saveTerm.closest('[data-wikipress-term-form]');
        const modal = saveTerm.closest('.modal');
        await request('wikipress_save_wiki_term', { wiki_id: modal?.id.match(/(\d+)$/)?.[1] || '', term_id: form.dataset.wikipressTermId || '', taxonomy: form.dataset.wikipressTaxonomy, name: form.querySelector('[data-wikipress-term-name]').value, slug: form.querySelector('[data-wikipress-term-slug]').value, description: form.querySelector('[data-wikipress-term-description]').value });
        window.location.reload();
      }
    } catch (error) {
      window.alert(error.message);
    }
  });

  root.addEventListener('change', (event) => {
    if (event.target.matches('[data-wikipress-use-global]')) {
      const value = event.target.closest('.row')?.querySelector('[data-wikipress-global-value]');
      if (value) value.disabled = event.target.checked;
    }
  });
});