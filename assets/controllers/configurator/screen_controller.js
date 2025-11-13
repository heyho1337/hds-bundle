import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    change(event) {
        // Get current screen data
        const { currentCheckbox, currentScreenId, currentScreenStep } = this.getCurrentScreen(event);
        
        // Save the current step value
        this.setValue(currentScreenId);
        
        // Change to next screen
        this.changeScreen(currentCheckbox, currentScreenId);
    }

    finish(event) {
        // Get current screen data
        const { currentCheckbox, currentScreenId, currentScreenStep } = this.getCurrentScreen(event);
        
        // Save the current step value
        this.endSetup();
        
        // Change to next screen
        this.changeScreen(currentCheckbox, currentScreenId);
    }

    getCurrentScreen(event) {
        const currentCheckbox = event.currentTarget;
        const currentScreenId = parseInt(event.params.id);
        const currentScreenStep = parseInt(event.params.step);
        
        return { currentCheckbox, currentScreenId, currentScreenStep };
    }

    changeScreen(currentCheckbox, currentScreenId) {
        // Uncheck current checkbox
        currentCheckbox.checked = false;
        
        // Get the next screen ID
        const nextScreenId = currentScreenId + 1;
        
        // Find the next section by data-id
        const nextSection = document.querySelector(`section[data-id="${nextScreenId}"]`);
        console.log(nextSection);
        
        if (nextSection) {
            const nextCheckbox = nextSection.querySelector('.next input[type="checkbox"]');
            if (nextCheckbox) {
                nextCheckbox.checked = true;
            }
        }
    }

    setValue(currentScreenId) {
        // Find the current section
        const currentSection = document.querySelector(`section[data-id="${currentScreenId}"]`);
        
        if (!currentSection) return;
        
        // Find the input inside the input-group
        const inputGroup = currentSection.querySelector('.input-group');
        if (!inputGroup) return;
        
        // Get the input element (could be input, select, or textarea)
        const input = inputGroup.querySelector('input, select, textarea');
        if (!input) return;
        
        // Get the input's name and value
        const fieldName = input.name;
        let fieldValue;
        
        // Handle different input types
        if (input.type === 'checkbox') {
            fieldValue = input.checked ? '1' : '0';
        } else if (input.multiple) {
            // Handle multi-select - send as JSON string
            fieldValue = JSON.stringify(Array.from(input.selectedOptions).map(option => option.value));
        } else {
            fieldValue = input.value;
        }
        
        // Extract the actual field name from "basic[field_name]"
        const fieldMatch = fieldName.match(/\[([^\]]+)\]/);
        const actualFieldName = fieldMatch ? fieldMatch[1] : fieldName;
        
        // Prepare the data to send
        const formData = new FormData();
        formData.append('field_name', actualFieldName);
        formData.append('field_value', fieldValue);
        formData.append('step', currentScreenStep);
        
        console.log('Sending:', {
            field_name: actualFieldName,
            field_value: fieldValue,
            step: currentScreenStep
        });
        
        // Make the AJAX call
        fetch('/setup/update', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Step saved successfully');
            } else {
                console.error('Error saving step:', data.error);
            }
        })
        .catch(error => {
            console.error('AJAX error:', error);
        });
    }

    endSetup() {
        // Make the AJAX call
        fetch('/setup/finish', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Step saved successfully');
            } else {
                console.error('Error saving step:', data.error);
            }
        })
        .catch(error => {
            console.error('AJAX error:', error);
        });
    }
}
