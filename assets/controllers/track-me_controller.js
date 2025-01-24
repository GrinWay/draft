import { Controller } from '@hotwired/stimulus';
import { MD5 } from 'object-hash';

export default class extends Controller {
    connect() {
        this.lockedElements = []
        this.geoable = null
        this.locked = false

        if (!navigator?.geolocation) {
            this.geoable = false
            console.error(`Geolocation is not supported by this browser`)
        } else {
            this.geoable = true
        }
    }

    disconnect() {
        if (this.trackInterval) {
            clearTimeout(this.trackInterval)
        }
    }

    start(event) {
        if (!this.geoable || this.locked) {
            return
        }
        this.locked = true

        const btn = event.currentTarget
        this.lock(btn)
        // btn.innerHTML = btn.innerHTML + `<span class="spinner-border" style="width: 1.1rem; height: 1.1rem"></span>`

        this.trackInterval = setInterval(() => {
            navigator.geolocation.getCurrentPosition(this.track.bind(this))
        }, 10_000)
    }

    track(position) {
        const latitude = position.coords.latitude
        const longitude = position.coords.longitude

        const uri = '/api/tracks'
        const options = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/ld+json',
            },
            body: JSON.stringify({
                "latitude": '' + latitude,
                "longitude": '' + longitude,
            })
        }
        fetch(uri, options)
    }

    stop(event) {
        if (!this.geoable || !this.trackInterval) {
            return
        }

        clearInterval(this.trackInterval)
        this.unlock()
        this.locked = false
    }

    lock(element) {
        this.lockedElements.push(element)

        element.setAttribute('disabled', null)
        element.classList.add('disabled')
    }

    unlock(element = null) {
        const callback = el => {
            el.removeAttribute('disabled')
            el.classList.remove('disabled')
        }

        if (null == element) {
            this.lockedElements.forEach(el => {
                callback(el)
            })
            this.lockedElements = []
        } else {
            callback(element)
        }
    }
}
