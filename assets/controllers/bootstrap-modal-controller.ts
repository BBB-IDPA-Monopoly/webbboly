import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';


export default class extends Controller<HTMLElement> {
  static values = {
    modalId: String,
    state: String,
  }

  declare modal: Modal;
  declare readonly modalIdValue: string;
  declare readonly stateValue: string;

  connect() {
    this.modal = Modal.getOrCreateInstance(document.getElementById(this.modalIdValue));
    if (this.stateValue === 'open') {
      this.open();
    }
  }

  disconnect() {
    this.modal.dispose();
  }

  open() {
    this.modal.show();
  }

  close() {
    this.modal.hide();
  }
}
