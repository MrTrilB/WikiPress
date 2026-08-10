import Selectpicker from '../../../node_modules/@crestapps/bootstrap-select/dist/js/bootstrap-select.min.js';

const root = window.wikipressShadowRoot || document;

const getOptionData = (field) => Array.from(field.options).map((option) => ({
  value: option.value,
  text: option.textContent,
  selected: option.selected,
  disabled: option.disabled,
  hidden: option.hidden,
  title: option.title,
  icon: option.dataset.icon,
}));

const initializeField = (field, options = {}) => {
  const instance = Selectpicker.getOrCreateInstance(field, {
    source: { data: getOptionData(field) },
    ...options,
  });

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
