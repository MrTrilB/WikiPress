document.addEventListener('DOMContentLoaded', () => {
  const root = window.wikipressShadowRoot || document;
  root.querySelectorAll('.wikipress-editor-form').forEach((form) => {
    form.addEventListener('submit', () => {
      const submit = form.querySelector('[type="submit"]');
      if (submit) submit.disabled = true;
    });
  });
});