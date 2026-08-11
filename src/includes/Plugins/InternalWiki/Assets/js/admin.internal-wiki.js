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
  const fields = root.matches?.('[data-internal-wiki-fields]')
    ? [root]
    : root.querySelectorAll('[data-internal-wiki-fields]');

  fields.forEach((fieldContainer) => {
    const accessType = fieldContainer.querySelector('[data-internal-wiki-access-type]');
    if (!accessType || accessType.dataset.internalWikiInitialized === 'true') return;

    accessType.dataset.internalWikiInitialized = 'true';
    accessType.addEventListener('change', () => updateAccessFields(fieldContainer));
    updateAccessFields(fieldContainer);
  });
};

const initializeInternalWikiController = () => {
  const root = window.wikipressShadowRoot || document;
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