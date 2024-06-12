/**
 * Components - Hero Browser
 */

/* Imports */

import { setItems, onResize } from '@alanizcreative/formation/lib/utils/utils'

/**
 * Function - set CSS variable height
 *
 * @param {Element|null} hero
 * @return {void}
 */
const setHeroHeight = (hero: Element | null): void => {
  if (hero === null) {
    return
  }

  document.documentElement.style.setProperty('--ht-hero-height', `${hero.clientHeight / 16}rem`)
}

/**
 * Function - initialize hero browser
 *
 * @return {void}
 */
const HeroBrowser = (): void => {
  /* Get DOM elements */

  const h = setItems({
    context: '.fusion-page-title-bar'
  })

  /* Args */

  const { context: hero } = h

  /* Instantiate */

  onResize(() => {
    setHeroHeight(hero)
  })

  setHeroHeight(hero)
}

HeroBrowser()
