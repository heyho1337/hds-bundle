import Sortable from 'sortablejs'
import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
  static values = {
    paramName: String
  }

  connect() {
    this.sortables = []

    this.element.querySelectorAll('ul[data-sortable="true"]').forEach(ul => {
      console.log("a");
      const sortable = Sortable.create(ul, {
        group: 'nested',
        animation: 150,
        fallbackOnBody: true,
        swapThreshold: 0.65,

        // For debugging try handle: null to enable dragging whole <li>
        handle: '.menu-item',

        onEnd: this.onEnd.bind(this),
      });
      console.log("b");
      this.sortables.push(sortable)
    })
  }

  onEnd(evt) {
    console.log("c");
    const list = evt.from  // the <ul> containing siblings moved within
    if (!list) return

    const items = Array.from(list.querySelectorAll('li[data-id]'))

    console.log(items);

    items.forEach((item, index) => {
        const updateUrl = item.dataset.sortableUpdateUrl
        console.log(updateUrl);
        console.log(item.dataset);
        if (!updateUrl) return

        const body = {
        [this.paramNameValue]: index + 1  // 1-based order
        }

        console.log(updateUrl);

        fetch(updateUrl, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
        }).then(response => {
        if (!response.ok) {
            console.error(`Failed to update order for id ${item.dataset.id}`)
        }
        }).catch(console.error)
    })
    }
}
