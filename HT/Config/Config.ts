/**
 * Config - Browser
 */

/* Imports */

import type { ConfigVars } from './ConfigTypes'
import { config, setConfig } from '@alanizcreative/formation/lib/config/config'
import { isStringStrict } from '@alanizcreative/formation/lib/utils/utils'

/* Workaround for global window variables */

interface Data {
  ht?: {
    ajax_url: string
  }
  ht_load_posts_query?: unknown
  ht_load_posts_query_static?: unknown
}

declare const window: Data & Window

/**
 * Store custom variables
 *
 * @type {import('./configTypes').ConfigVars}
 */
const configBrowserVars: ConfigVars = {
  namespace: '',
  ajaxUrl: ''
}

/**
 * Function - initialize default functionality and props
 *
 * @return {void}
 */
const setConfigBrowser = (): void => {
  setConfig()

  const namespace = 'ht'
  const data = window[namespace]

  configBrowserVars.namespace = namespace
  configBrowserVars.ajaxUrl = data !== undefined && isStringStrict(data.ajax_url) ? data.ajax_url : ''
}

/* Exports */

export {
  config as configBrowser,
  configBrowserVars,
  setConfigBrowser
}
