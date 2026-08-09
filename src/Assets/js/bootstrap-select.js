import Selectpicker from '@crestapps/bootstrap-select';

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

const initialize = (scope = root) => {
  if (!scope.querySelectorAll) return;

  scope.querySelectorAll('.wikipress-bootstrap-select').forEach((field) => {
    Selectpicker.getOrCreateInstance(field, {
      source: { data: getOptionData(field) },
    });
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
          Selectpicker.getOrCreateInstance(node, {
            source: { data: getOptionData(node) },
          });
        }
        initialize(node);
      });
    });
  }).observe(root, { childList: true, subtree: true });
}
