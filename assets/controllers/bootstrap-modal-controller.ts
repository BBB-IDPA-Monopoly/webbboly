import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';


export default class extends Controller {
  static values = {
    modalId: String,
  }

  declare modal: Modal;
  declare readonly modalIdValue: string;

  connect() {
    this.modal = Modal.getOrCreateInstance(document.getElementById(this.modalIdValue));
    this.open();
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
