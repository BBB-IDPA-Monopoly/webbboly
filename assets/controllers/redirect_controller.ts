import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        path: String,
        params: Object,
    }

    declare readonly pathValue: string;
    declare readonly paramsValue: object;

    connect() {
      this.redirect();
    }

    redirect() {
      const url = new URL(this.pathValue, window.location.origin);
      Object.keys(this.paramsValue).forEach(key => url.searchParams.append(key, this.paramsValue[key]));

      window.location.href = url.href;
    }
}
