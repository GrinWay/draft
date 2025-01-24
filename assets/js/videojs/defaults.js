import videojs from 'video.js'

//###> DEFAULTS ###
// videojs.options.autoplay = true
videojs.options.autoSetup = false
//###< DEFAULTS ###

//###> LANG ###
videojs.addLanguage('ru', {
    Play: 'Включить',
    Pause: 'Остановить',
    'Current Time': 'Текущее время',
    'Duration': 'Длительность',
    'Remaining Time': 'Оставшееся время',
});
//###< LANG ###

//###> PLUGIN ###
const Plugin = videojs.getPlugin('plugin')

class AppPlugin extends Plugin {
    #player = null

    constructor(player, options) {
        super(player, options)

        this.#player = player
    }

    log() {
        this.#player.log('LOG FROM PLUGIN')
    }
}

videojs.registerPlugin('appPlugin', AppPlugin)
//###< PLUGIN ###

//###> COMPONENTS ###

//###< COMPONENTS ###
