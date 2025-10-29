import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    console.log("Controller connected");
    
    // Listen for turbo frame load event
    this.element.addEventListener('turbo:frame-load', () => {
      console.log("Turbo frame loaded, initializing collection field");
      this.initializeCollectionField();
    });

    // Also try to initialize immediately in case content is already present
    setTimeout(() => {
      this.initializeCollectionField();
    }, 100);
  }

  initializeCollectionField() {
    console.log("1 - Starting initialization");
    // Find the collection container in the document
    const collectionContainer = document.querySelector('[data-ea-collection-field="true"]');
    
    if (!collectionContainer) {
      console.log("2 - No collection container found");
      return;
    }

    const itemsContainer = collectionContainer.querySelector('.ea-form-collection-items');
    const addButton = collectionContainer.querySelector('.field-collection-add-button');

    console.log("3 - Found elements");
    if (!itemsContainer || !addButton) {
      console.log("4 - Items container or add button not found");
      return;
    }

    console.log("Items container:", itemsContainer);
    console.log("Add button:", addButton);

    // Check if already initialized (to avoid duplicate listeners)
    if (addButton.dataset.initialized === 'true') {
      console.log("Already initialized, skipping");
      return;
    }
    
    console.log("Setting initialized flag");
    addButton.dataset.initialized = 'true';

    // Get current count
    let count = parseInt(collectionContainer.getAttribute('data-num-items'), 10) || 0;
    console.log("Initial count:", count);

    // Use direct onclick assignment instead of addEventListener
    addButton.onclick = (event) => {
      console.log("5 - CLICK HANDLER FIRED!");
      event.preventDefault();
      event.stopPropagation();

      const prototype = collectionContainer.getAttribute('data-prototype');
      if (!prototype) {
        console.log("6 - No prototype");
        console.error('No data-prototype found on collection field');
        return;
      }

      console.log("Creating new item with count:", count);

      // Replace __name__ placeholder with current count
      let newItemHtml = prototype.replace(/__name__/g, count);

      // Create DOM element from HTML string
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = newItemHtml;
      const newItem = tempDiv.firstElementChild;

      // Append the new item to items container
      itemsContainer.appendChild(newItem);

      // Increment count
      count++;
      collectionContainer.setAttribute('data-num-items', count);
      console.log("New count:", count);

      // Remove "Empty" badge if present
      const emptyBadge = itemsContainer.querySelector('.collection-empty');
      console.log("7 - Looking for empty badge");
      if (emptyBadge) {
        console.log("8 - Removing empty badge");
        emptyBadge.remove();
      }

      return false;
    };

    console.log("Click handler assigned via onclick");

    // Delegate remove button clicks using event delegation on document
    document.addEventListener('click', (event) => {
      const deleteButton = event.target.closest('.field-collection-delete-button');
      if (deleteButton && itemsContainer.contains(deleteButton)) {
        console.log("10 - Delete button clicked");
        event.preventDefault();
        event.stopPropagation();
        
        const item = deleteButton.closest('.field-collection-item');
        if (item) {
          console.log("11 - Removing item");
          item.remove();
          count--;
          collectionContainer.setAttribute('data-num-items', count);

          // Show "Empty" badge if no items remain
          if (itemsContainer.querySelectorAll('.field-collection-item').length === 0) {
            console.log("12 - No items left, showing empty badge");
            itemsContainer.innerHTML = '<div class="empty collection-empty"><span class="badge badge-secondary">Empty</span></div>';
          }
        }
        return false;
      }
    }, true);

    console.log("Collection field initialized successfully");
  }
}
