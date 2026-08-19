const initializeRoleForms = () => {
  const root = window.wikipressShadowRoot || document;

  root.querySelectorAll('.wikipress-role-form').forEach((form) => {
    if (form.dataset.roleValidationInitialized) return;
    form.dataset.roleValidationInitialized = 'true';
  const steps = Array.from(form.querySelectorAll('[data-role-step]'));
  const next = form.querySelector('[data-role-next]');
  const back = form.querySelector('[data-role-back]');
  const isCreateForm = form.hasAttribute('data-role-create');
  const nameInput = form.querySelector('#wikipress-add-role-name');
  const slugInput = form.querySelector('#wikipress-add-role-slug');
  const identity = steps[0]?.querySelectorAll('input[required]') || [];
  const existingData = steps[0]?.querySelector('[data-role-existing-names]');
  const existingNames = JSON.parse(existingData?.dataset.roleExistingNames || '[]');
  const existingSlugs = JSON.parse(existingData?.dataset.roleExistingSlugs || '[]');
  let slugManuallyEdited = false;
  let currentStep = 0;

  const setFeedback = (input, feedback, message, valid) => {
    if (!input || !feedback) return;
    input.classList.toggle('is-valid', valid);
    input.classList.toggle('is-invalid', !valid);
    feedback.textContent = message;
    feedback.classList.toggle('valid-feedback', valid);
    feedback.classList.toggle('invalid-feedback', !valid);
  };

  const slugFromName = (value) => value
    .toLowerCase()
    .replace(/\s+/g, '-')
    .replace(/[^a-z0-9_-]+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '');

  const validateCreateFields = () => {
    if (!isCreateForm || !nameInput || !slugInput) return true;
    const name = nameInput.value.trim();
    const slug = slugInput.value.trim();
    const nameFeedback = form.querySelector('[data-role-name-feedback]');
    const slugFeedback = form.querySelector('[data-role-slug-feedback]');
    const nameValid = name.length > 0 && /^[A-Za-z0-9 _-]+$/.test(name)
      && !existingNames.includes(name.toLowerCase());
    const slugValid = slug.length > 0 && /^[a-z0-9_-]+$/.test(slug)
      && !existingSlugs.includes(slug.toLowerCase());

    setFeedback(nameInput, nameFeedback, name.length === 0 ? 'Role name is required.'
      : !/^[A-Za-z0-9 _-]+$/.test(name) ? 'Use letters, numbers, spaces, underscores, and hyphens only.'
        : !nameValid ? 'A role with this name already exists.' : 'Role name is available.', nameValid);
    setFeedback(slugInput, slugFeedback, slug.length === 0 ? 'Role slug is required.'
      : !/^[a-z0-9_-]+$/.test(slug) ? 'Use lowercase letters, numbers, underscores, and hyphens only.'
        : !slugValid ? 'A role with this slug already exists.' : 'Role slug is available.', slugValid);
    form.classList.add('was-validated');
    return nameValid && slugValid;
  };

  const updateStep = () => {
    steps.forEach((step, index) => {
      step.classList.toggle('d-none', index !== currentStep);
    });
    if (next) next.classList.toggle('d-none', currentStep === steps.length - 1);
    if (back) back.classList.toggle('d-none', currentStep === 0);
    if (next) next.disabled = isCreateForm ? !validateCreateFields()
      : Array.from(identity).some((input) => !input.checkValidity());
  };

  next?.addEventListener('click', () => {
    if (isCreateForm ? !validateCreateFields() : steps[0]?.querySelector('input:invalid')) return;
    currentStep = Math.min(currentStep + 1, steps.length - 1);
    updateStep();
  });
  back?.addEventListener('click', () => {
    currentStep = Math.max(currentStep - 1, 0);
    updateStep();
  });
  nameInput?.addEventListener('input', () => {
    if (!slugManuallyEdited) slugInput.value = slugFromName(nameInput.value);
    updateStep();
  });
  slugInput?.addEventListener('input', () => {
    slugManuallyEdited = true;
    slugInput.value = slugInput.value.toLowerCase();
    updateStep();
  });
  form.querySelectorAll('input[required]').forEach((input) => {
    input.addEventListener('input', updateStep);
  });
  form.addEventListener('submit', (event) => {
    if (isCreateForm && !validateCreateFields()) {
      event.preventDefault();
    }
  });
  updateStep();
  });
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeRoleForms);
} else {
  initializeRoleForms();
}
