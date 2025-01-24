// TODO: JS TypeChecker
class TypeChecker {
    constructor() {
    }

    /**
     * API
     */
    isObject(object) {
        return typeof object === 'object' && null !== object && !Array.isArray(object)
    }

    /**
     * API
     */
    isNotObject(object) {
        return !this.isObject(object)
    }

    /**
     * API
     */
    isFunction(func) {
        return typeof func === 'function'
    }

    /**
     * API
     */
    isNotFunction(func) {
        return !this.isFunction(func)
    }

    /**
     * API
     */
    isString(string) {
        return String(string) === string
    }

    /**
     * API
     */
    isNotString(string) {
        return !this.isString(string)
    }
}

const typeChecker = new TypeChecker()

export { TypeChecker, typeChecker }
