/**
 * Global - Browser
 */

/* Imports */

import { onResize } from '@alanizcreative/formation/lib/utils/utils'
import { setConfigBrowser } from '../Config/Config'
import { NavigationBrowser } from '../Components/Navigation/Navigation'
import { CollapsibleBrowser } from '../Objects/Collapsible/Collapsible'

/* Config */

setConfigBrowser()

/**
 * Html element
 *
 * @type {HTMLElement}
 */
const html: HTMLElement = document.documentElement

/**
 * Function - scrollbar width from window and html element widths
 *
 * @return {void}
 */
const setScrollbarWidth = (): void => {
  const w = window.innerWidth - html.clientWidth

  html.style.setProperty('--ht-scrollbar-width', `${w}px`)
}

setScrollbarWidth()

onResize(() => {
  setScrollbarWidth()
}, 800)

/* Collapsible */

CollapsibleBrowser()

/* Navigation */

NavigationBrowser()
