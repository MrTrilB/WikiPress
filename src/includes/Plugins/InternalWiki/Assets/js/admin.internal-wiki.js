const updateAccessFields = (root) => {
  const accessType = root.querySelector('[data-internal-wiki-access-type]');
  const roles = root.querySelector('[data-internal-wiki-roles], [data-internal-wiki-roles-select]');
  const permissions = root.querySelector('[data-internal-wiki-permissions], [data-internal-wiki-permissions-select]');

  if (!accessType || !roles || !permissions) return;

  const rolesContainer = roles.closest('tr, [data-internal-wiki-roles]') || roles;
  const permissionsContainer = permissions.closest('tr, [data-internal-wiki-permissions]') || permissions;
  const selectedType = accessType.value;
  rolesContainer.classList.toggle('d-none', selectedType !== 'roles');
  permissionsContainer.classList.toggle('d-none', selectedType !== 'permissions');
};

const initializeInternalWikiFields = (root = document) => {
  root.querySelectorAll('[data-internal-wiki-access-type]').forEach((accessType) => {
    const fieldContainer = accessType.closest('[data-internal-wiki-fields], form') || root;
    if (!accessType || accessType.dataset.internalWikiInitialized === 'true') return;

    accessType.dataset.internalWikiInitialized = 'true';
    accessType.addEventListener('change', () => updateAccessFields(fieldContainer));
    updateAccessFields(fieldContainer);
  });
};

const initializeInternalWikiController = () => {
  const root = document;
  initializeInternalWikiFields(root);

  if (root === document) {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType !== Node.ELEMENT_NODE) return;
          initializeInternalWikiFields(node);
        });
      });
    });

    observer.observe(document.body, { childList: true, subtree: true });
  }
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeInternalWikiController, { once: true });
} else {
  initializeInternalWikiController();
}