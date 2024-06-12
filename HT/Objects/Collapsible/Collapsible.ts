/**
 * Objects - Collapsible Browser
 */

/* Imports */

import type { CollapsibleArgs } from '@alanizcreative/formation/lib/objects/Collapsible/CollapsibleTypes'
import { setItems, onResize, isStringStrict } from '@alanizcreative/formation/lib/utils/utils'
import { Collapsible } from '@alanizcreative/formation/lib/objects/Collapsible/Collapsible'
import { configBrowser } from '../../Config/Config'

/**
 * Function - initialize collapsible browser
 *
 * @return {void}
 */
const CollapsibleBrowser = (): void => {
  /* Get DOM elements */

  const collapsibles = setItems([
    {
      context: '.o-collapsible',
      main: '.o-collapsible__main',
      trigger: '.o-collapsible__toggle'
    }
  ])

  /* Breakpoint */

  const { fontSizeMultiplier } = configBrowser
  const tocBreakpoint = 1025 * fontSizeMultiplier

  /* Instantiate */

  if (collapsibles.length > 0) {
    collapsibles.forEach(c => {
      const { context, main, trigger } = c

      const args: CollapsibleArgs = {
        container: context,
        collapsible: main,
        trigger
      }

      let isToc = false

      if (context !== null) {
        isToc = context.hasAttribute('data-toc')

        const triggerId = context.getAttribute('data-trigger')

        if (trigger === null && isStringStrict(triggerId)) {
          args.trigger = document.getElementById(triggerId)
        }
      }

      const instance = new Collapsible(args)

      if (isToc) {
        if (window.innerWidth >= tocBreakpoint) {
          instance.toggle(true)
        }

        onResize(() => {
          if (window.innerWidth >= tocBreakpoint) {
            instance.toggle(true)
          }
        })
      }
    })
  }
}

CollapsibleBrowser()
