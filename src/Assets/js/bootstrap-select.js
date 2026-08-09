import Selectpicker from '@crestapps/bootstrap-select';

const root = window.wikipressShadowRoot || document;

const initialize = (scope = root) => {
  if (!scope.querySelectorAll) return;

  scope.querySelectorAll('.wikipress-bootstrap-select').forEach((field) => {
    Selectpicker.getOrCreateInstance(field);
  });
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => initialize(), { once: true });
} else {
  initialize();
}

if (root !== document) {
  new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (node.nodeType !== Node.ELEMENT_NODE) return;
        if (node.matches?.('.wikipress-bootstrap-select')) {
          Selectpicker.getOrCreateInstance(node);
        }
        initialize(node);
      });
    });
  }).observe(root, { childList: true, subtree: true });
}
