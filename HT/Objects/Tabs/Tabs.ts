/**
 * Objects - Tabs Browser
 */

/* Imports */

import { setItems } from '@alanizcreative/formation/lib/utils/utils'
import { Tabs } from '@alanizcreative/formation/lib/objects/Tabs/Tabs'

/**
 * Function - initialize tabs browser
 *
 * @return {void}
 */
const TabsBrowser = (): void => {
  /* Get DOM elements */

  const t = setItems([
    {
      context: '.o-tabs',
      nav: [
        '[role="tab"]'
      ],
      panels: [
        '[role="tabpanel"]'
      ]
    }
  ])

  /* Instantiate */

  if (t.length > 0) {
    t.forEach(tab => {
      const { nav, panels } = tab

      return new Tabs({
        tabs: nav,
        panels,
        delay: 400
      })
    })
  }
}

TabsBrowser()
