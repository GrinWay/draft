import { Controller } from '@hotwired/stimulus'
import * as L from 'leaflet'
import "leaflet.locatecontrol"
import "leaflet-draw"
import 'leaflet-routing-machine'
import 'leaflet.browser.print/src/leaflet.browser.print.js'
import Routing from 'fos-router'

// Tiles: https://leaflet-extras.github.io/leaflet-providers/preview/
export default class extends Controller {
    connect() {
        this.abortController = new AbortController()

        this.element.addEventListener('ux:map:pre-connect', this._onPreConnect.bind(this), { signal: this.abortController.signal })
        this.element.addEventListener('ux:map:connect', this._onConnect.bind(this), { signal: this.abortController.signal })
        this.element.addEventListener('ux:map:marker:before-create', this._onMarkerBeforeCreate.bind(this), { signal: this.abortController.signal })
        this.element.addEventListener('ux:map:marker:after-create', this._onMarkerAfterCreate.bind(this), { signal: this.abortController.signal })
        this.element.addEventListener('ux:map:info-window:before-create', this._onInfoWindowBeforeCreate.bind(this), { signal: this.abortController.signal })
        this.element.addEventListener('ux:map:info-window:after-create', this._onInfoWindowAfterCreate.bind(this), { signal: this.abortController.signal })

        // console.log(Routing.generate('app_event_source'))
    }

    disconnect() {
        this.abortController.abort()
    }

    /**
     * Event listener
     *
     * @param event
     * @private
     */
    _onPreConnect(event) {
    }

    /**
     * Event listener
     *
     * @param event
     * @private
     */
    _onConnect(event) {
        const { infoWindows, map, markers, polygons, polylines } = event.detail

        this.infoWindows = infoWindows
        this.map = map
        this.markers = markers
        this.polygons = polygons
        this.polylines = polylines

        L.control.scale({
            position: 'bottomright',
        }).addTo(this.map)

        const real = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_satellite/{z}/{x}/{y}{r}.{ext}', {
            minZoom: 0,
            maxZoom: 20,
            ext: 'jpg'
        })
        const dark = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.{ext}', {
            minZoom: 0,
            maxZoom: 20,
            ext: 'png'
        }).addTo(this.map)
        const baseLayers = {
            "Real": real,
            "Dark": dark,
        }
        const overLayers = {
            ...this.markers.reduce((acc, next) => ({ ...acc, [next.options.title]: next }), {}),
        }
        const options = {
            collapsed: false,
        }
        const controlLayers = L.control.layers(baseLayers, overLayers, options).addTo(this.map)

        //
        // this.map.on('click', event => {
        //     const marker = this.markers[0]
        //
        //     L.Routing.control({
        //         waypoints: [
        //             L.latLng(46.692201, 38.776985),
        //             L.latLng(event.latlng.lat, event.latlng.lng)
        //         ]
        //     }).on('routesfound', event => {
        //         event.routes[0].coordinates.forEach(({ lat, lng }, idx) => {
        //             setTimeout(() => marker.setLatLng([lat, lng]), idx * 10)
        //         })
        //     })
        //         .addTo(this.map)
        //
        // })

        //

        //
        const featureGroup = new L.FeatureGroup()
        this.map.addLayer(featureGroup)

        const draw = new L.Control.Draw({
            edit: {
                featureGroup,
                remove: false,
            },
        })
        this.map.addControl(draw)

        //
        this.map.on('draw:created', event => {
            featureGroup.addLayer(event.layer)
        })
    }

    /**
     * Event listener
     *
     * @param event
     * @private
     */
    _onMarkerBeforeCreate(event) {
        const icon = L.icon({
            iconUrl: event.detail.definition.extra.icon,
            iconSize: 50,
        })
        event.detail.definition.icon = icon
    }

    /**
     * Event listener
     *
     * @param event
     * @private
     */
    _onMarkerAfterCreate(event) {
    }

    /**
     * Event listener
     *
     * @param event
     * @private
     */
    _onInfoWindowBeforeCreate(event) {
    }

    /**
     * Event listener
     *
     * @param event
     * @private
     */
    _onInfoWindowAfterCreate(event) {
    }

    /**
     * Helper
     *
     * @private
     * @param args
     */
    #log(...args) {
        console.log(...args)
    }

    setDefaultView(event) {
        this.map.setView([46.692201, 38.776985], 15)
    }
}
