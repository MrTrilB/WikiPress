document.querySelectorAll('.wikipress-role-form').forEach((form) => {
  const steps = Array.from(form.querySelectorAll('[data-role-step]'));
  const next = form.querySelector('[data-role-next]');
  const back = form.querySelector('[data-role-back]');
  let currentStep = 0;

  const updateStep = () => {
    steps.forEach((step, index) => step.classList.toggle('d-none', index !== currentStep));
    if (next) next.classList.toggle('d-none', currentStep === steps.length - 1);
    if (back) back.classList.toggle('d-none', currentStep === 0);
    const identity = steps[0]?.querySelectorAll('input[required]') || [];
    if (next) next.disabled = Array.from(identity).some((input) => !input.checkValidity());
  };

  next?.addEventListener('click', () => {
    if (steps[0]?.querySelector('input:invalid')) return;
    currentStep = Math.min(currentStep + 1, steps.length - 1);
    updateStep();
  });
  back?.addEventListener('click', () => {
    currentStep = Math.max(currentStep - 1, 0);
    updateStep();
  });
  form.querySelectorAll('input[required]').forEach((input) => input.addEventListener('input', updateStep));
  updateStep();
});
