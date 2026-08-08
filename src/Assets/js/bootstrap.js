import * as bootstrap from 'bootstrap';
import Backdrop from 'bootstrap/js/src/util/backdrop.js';

class WikiPressModal extends bootstrap.Modal {
  _initializeBackDrop() {
    const root = this._element.getRootNode?.();
    const rootElement = root instanceof ShadowRoot ? root : document.body;

    return new Backdrop({
      isVisible: Boolean(this._config.backdrop),
      isAnimated: this._isAnimated(),
      rootElement,
    });
  }

  _showElement(relatedTarget) {
    const root = this._element.getRootNode?.();
    if (!(root instanceof ShadowRoot)) {
      super._showElement(relatedTarget);
      return;
    }

    if (!this._element.isConnected) {
      root.append(this._element);
    }

    this._element.style.display = 'block';
    this._element.removeAttribute('aria-hidden');
    this._element.setAttribute('aria-modal', true);
    this._element.setAttribute('role', 'dialog');
    this._element.scrollTop = 0;
    const modalBody = this._dialog?.querySelector('.modal-body');
    if (modalBody) {
      modalBody.scrollTop = 0;
    }
    void this._element.offsetHeight;
    this._element.classList.add('show');
    const transitionComplete = () => {
      if (this._config.focus) {
        this._focustrap.activate();
      }
      this._isTransitioning = false;
      this._element.dispatchEvent(new Event('shown.bs.modal', { bubbles: true }));
    };
    this._queueCallback(transitionComplete, this._dialog, this._isAnimated());
  }
}

if (typeof window !== 'undefined') {
  window.bootstrap = { ...bootstrap, Modal: WikiPressModal };
}
