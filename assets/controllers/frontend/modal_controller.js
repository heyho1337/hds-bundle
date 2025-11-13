import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

export default class extends Controller {
    static targets = ['modal', 'modalBody'];

    static values = {
        url: String
    };

    openModal(event) {
        event.preventDefault();
        this.urlValue = event.currentTarget.getAttribute('data-url');
        console.log(this.urlValue);
        this.modalBodyTarget.src = this.urlValue;

        const modalElement = this.modalTarget;
        const modal = Modal.getInstance(modalElement) || new Modal(modalElement);
        modal.show();
    }


    submitForm(event) {
        event.preventDefault();
        const form = event.target;

        fetch(this.urlValue, {
            method: form.method,
            body: new FormData(form),
            headers: { 'Accept': 'text/vnd.turbo-stream.html' },
            credentials: 'same-origin'
        })
        .then(response => response.text())
        .then(html => {
            this.modalBodyTarget.innerHTML = html;

            const modalElement = this.modalTarget;
            const modal = Modal.getInstance(modalElement) || new Modal(modalElement);
            modal.hide();
            this.dispatch('success');
        });
    }
}
