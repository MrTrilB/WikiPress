import * as bootstrap from 'bootstrap';

// Expose Bootstrap to legacy scripts that expect a global
if (typeof window !== 'undefined') {
  window.bootstrap = bootstrap;
}