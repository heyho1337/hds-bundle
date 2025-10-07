import { Controller } from '@hotwired/stimulus';
import Turbo from '@hotwired/turbo';

export default class extends Controller {
  connect() {
    // Wait for the modal-frame element to exist before attaching listeners
    this.attachFrameListener();

    // Listen globally for Turbo form submissions inside the modal frame
    this.submitListener = (event) => {
      if (event.target.closest('#modal-frame form')) {
        this.isSubmitting = true;
      }
    };
    document.addEventListener('turbo:submit-start', this.submitListener);
  }

  disconnect() {
    this.detachFrameListener();

    if (this.submitListener) {
      document.removeEventListener('turbo:submit-start', this.submitListener);
      this.submitListener = null;
    }
  }

  attachFrameListener() {
    const frame = document.getElementById('modal-frame');
    if (!frame) {
      // If the frame doesn't exist yet, check again shortly (in case it's loaded dynamically)
      this.frameListenerTimeout = setTimeout(() => this.attachFrameListener(), 100);
      return;
    }

    if (this.frameListenerTimeout) {
      clearTimeout(this.frameListenerTimeout);
      this.frameListenerTimeout = null;
    }

    if (!this.frameListenerAttached) {
      frame.addEventListener('turbo:before-fetch-response', this.handleRedirect);
      this.frameListenerAttached = true;
    }
  }

  detachFrameListener() {
    const frame = document.getElementById('modal-frame');
    if (!frame) return;
    if (this.frameListenerAttached) {
      frame.removeEventListener('turbo:before-fetch-response', this.handleRedirect);
      this.frameListenerAttached = false;
    }
  }

  handleRedirect = (event) => {
    console.log(event);
    if (!this.isSubmitting) return;

    const frame = event.target;

    // Log for debugging
    console.log('handleRedirect called');

    if (!frame.querySelector('form')) {
      event.preventDefault();

      // Close the Bootstrap modal
      const modalEl = document.getElementById('modal');
      if (modalEl) {
        const modalInstance = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.hide();
      }

      this.isSubmitting = false;

      // Reload the whole page with Turbo to show flash messages
      window.Turbo.visit(window.location.href);
    } else {
      // Form still exists (validation errors, etc.)
      this.isSubmitting = false;
    }
  };

  load(event) {
    event.preventDefault();
    const button = event.currentTarget;
    window.lastModalTrigger = button;
    const url = button.dataset.modalUrl;
    const title = button.dataset.modalTitle || '';

    const modalLabel = document.getElementById('modalLabel');
    if (modalLabel) {
      modalLabel.textContent = title;
    }

    const frame = document.getElementById('modal-frame');
    if (frame) {
      frame.src = url;
    }
  }
}
