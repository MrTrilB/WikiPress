import Selectpicker from '../../../node_modules/@crestapps/bootstrap-select/dist/js/bootstrap-select.min.js';

const root = window.wikipressShadowRoot || document;

const initializeField = (field, options = {}) => {
  const modal = field.closest?.('.modal');
  if (modal && !modal.classList.contains('show')) {
    if (!field.__wikipressModalSelectHandler) {
      field.__wikipressModalSelectHandler = () => {
        field.__wikipressModalSelectHandler = null;
        initializeField(field, options);
      };
      modal.addEventListener('shown.bs.modal', field.__wikipressModalSelectHandler, { once: true });
    }
    return null;
  }

  const instance = Selectpicker.getOrCreateInstance(field, options);

  if (field.getRootNode?.() instanceof ShadowRoot && !instance.__wikipressShadowClickHandler) {
    instance.__wikipressShadowClickHandler = (event) => {
      event.preventDefault();
      instance.toggle(event);
    };
    instance.button.addEventListener('click', instance.__wikipressShadowClickHandler);
  }

  return instance;
};

window.wikipressBootstrapSelect = {
  initialize: initializeField,
};

const initialize = (scope = root) => {
  if (!scope.querySelectorAll) return;

  scope.querySelectorAll('.selectpicker').forEach((field) => {
    initializeField(field);
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
        if (node.matches?.('.selectpicker')) {
          initializeField(node);
        }
        initialize(node);
      });
    });
  }).observe(root, { childList: true, subtree: true });
}
