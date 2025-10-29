import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static values = {
    url: String
  }

  save(event) {
    event.preventDefault();

    const turboFrame = event.target.closest('turbo-frame');
    const form = turboFrame.querySelector('form');
    const formData = new FormData(form);

    const url = this.urlValue;

    console.log(formData);
    console.log(url);
    fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    })
    .then(async response => {
      if (response.ok) {
        const data = await response.json();
        this.closeModal();
        window.location.reload(); // Reload to get the flash messages from the backend
      } else if ([422, 400].includes(response.status)) {
        const errorData = await response.json().catch(() => null);
        if (errorData) {
          this.showFormErrors(errorData.errors || errorData);
        } else {
          const errorText = await response.text();
        }
      } else {
        const errorText = await response.text();
      }
    })
    .catch(error => {
    });
  }

  showFormErrors(errors) {
    alert('Validation errors: ' + JSON.stringify(errors));
  }

  // This closes your modal frame by removing it from DOM or hiding it.
  closeModal() {
    // If you use a turbo-frame modal, remove it or hide it.
    // Simple example:
    const frame = document.getElementById('modal-frame');
    if (frame) {
      frame.remove();
    }
    // If you use a custom modal,
    // document.querySelector('.modal').classList.add('hidden');
    // or trigger your modal closing function.
  }
}
