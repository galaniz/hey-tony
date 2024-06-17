/**
 * Build - Copy
 */

/* Imports */

import { cp } from 'node:fs/promises'

/**
 * Function - copy svg folder to public assets
 *
 * @return {void}
 */
const init = async (to = '', from = '') => {
  try {
    await cp(to, from, { recursive: true })
  } catch (error) {
    console.error('[HT] Error copying svg ', error)
  }
}

init()
