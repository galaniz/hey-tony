/**
 * Components: nav
 *
 * @param args [object] {
 *  @param outer [HTMLElement]
 *  @param inner [HTMLElement]
 *  @param scaleTo [array]
 *  @param maxOffset [boolean]
 *  @param friction [float]
 *  @param transition [string]
 *  @param reduceMotion [boolean]
 *  @param getScales [function]
 * }
 */

/* Imports */

import { config } from '@alanizcreative/formation/lib/config/config'
import { prefix, onResize, onScroll } from '@alanizcreative/formation/lib/utils/utils'

/* Class */

class NavigationScaleBrowser {
  /**
   * Constructor
   */

  constructor (args) {
    /**
     * Public variables
     */

    const {
      outer = null,
      inner = null,
      scaleTo = [],
      maxOffset = false,
      friction = 1,
      transition = 'transform 400ms ease',
      reduceMotion = config.reduceMotion,
      getScales = () => {}
    } = args

    this.outer = outer
    this.inner = inner
    this.scaleTo = scaleTo
    this.maxOffset = maxOffset
    this.friction = friction // slow down scaling ( eg. 1.5 makes it slower than 1 )
    this.transition = transition
    this.reduceMotion = reduceMotion
    this.getScales = getScales

    /**
     * Internal variables
     */

    /* Keep track of transform scale */

    this._currentScale = 1

    /* Keep track of scale to by breakpoint */

    this._currentScaleTo = 1

    /* Keep track if scaled down or not */

    this._scaledDown = false

    /* For getting correct scale to */

    this._viewportWidth = window.innerWidth

    /* For prevent scroll on open */

    this._html = document.documentElement

    /**
     * Initialize
     */

    const init = this._initialize()

    if (!init) {
      return false
    }
  }

  /**
   * Initialize
   */

  _initialize () {
    /* Check that required variables not null */

    if (!this.outer || !this.inner) {
      return false
    }

    /* Set low and high for scale to breakpoints */

    const scaleToLength = this.scaleTo.length

    this.scaleTo.forEach((s, i) => {
      const low = s.width
      let high = 99999

      if (scaleToLength > 1 && i < scaleToLength - 1) {
        high = this.scaleTo[i + 1].width
      }

      s.low = low
      s.high = high
    })

    this._setScaleTo()

    /* Store height of outer element before scaling */

    this._outerHeight = this.outer.clientHeight
    this._ogOuterHeight = this._outerHeight

    /* Scroll position on load */

    if (window.scrollY >= this._ogOuterHeight) {
      this._scaledDown = true
      this._setTransformValues(this._currentScaleTo)
    }

    /* Scroll event */

    onScroll(() => {
      this._scroll()
    }, 10)

    /* Resize event */

    onResize(() => {
      this._resize()
    }, 100)

    return true
  }

  /**
   * Helpers: getters
   */

  _getInnerScale () {
    return this._ogOuterHeight / this._outerHeight
  }

  _getTranslateY (innerScale) {
    return (this._outerHeight / 2) - (this._ogOuterHeight / 2)
  }

  _getTransformValues (scale) {
    this._setOuterHeight(scale)

    const innerScale = this._getInnerScale()
    const translateY = this._getTranslateY(innerScale)

    return {
      scale,
      innerScale,
      translateY
    }
  }

  /**
   * Helpers: setters
   */

  _setScaleTo () {
    this.scaleTo.forEach((s) => {
      if (this._viewportWidth >= s.low && this._viewportWidth < s.high) {
        this._currentScaleTo = s.scale
      }
    })
  }

  _setOuterHeight (scale) {
    this._outerHeight = this._ogOuterHeight * scale
  }

  _setTransformValues (scale, transition = false) {
    if (this._scaledDown && scale < 0.9) {
      this.outer.setAttribute('data-nav-scale', 'down')
    } else {
      this.outer.setAttribute('data-nav-scale', 'default')
    }

    const transformValues = this._getTransformValues(scale)

    if (transformValues.scale === this._currentScaleTo) {
      this.outer.setAttribute('data-nav-scale-to', true)
    } else {
      this.outer.removeAttribute('data-nav-scale-to')
    }

    if (transition && !this.reduceMotion) {
      prefix('transition', this.outer, this.transition)
      prefix('transition', this.inner, this.transition)
    } else {
      prefix('transition', this.outer, '')
      prefix('transition', this.inner, '')
    }

    prefix('transform', this.outer, `matrix(1, 0, 0, ${transformValues.scale}, 0, 0)`)
    prefix('transform', this.inner, `scaleY( ${transformValues.innerScale} )`)

    this._html.style.setProperty('--ht-nav-y', (transformValues.translateY / 16) + 'rem')
    this._html.style.setProperty('--ht-nav-scale', transformValues.scale)

    /* Pass container and inner scales for element */

    this.getScales(transformValues.scale, transformValues.innerScale)

    this._currentScale = scale
  }

  /**
   * Event handlers
   */

  _resize () {
    this._viewportWidth = window.innerWidth
    const y = window.scrollY

    /* Reset current scale to */

    this._setScaleTo()

    /* Have to reset this because could be different on resize */

    this._ogOuterHeight = this.outer.clientHeight

    /* */

    if (y >= this._ogOuterHeight) {
      this._scaledDown = true
      this._setTransformValues(this._currentScaleTo)
    } else {
      this._scaledDown = false
    }
  }

  _scroll () {
    const y = window.scrollY

    const slowScrollY = y * (this._currentScaleTo / this.friction) // to slow down transformation not so abrupt
    const diff = Math.abs(this._ogOuterHeight - slowScrollY)
    let scale = diff / this._ogOuterHeight
    let transition = false

    if (this.maxOffset && y > this.maxOffset) {
      scale = this._currentScaleTo
      transition = true
    }

    if (!this.reduceMotion) {
      if (slowScrollY <= this._ogOuterHeight) {
        if (scale <= 1 && scale >= this._currentScaleTo) {
          if (scale < 1 && scale >= this._currentScaleTo) {
            this._scaledDown = true
          } else {
            this._scaledDown = false
          }

          this._setTransformValues(scale, transition)
        }
      } else {
        /* If has not scaled to this._currentScaleTo then transform to that value */

        if (this._currentScale > this._currentScaleTo) {
          this._scaledDown = true
          this._setTransformValues(this._currentScaleTo, transition)
        }
      }
    } else {
      if (y >= 0 && y > 10) {
        this._scaledDown = true
        this._setTransformValues(this._currentScaleTo, transition)
      } else {
        this._scaledDown = false
        this._setTransformValues(1, transition)
      }
    }
  }
}

/* Exports */

export { NavigationScaleBrowser }
