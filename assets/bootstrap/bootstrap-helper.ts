import { Tooltip } from 'bootstrap';

const BootstrapHelper = class {
  constructor() {
    this.activateTooltips();
  }

  activateTooltips() {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));
  }
}

export default new BootstrapHelper();
