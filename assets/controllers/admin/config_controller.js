// assets/controllers/config_controller.js
import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = [ "schemaTextRow" ]

  connect() {
    this.toggleArticleField()
  }

  toggleArticleField() {
    const value = this.typeFieldTarget.value;
    const targetsMap = {
      '3': this.schemaTextRowTarget,
    };

    Object.values(targetsMap).forEach(target => {
      if (target) target.classList.add('hide');
    });

    if (targetsMap[value]) {
      targetsMap[value].classList.remove('hide');
    }
  }

  changeType() {
    this.toggleArticleField()
  }
}
