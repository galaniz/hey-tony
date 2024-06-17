// @ts-nocheck

/**
 * Components - Filters Browser
 */

/* Imports */

import { isStringStrict, isNumber, setItems } from '@alanizcreative/formation/lib/utils/utils'
import { LoadMore } from '@alanizcreative/formation/lib/objects/LoadMore/LoadMore'
import { configBrowserVars } from '../../Config/Config'

/**
 * Store base link for filters
 *
 * @type {string}
 */
let baseLink: string = ''

/**
 * Function - filter data object
 *
 * @param {object} data
 * @return {object}
 */
const filterPostsData = function (data: object): object {
  const { page = false } = data

  if (page) {
    const d = Object.assign({}, data)

    d.paged = page

    return d
  }

  return data
}

/**
 * Function - filter push data
 *
 * @param {boolean} state
 * @param {object} data
 * @return {object}
 */
const filterPushUrlParams = function (state: boolean, data: object): object {
  const {
    query_args = false,
    filters = false,
    page = 0
  } = data

  const queryArgs = query_args // eslint-disable-line camelcase
  const params = {}
  const delStr = 'load_more_delete_param'

  if (queryArgs && filters) {
    for (const q in queryArgs) {
      const qa = queryArgs[q]
      let actualV = 'null'
      let vNull = false

      if (filters[q] !== undefined) {
        actualV = filters[q].value
      }

      if (actualV === 'null') {
        vNull = true
      }

      for (const a in qa) {
        let v = qa[a]

        if (v === '%value') {
          v = vNull ? delStr : actualV
        }

        if (v === '%value:int') {
          v = vNull ? delStr : parseInt(actualV)
        }

        if (Array.isArray(v)) {
          v.forEach(vv => {
            for (const aa in vv) {
              let vvv = vNull ? delStr : vv[aa]

              if (vvv === '%value') {
                vvv = actualV
              }

              if (vvv === '%value:int') {
                vvv = parseInt(actualV)
              }

              params[aa] = vvv
            }
          })
        } else {
          params[a] = v
        }
      }
    }
  }

  params.pg = page > 1 ? page : delStr

  return params
}

/**
 * Function - filter next and prev links
 *
 * @param {object} args
 * @return {object}
 */
const filterControlUrls = function (args: object): object {
  const { urlParams, total } = args
  const page = this._data.page

  const prevParams = Object.assign({}, urlParams)
  const nextParams = Object.assign({}, urlParams)

  let prev = ''
  let next = ''

  if (page > 1) {
    prevParams.pg = (page - 1 === 1) ? 0 : page - 1

    prev = baseLink

    const prevParamsArr = []

    for (const p in prevParams) {
      const pp = prevParams[p]

      if (pp && pp !== 'load_more_delete_param') {
        prevParamsArr.push(`${p}=${pp}`)
      }
    }

    if (prevParamsArr.length > 0) {
      prev += `?${prevParamsArr.join('&')}`
    }
  }

  if (page < total) {
    nextParams.pg = page + 1

    next = baseLink

    const nextParamsArr = []

    for (const n in nextParams) {
      const nn = nextParams[n]

      if (nn && nn !== 'load_more_delete_param') {
        nextParamsArr.push(`${n}=${nn}`)
      }
    }

    if (nextParamsArr.length > 0) {
      next += `?${nextParamsArr.join('&')}`
    }
  }

  return {
    prev,
    next
  }
}

/**
 * Function - initialize filters browser
 *
 * @return {*}
 */
const FiltersBrowser = (): unknown => {
  /* Get DOM elements */

  const l = setItems({
    next: '.js-load-more',
    prev: '.js-load-more-prev',
    insert: '.js-insert',
    loader: '.js-load-more-loader',
    current: '.js-load-more-current',
    tot: '.js-load-more-total',
    error: '.js-load-more-err',
    noResultContainers: [
      '.js-load-more-no-res'
    ],
    noResultButtons: [
      '.js-load-more-no-res__btn'
    ],
    filters: [
      '.js-load-more-filter'
    ],
    filtersForm: '.js-load-more-filter-form'
  })

  /* Args */

  const {
    next,
    prev,
    insert,
    loader,
    current,
    tot,
    error,
    noResultContainers,
    noResultButtons,
    filters,
    filtersForm
  } = l

  /* Namespace */

  const { namespace, ajaxUrl } = configBrowserVars

  /* Attributes */

  let type = ''
  let page = 0
  let ppp = 0
  let total = 0

  if (next !== null) {
    const baseLinkAttr = next.getAttribute('data-base-link')
    const typeAttr = next.getAttribute('data-type')
    const pageAttr = next.getAttribute('data-page')
    const pppAttr = next.getAttribute('data-per-page')
    const totalAttr = next.getAttribute('data-total')

    baseLink = isStringStrict(baseLinkAttr) ? baseLinkAttr : ''
    type = isStringStrict(typeAttr) ? typeAttr : ''

    if (isStringStrict(pageAttr)) {
      const pageAttrNum = parseInt(pageAttr, 10)

      page = isNumber(pageAttrNum) ? pageAttrNum : 0
    }

    if (isStringStrict(pppAttr)) {
      const pppAttrNum = parseInt(pppAttr, 10)

      ppp = isNumber(pppAttrNum) ? pppAttrNum : 0
    }

    if (isStringStrict(totalAttr)) {
      const totalAttrNum = parseInt(totalAttr, 10)

      total = isNumber(totalAttrNum) ? totalAttrNum : 0
    }
  }

  /* Pagination */

  const pagination = prev !== null && current !== null

  /* Data */

  const data = {
    action: 'get_posts',
    type
  }

  const postsQuery = window[`${namespace}_load_posts_query`]
  const postsStaticQuery = window[`${namespace}_load_posts_query_static`]

  if (postsQuery !== undefined) {
    data.query_args = postsQuery
  }

  if (postsStaticQuery !== undefined) {
    data.query_args_static = postsStaticQuery
  }

  /* Args */

  const args = {
    url: ajaxUrl,
    data,
    next,
    prev,
    current,
    tot,
    loader,
    ppp,
    page,
    total,
    filters,
    filtersForm,
    insertInto: insert,
    error,
    noResults: {
      containers: noResultContainers,
      buttons: noResultButtons
    },
    filterPostData: pagination ? filterPostsData : undefined,
    filterPushUrlParams: pagination ? filterPushUrlParams : undefined,
    filterControlUrls: pagination ? filterControlUrls : undefined
  }

  /* Instantiate */

  return new LoadMore(args)
}

FiltersBrowser()
