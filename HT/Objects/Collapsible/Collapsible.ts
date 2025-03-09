/**
 * Objects - Collapsible Browser
 */

/* Imports */

import type { CollapsibleArgs } from '@alanizcreative/formation/lib/objects/Collapsible/CollapsibleTypes'
import { addAction, setItems, onResize, isStringStrict } from '@alanizcreative/formation/lib/utils/utils'
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
      let isNav = false

      if (context !== null) {
        isToc = context.hasAttribute('data-toc')
        isNav = context.hasAttribute('data-nav')

        const triggerId = context.getAttribute('data-trigger')

        if (trigger === null && isStringStrict(triggerId)) {
          args.trigger = document.getElementById(triggerId)
        }
      }

      if (isNav) {
        args.doAccordion = 'nav-collapsible-accordion'
        args.doHover = 'nav-collapsible-hover'
      }

      const instance = new Collapsible(args)

      if (isNav) {
        addAction('nav-close', () => {
          instance.toggle(false)
        })
      }

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

/* Exports */

export { CollapsibleBrowser }
