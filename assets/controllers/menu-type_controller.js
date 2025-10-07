// assets/controllers/menu-type_controller.js
import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = [ "typeField", "articleRow", "blogRow", 'tagRow', 'fileRow', 'blogCategoryRow' ]

  connect() {
    this.toggleArticleField()
  }

  toggleArticleField() {
    const value = this.typeFieldTarget.value;
    const targetsMap = {
      '1': this.articleRowTarget,
      '2': this.blogRowTarget,
      '3': this.tagRowTarget,
      '4': this.blogCategoryRowTarget,
      '5': this.fileRowTarget,
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
