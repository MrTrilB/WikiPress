import * as bootstrap from 'bootstrap';

// Expose Bootstrap to legacy scripts that expect a global
if (typeof window !== 'undefined') {
  window.bootstrap = bootstrap;
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
    bootstrap.Tooltip.getOrCreateInstance(element);
  });
});