/**
 * Components - Table of Contents Browser
 */

/* Imports */

import type { TableOfContentsHashArgs } from './TableOfContentsTypes'
import { setItems } from '@alanizcreative/formation/lib/utils/utils'
import { VisibleHash } from '@alanizcreative/formation/lib/effects/Visible/VisibleHash'
import { configBrowser } from '../../Config/Config'

/**
 * Function - initialize table of contents browser
 *
 * @return {void}
 */
const TableOfContentsBrowser = (): void => {
  /* Get DOM elements */

  const a = setItems({
    hashes: [
      '.o-toc__link'
    ],
    end: '.ht-related'
  })

  /* Offset */

  const { fontSizeMultiplier } = configBrowser
  const offset = 112 * fontSizeMultiplier

  /* Hash links */

  const { hashes, end } = a

  if (end !== null && hashes.length > 0) {
    const hashArgs: TableOfContentsHashArgs[] = []

    /* Get visible item from hash */

    hashes.forEach((h) => {
      const id = h instanceof HTMLAnchorElement ? h.hash.replace('#', '') : ''

      if (id === '') {
        return
      }

      const item = document.getElementById(id)

      if (item === null) {
        return
      }

      hashArgs.push({
        link: h,
        item: [item],
        offset
      })
    })

    /* Check for next visible item and instantiate */

    hashArgs.forEach((args, i) => {
      const next = hashArgs[i + 1]

      if (next !== undefined) {
        args.item.push(next.item[0])
      } else {
        args.item.push(end)
      }

      return new VisibleHash(args)
    })
  }
}

TableOfContentsBrowser()
