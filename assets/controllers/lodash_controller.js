import { Controller } from '@hotwired/stimulus'
import _ from 'lodash'

export default class extends Controller {
    connect() {
        const logger = _.before(3, mess => console.log(mess))
    }
}
