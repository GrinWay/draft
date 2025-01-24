// TODO: JS ThemeManager
/**
 * Logic of theme changing with "set" method:
 *
 * You MUST do step (1) and step (2) before using this themeManager (see below)
 *
 * If "theme" string passed, it sets body "data-app-theme" attribute with "theme" value
 *     (1) YOU have to describe css styles essentially via CSS VARIABLEs for body[data-app-theme="<theme>"]
 *     (PS: You will probably call theme like: "light", "dark" as always)
 *
 * If "theme" string was not passed, it checks if "bgColor", "color" are strings and if they are it sets ":root" css variables
 *    (2) YOU have to set CSS VARIABLEs (see below) in your ":root" of css styles
 *
 * themeManager will READ and WRITE ":root" css variables
 * and only READ body[data-app-theme="<theme>"] css variables
 *
 * You can always get won theme colors via callback of set method
 *
 * Usage:
 *     const theme = 'dark'
 *     let bgColorVar = null
 *     const callback = wonThemeEl => bgColorVar = themeManager.getStyleProp(wonThemeEl, ThemeManager.THEME_BG_COLOR_CSS_VAR)
 *     themeManager.set({ theme, callback })
 *     // you saved bgColorVar use it outside :)
 */
class ThemeManager {
    constructor() {
        this.bodyEl = document.querySelector('body')
        this.rootEl = document.querySelector(':root')
    }

    /**
     * CSS VARIABLE
     */
    static get THEME_BG_COLOR_CSS_VAR() {
        return '--app-theme-bg-color'
    }

    /**
     * CSS VARIABLE
     */
    static get THEME_COLOR_CSS_VAR() {
        return '--app-theme-color'
    }

    /**
     * API
     * Pass "theme", "callback" if you know the theme name OR "bgColor", "color", "callback" if you know only colors
     *
     * Usage:
     *         const theme = 'dark'
     *         let colorBgValue = null
     *
     *         // callback is needed to get the theme "#??????" color value that won
     *         const callback = wonThemeEl => colorBgValue = themeManager.getStyleProp(wonThemeEl, ThemeManager.THEME_BG_COLOR_CSS_VAR)
     *         themeManager.set({ theme, callback })
     *
     *         // Imagine you have outside places where you need to set theme colors
     *         this.someObj.someBgSetter1(colorBgValue)
     *         this.someObj.someBgSetter2(colorBgValue)
     *         this.someObj.someBgSetter3(colorBgValue)
     *
     * @param string theme Has priority over bgColor and color
     * @param callback callback Use to get the won theme some color: themeManager.getStyleProp(wonThemeEl, ThemeManager.THEME_BG_COLOR_CSS_VAR) for instance
     */
    set({ theme, bgColor, color, callback }) {
        if (typeof callback !== 'function') {
            callback ??= wonThemeEl => {
                return
            }
        }

        let wonThemeEl = null

        if (String(theme) === theme) {
            this.#setBodyStyle({ theme })
            wonThemeEl = this.bodyEl
            callback(wonThemeEl)
            return this
        }

        if (String(bgColor) === bgColor && String(color) === color) {
            this.#setRootStyle({ bgColor, color })
            wonThemeEl = this.rootEl
            callback(wonThemeEl)
            return this
        }

        return this
    }

    /**
     * API
     * Works well inside callback of this.set method
     */
    getStyleProp(el, property) {
        if (typeof el === 'object' && el !== null && String(property) === property) {
            return getComputedStyle(el)?.getPropertyValue(property)
        }
        return undefined
    }

    /**
     * API
     * Sets style (css) property with value
     */
    setStyleProp(el, property, value) {
        if (String(property) === property && String(value) === value && typeof el === 'object' && el !== null) {
            el?.style?.setProperty(property, value)
        }
        return this
    }

    #setBodyStyle({ theme }) {
        if (String(theme) === theme) {
            this.bodyEl.dataset.appTheme = theme
        }
        return this
    }

    #setRootStyle({ bgColor, color }) {
        this.bodyEl.dataset.appTheme = ''
        this.setStyleProp(this.rootEl, ThemeManager.THEME_BG_COLOR_CSS_VAR, bgColor)
        this.setStyleProp(this.rootEl, ThemeManager.THEME_COLOR_CSS_VAR, color)
        return this
    }
}

const themeManager = new ThemeManager

export { ThemeManager, themeManager }
