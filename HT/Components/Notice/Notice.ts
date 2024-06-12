/**
 * Components - Notice Browser
 */

/* Imports */

import { isHTMLElement, setItems } from '@alanizcreative/formation/lib/utils/utils'
import { Remove } from '@alanizcreative/formation/lib/objects/Remove/Remove'
import { configBrowserVars } from '../../Config/Config'

/**
 * Function - initialize notice browser (display based on cookie)
 *
 * @return {void}
 */
const NoticeBrowser = (): void => {
  /* Get DOM elements */

  const n = setItems({
    context: '.c-notice',
    button: 'button'
  })

  /* Namespace */

  const { namespace } = configBrowserVars

  /* Args */

  const { context, button } = n

  /* Instantiate */

  const notice = new Remove({
    item: context,
    trigger: button,
    cookie: {
      name: `${namespace}_cookie_notice`,
      value: 'close',
      expirationDays: 365
    }
  })

  const hide = notice.getHide()

  if (hide === false && isHTMLElement(context)) {
    context.style.display = 'block'
  }
}

NoticeBrowser()
