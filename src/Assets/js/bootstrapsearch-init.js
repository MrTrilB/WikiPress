document.querySelectorAll('[data-bootstrap-search]').forEach((field) => {
  if (typeof window.BootstrapSearch !== 'function') return;

  let options;
  try {
    options = JSON.parse(field.dataset.bootstrapSearch);
  } catch (error) {
    return;
  }

  const hiddenFields = Array.from(document.querySelectorAll(`[data-bootstrap-search-value="${field.id}"]`));
  const valueKey = options.value || 'value';
  const setValues = (items) => {
    const selected = Array.isArray(items) ? items : [items];
    if (!options.multiSelect) {
      hiddenFields[0].value = selected[0]?.[valueKey] ?? selected[0]?.value ?? selected[0] ?? '';
      return;
    }

    const template = hiddenFields[0];
    hiddenFields.slice(1).forEach((hidden) => hidden.remove());
    selected.forEach((item, index) => {
      const hidden = index === 0 ? template : template.cloneNode();
      hidden.value = item?.[valueKey] ?? item?.value ?? item ?? '';
      if (index > 0) template.after(hidden);
    });
    if (selected.length === 0) template.value = '';
  };

  options.onSelectItem = setValues;
  new window.BootstrapSearch(field, options);
  if (options.selectedItems?.length) setValues(options.selectedItems);
});
