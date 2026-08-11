const updateAccessFields = (root) => {
  const accessType = root.querySelector('[data-internal-wiki-access-type]');
  const roles = root.querySelector('[data-internal-wiki-roles]');
  const permissions = root.querySelector('[data-internal-wiki-permissions]');

  if (!accessType || !roles || !permissions) return;

  const selectedType = accessType.value;
  roles.classList.toggle('d-none', selectedType !== 'roles');
  permissions.classList.toggle('d-none', selectedType !== 'permissions');
};

const initializeInternalWikiFields = (root = document) => {
  root.querySelectorAll('[data-internal-wiki-fields]').forEach((fields) => {
    const accessType = fields.querySelector('[data-internal-wiki-access-type]');
    if (!accessType || accessType.dataset.internalWikiInitialized === 'true') return;

    accessType.dataset.internalWikiInitialized = 'true';
    accessType.addEventListener('change', () => updateAccessFields(fields));
    updateAccessFields(fields);
  });
};

document.addEventListener('DOMContentLoaded', () => initializeInternalWikiFields());