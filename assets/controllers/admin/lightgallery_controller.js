import { Controller } from '@hotwired/stimulus';
import lightGallery from '../../vendor/lightgallery/lightgallery.es5.js';

// Import any plugins if you use them, e.g. thumbnails, zoom
// import lgThumbnail from 'lightgallery/plugins/thumbnail';
// import lgZoom from 'lightgallery/plugins/zoom';

export default class extends Controller {
    connect() {
        this.lg = lightGallery(this.element, {
            
        });
    }

    disconnect() {
        if (this.lg) {
            this.lg.destroy();
        }
    }
}
