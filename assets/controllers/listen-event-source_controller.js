import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.eventSource = new EventSource('http://127.0.0.1:8000/event-source')

        this.eventSource.addEventListener('mess', event => {
            console.log(event.data, event.lastEventId)
        })

        setTimeout(() => {
            this.eventSource.close()
        }, 5000)
    }
}
