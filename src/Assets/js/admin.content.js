document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.wikipress-inline-form').forEach((form) => {
    form.addEventListener('submit', () => {
      const submit = form.querySelector('[type="submit"]');
      if (submit) submit.disabled = true;
    });
  });
});