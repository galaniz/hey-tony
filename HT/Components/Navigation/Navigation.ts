/**
 * Components - Navigation Browser
 */

/* Imports */

import { setItems } from '@alanizcreative/formation/lib/utils/utils'
import { Nav } from '@alanizcreative/formation/lib/components/Nav/Nav'
import { NavigationScaleBrowser } from './NavigationScale'

/**
 * Function - initialize navigation browser
 *
 * @return {void}
 */
const NavigationBrowser = (): void => {
  /* Item selector */

  const itemSelector = '.c-nav__item[data-depth="0"]'

  /* Get DOM elements */

  const n = setItems({
    context: '.c-nav',
    list: '.c-nav__list',
    overflow: '.c-nav__overflow',
    overflowList: '.c-nav__column',
    items: [
      itemSelector
    ],
    links: [
      '.c-nav__link'
    ],
    open: '.c-nav__open',
    close: '.c-nav__close',
    overlay: '.c-nav__overlay'
  })

  /* Args */

  const {
    context,
    list,
    overflow,
    overflowList,
    items,
    links,
    open,
    close,
    overlay
  } = n

  const args = {
    nav: context,
    list,
    overflow,
    overflowList,
    items,
    itemSelector,
    links,
    open,
    close,
    overlay
  }

  /* Instantiate */

  const nav = (): unknown => {
    return new Nav(args)
  }

  const navScale = (): unknown => {
    return new NavigationScaleBrowser({
      outer: context,
      inner: context !== null ? context.firstElementChild : null,
      scaleTo: [
        {
          width: 0,
          scale: 0.75
        },
        {
          width: 900,
          scale: 0.45
        }
      ],
      friction: 1.15,
      maxOffset: 600
    })
  }

  nav()
  navScale()
}

/* Exports */

export { NavigationBrowser }
