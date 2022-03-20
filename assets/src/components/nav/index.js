/**
 * Components: nav
 *
 * @param args [object] {
 *  @param outer [HTMLElement]
 *  @param inner [HTMLElement]
 *  @param scaleTo [array]
 *  @param maxOffset [boolean]
 *  @param makeFixed [boolean]
 *  @param itemToOffsetFrom [HTMLElement]
 *  @param friction [float]
 *  @param buffer [HTMLElement]
 *  @param transition [string]
 *  @param reduceMotion [boolean]
 *  @param getScales [function]
 *  @param button [HTMLElement]
 *  @param overflowHiddenClass [string]
 * }
 */

/* Imports */

import {
  mergeObjects,
  toggleFocusability,
  getScrollY,
  prefix
} from 'Formation/utils'

/* Class */

class Nav {
  /**
   * Constructor
   */

  constructor (args) {
    /**
     * Public variables
     */

    this.outer = null
    this.inner = null
    this.scaleTo = []
    this.maxOffset = false
    this.makeFixed = false
    this.itemToOffsetFrom = null
    this.friction = 1 // slow down scaling ( eg. 1.5 makes it slower than 1 )
    this.buffer = null // make same height as element to prevent collapsing content
    this.transition = 'transform 400ms ease'
    this.reduceMotion = false
    this.getScales = () => {}
    this.button = null
    this.overflowHiddenClass = 'u-o-h'

    mergeObjects(this, args)

    /**
     * Internal variables
     */

    /* Keep track of transform scale */

    this._currentScale = 1

    /* Keep track of scale to by breakpoint */

    this._currentScaleTo = 1

    /* Keep track if scaled down or not */

    this._scaledDown = false

    /* Offset for making fixed */

    this._offsetY = 0

    /* For throttling resize event */

    this._resizeTimer = null

    /* For getting correct scale to */

    this._viewportWidth = window.innerWidth

    /* Track open state */

    this._open = false

    /* For toggling outer */

    this._focusableItems = []

    /* For prevent scroll on open */

    this._html = document.documentElement

    /**
     * Initialize
     */

    const init = this._initialize()

    if (!init) { return false }
  }

  /**
   * Initialize
   */

  _initialize () {
    /* Check that required variables not null */

    if (!this.button || !this.outer || !this.inner) { return false }

    /* Set low and high for scale to breakpoints */

    const scaleToLength = this.scaleTo.length

    this.scaleTo.forEach((s, i) => {
      const low = s.width
      let high = 99999

      if (scaleToLength > 1 && i < scaleToLength - 1) { high = this.scaleTo[i + 1].width }

      s.low = low
      s.high = high
    })

    this._setScaleTo()

    /* Set offset for making fixed */

    this._setOffset()

    /* Store height of outer element before scaling */

    this._outerHeight = this.outer.clientHeight
    this._ogOuterHeight = this._outerHeight

    this._setCssHeightVar()
    this._setBufferHeight()

    /* Scroll position on load */

    if (getScrollY() >= this._ogOuterHeight + this._offsetY) {
      this._scaledDown = true
      this._setTransformValues(this._currentScaleTo)
    }

    /* Get focusable elements */

    const focusSelector = 'a, area, input, select, textarea, button, [tabindex], [data-context-inert-tabindex], iframe'
    const outerFocusableItems = Array.from(this.outer.querySelectorAll(focusSelector))

    if (outerFocusableItems.length) {
      this._focusableItems = Array.from(document.querySelectorAll(focusSelector))

      this._focusableItems = this._focusableItems.filter(item => {
        if (outerFocusableItems.indexOf(item) === -1) {
          return true
        }

        return false
      })
    }

    /* Scroll event */

    this._scrollHandler = this._scroll.bind(this)
    window.addEventListener('scroll', this._scrollHandler)

    /* Toggle menu */

    this._clickHandler = this._click.bind(this)
    this.button.addEventListener('click', this._clickHandler)

    /* Resize event */

    this._resizeHandler = this._resize.bind(this)
    window.addEventListener('resize', this._resizeHandler)

    /* Keydown event */

    this._keydownHandler = this._keydown.bind(this)
    document.addEventListener('keydown', this._keydownHandler)

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
      scale: scale,
      innerScale: innerScale,
      translateY: translateY
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
      this.outer.setAttribute('data-scale', 'down')
    } else {
      this.outer.setAttribute('data-scale', 'default')
    }

    const transformValues = this._getTransformValues(scale)

    if (transformValues.scale === this._currentScaleTo) {
      this.outer.setAttribute('data-scale-to', true)
    } else {
      this.outer.removeAttribute('data-scale-to')
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

    this._html.style.setProperty('--ht-nav-t-y', (transformValues.translateY / 16) + 'rem')
    this._html.style.setProperty('--ht-nav-s', transformValues.scale)

    /* Pass container and inner scales for element */

    this.getScales(transformValues.scale, transformValues.innerScale)

    this._currentScale = scale
  }

  _setOffset () {
    if (!this.makeFixed || !this.itemToOffsetFrom) { return }

    this._offsetY = this.itemToOffsetFrom.offsetTop + this.itemToOffsetFrom.clientHeight
  }

  _setFixed (y) {
    if (!this.makeFixed || !this.itemToOffsetFrom) { return }

    if (y >= this._offsetY) {
      this.outer.setAttribute('data-fixed', '')
    } else {
      this.outer.removeAttribute('data-fixed')
    }
  }

  _setBufferHeight () {
    if (!this.buffer) { return }

    this.buffer.style.height = `${this._ogOuterHeight}px`
  }

  _setCssHeightVar () {
    this._html.style.setProperty('--ht-nav-h', (this._ogOuterHeight / 16) + 'rem')
  }

  /**
   * Prevent scroll when open mobile navigation
   */

  _disableScroll (disable = true) {
    if (disable) {
      this._html.classList.add(this.overflowHiddenClass)
    } else {
      this._html.classList.remove(this.overflowHiddenClass)
    }
  }

  /**
   * Open/close outer nav
   */

  _toggle (open) {
    toggleFocusability(!open, this._focusableItems)

    if (open) {
      this.outer.setAttribute('data-open', '')
      this.button.setAttribute('data-show', '')
    } else {
      this.outer.removeAttribute('data-open', '')
      this.button.removeAttribute('data-show', '')
    }

    this.button.setAttribute('aria-expanded', open)
    this._disableScroll(open)

    this._open = open
  }

  /**
   * Event handlers
   */

  _resize () {
    clearTimeout(this._resizeTimer)

    this._resizeTimer = setTimeout(() => {
      this._viewportWidth = window.innerWidth

      /* Reset current scale to */

      this._setScaleTo()

      /* Reset offset */

      this._setOffset()
      this._setFixed(getScrollY())

      /* Have to reset this because could be different on resize */

      this._ogOuterHeight = this.outer.clientHeight
      this._setCssHeightVar()

      /* Reset buffer */

      this._setBufferHeight()

      if (getScrollY() >= this._ogOuterHeight + this._offsetY) {
        this._scaledDown = true
        this._setTransformValues(this._currentScaleTo)
      } else {
        this._scaledDown = false
      }
    }, 100)
  }

  _scroll () {
    const y = getScrollY()

    this._setFixed(y)

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
      if (y >= this._offsetY) {
        this._scaledDown = true
        this._setTransformValues(this._currentScaleTo, transition)
      } else {
        this._scaledDown = false
        this._setTransformValues(1, transition)
      }
    }
  }

  _click () {
    this._toggle(!this._open)
  }

  _keydown (e) {
    const key = e.key || e.keyCode || e.which || e.code

    if ([27, 'Escape'].indexOf(key) !== -1 && this._open) {
      this._toggle(false)
    }
  }
} // End Nav

/* Exports */

export default Nav
