import { Controller } from '@hotwired/stimulus';
import SimpleLightbox from "simplelightbox";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller<HTMLAnchorElement> {
  declare lightbox: SimpleLightbox;

  connect() {
    //new simpleLightbox with this element and group by rel attribute
    this.lightbox = new SimpleLightbox(this.element, {
      captionsData: "alt",
    });
  }
}
