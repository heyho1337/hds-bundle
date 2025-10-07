import { Controller } from '@hotwired/stimulus';
import * as FilePond from 'filepond';

export default class extends Controller {
    static targets = ['input', 'classInput', 'pathInput', 'form']

    connect() {
        // Initialize FilePond if input target is present
        if (this.hasInputTarget) {
            this.pond = FilePond.create(this.inputTarget, {
                allowMultiple: true,
                acceptedFileTypes: ['image/jpg', 'image/jpeg', 'image/png', 'image/webp', 'image/heic'],
                server: null,
                storeAsFile: true,
            });
        }

        // Set values from window.lastModalTrigger if available
        const trigger = window.lastModalTrigger;
        if (trigger) {
            if (this.hasClassInputTarget) {
                this.classInputTarget.value = trigger.getAttribute('data-class') || '';
            }
            if (this.hasPathInputTarget) {
                this.pathInputTarget.value = trigger.getAttribute('data-path') || '';
            }
        }
    }
}
