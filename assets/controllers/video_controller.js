import { Controller } from '@hotwired/stimulus';
import videojs from 'video.js'

export default class extends Controller {
    static values = {
        src: {
            type: String,
            default: '',
        },
        type: {
            type: String,
            default: 'video/mp4',
        }
    }

    connect() {
        this.player = videojs(this.element, {
            controls: true,
            sources: [
                { src: this.srcValue, type: this.typeValue },
            ],
            // children: [
            //     'BigPlayButton',
            //     'PlayToggle',
            //     'FullscreenToggle',
            //     'MuteToggle',
            // ],
        })

        this.player.removeChild(videojs.getComponent('FullscreenToggle'))
    }
}
