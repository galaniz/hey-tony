/**
 * Objects - Overflow Browser
 */

/* Imports */

import { setItems } from '@alanizcreative/formation/lib/utils/utils'
import { OverflowIndicator } from '@alanizcreative/formation/lib/objects/OverflowIndicator/OverflowIndicator'

/**
 * Function - initialize overflow browser
 *
 * @return {void}
 */
const OverflowBrowser = (): void => {
  /* Get DOM elements */

  const o = setItems([
    '.o-overflow'
  ])

  /* Instantiate */

  if (o.length > 0) {
    o.forEach(overflow => {
      return new OverflowIndicator({
        indicator: overflow !== null ? overflow.parentElement : null,
        scroll: overflow,
        y: false
      })
    })
  }
}

OverflowBrowser()
