import { Controller } from '@hotwired/stimulus';

export default class extends Controller<HTMLFormElement> {
    static values = {
        action: String,
        target: String,
    }

    declare actionValue: string
    declare targetValue: string

    connect() {
        document.addEventListener("turbo:before-stream-render", this.beforeStreamRender)
    }

    disconnect() {
        document.removeEventListener("turbo:before-stream-render", this.beforeStreamRender)
    }

    beforeStreamRender(event: CustomEvent) {
        const fallbackToDefaultActions = event.detail.render

        console.log('turbo:before-stream-render');

        event.detail.render = (streamElement: { action: string; target: string; }) => {
            if (streamElement.action == this.actionValue && streamElement.target == this.targetValue) {
                return
            } else {
                fallbackToDefaultActions(streamElement)
            }
        }
    }
}
