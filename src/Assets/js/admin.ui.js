document.addEventListener('DOMContentLoaded', () => {
  const root = window.wikipressShadowRoot || document;

  root.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
    window.bootstrap?.Tooltip.getOrCreateInstance(element);
  });

  root.querySelectorAll('[data-permalink-field="permalink"]').forEach((field) => {
    const tokenButtons = field.parentElement.querySelectorAll('[data-permalink-token]');

    const refreshTokens = () => {
      tokenButtons.forEach((button) => {
        button.classList.toggle('d-none', field.value.includes(button.dataset.permalinkToken));
      });
    };

    tokenButtons.forEach((button) => {
      button.addEventListener('click', () => {
        const token = button.dataset.permalinkToken || '';
        if (!token || field.value.includes(token)) return;

        const start = field.selectionStart ?? field.value.length;
        const end = field.selectionEnd ?? start;
        const left = field.value.slice(0, start).replace(/\/+$/, '');
        const right = field.value.slice(end).replace(/^\/+/, '');
        const prefix = left ? `${left}/` : '';
        const suffix = right ? `/${right}` : '';
        field.value = `${prefix}${token}/${suffix}`.replace(/\/{2,}/g, '/');
        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.focus();
      });
    });

    field.addEventListener('input', refreshTokens);
    refreshTokens();
  });
});