/**
 * Theme js
 */

/* Imports */

import { setElements, usingMouse } from 'Formation/utils'
import Nav from './components/nav'

/* Variables */

const el = {}
const elMeta = [
  {
    prop: 'nav',
    selector: '.c-nav'
  },
  {
    prop: 'hero',
    selector: '.fusion-page-title-bar'
  }
]

/* DOM loaded */

const initialize = () => {
  /* Check if reduce motion */

  let reduceMotion = false
  const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)')

  if (!mediaQuery || mediaQuery.matches) { reduceMotion = true }

  /* Set elements object */

  setElements(elMeta, el)

  /* Check if using mouse */

  usingMouse()

  /* Fixed Header */

  if (el.nav) {
    const nav = new Nav({
      button: el.nav.querySelector('.c-nav__button'),
      outer: el.nav,
      inner: el.nav.firstElementChild,
      scaleTo: [
        {
          width: 0,
          scale: 0.6
        },
        {
          width: 1000,
          scale: 0.4
        }
      ],
      makeFixed: true,
      friction: 1.15,
      maxOffset: 600,
      reduceMotion: reduceMotion
    })
  }

  /* Get height of hero */

  if (el.hero) {
    let resizeTimer

    const set = () => {
      document.documentElement.style.setProperty('--ht-hero-h', (el.hero.clientHeight / 16) + 'rem')
    }

    const resizeHandler = () => {
      clearTimeout(resizeTimer)

      resizeTimer = setTimeout(() => {
        set()
      }, 100)
    }

    set()

    window.addEventListener('resize', resizeHandler)
  }
} // end initialize

document.addEventListener('DOMContentLoaded', initialize)
