import { Controller } from '@hotwired/stimulus';
import * as FilePond from 'filepond';

export default class extends Controller {
    static targets = ['input']

    connect() {
        if (this.hasInputTarget) {
            this.pond = FilePond.create(this.inputTarget, {
                allowMultiple: true,
                acceptedFileTypes: ['image/jpg', 'image/jpeg', 'image/png', 'image/webp', 'image/heic'],
                server: null,
                storeAsFile: true,
            });
        }
    }
}