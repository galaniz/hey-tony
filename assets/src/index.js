/**
 * Theme js
 */

/* Imports */

import { setElements, usingMouse } from 'Formation/utils'
import Nav from './components/nav'
import LoadMore from 'Formation/objects/load/more'

/* Variables */

const el = {}
const elMeta = [
  {
    prop: 'nav',
    selector: '.c-nav'
  },
  {
    prop: 'hero',
    selector: '.fusion-page-title-bar'
  },
  {
    prop: 'loadMore',
    selector: '.js-load-more'
  }
]

/* DOM loaded */

const initialize = () => {
  /* Check if reduce motion */

  let reduceMotion = false
  const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)')

  if (!mediaQuery || mediaQuery.matches) { reduceMotion = true }

  /* Set elements object */

  setElements(elMeta, el)

  /* Check if using mouse */

  usingMouse()

  /* Fixed Header */

  if (el.nav) {
    const n = new Nav({
      button: el.nav.querySelector('.c-nav__button'),
      outer: el.nav,
      inner: el.nav.firstElementChild,
      scaleTo: [
        {
          width: 0,
          scale: 0.6
        },
        {
          width: 1000,
          scale: 0.4
        }
      ],
      makeFixed: true,
      friction: 1.15,
      maxOffset: 600,
      reduceMotion: reduceMotion
    })
  }

  /* Get height of hero */

  if (el.hero) {
    let resizeTimer

    const set = () => {
      document.documentElement.style.setProperty('--ht-hero-h', (el.hero.clientHeight / 16) + 'rem')
    }

    const resizeHandler = () => {
      clearTimeout(resizeTimer)

      resizeTimer = setTimeout(() => {
        set()
      }, 100)
    }

    set()

    window.addEventListener('resize', resizeHandler)
  }

  /* Load posts */

  if (el.loadMore) {
    const ns = 'ht'
    const n = window[ns]

    const next = el.loadMore
    const baseLink = next.getAttribute('data-base-link')
    const type = next.getAttribute('data-type')
    const page = parseInt(next.getAttribute('data-page'))
    const ppp = parseInt(next.getAttribute('data-per-page'))
    const total = parseInt(next.getAttribute('data-total'))
    const insertInfo = document.querySelector(next.getAttribute('data-insert-selector'))

    /* Vars outside of next */

    const prev = document.querySelector('.js-load-more-prev')
    const loader = document.querySelector('.js-load-more-loader')
    const current = document.querySelector('.js-load-more-current')
    const tot = document.querySelector('.js-load-more-total')
    const error = document.querySelector('.js-p-err')
    const noResults = Array.from(document.querySelectorAll('.js-p-no-res'))
    const filters = Array.from(document.querySelectorAll('.js-load-more-filter'))
    const pagination = prev && current
    const loaders = []

    /* Loaders */

    if (loader) {
      loaders.push(loader)
    }

    /* Data */

    const data = {
      action: type === 'comments' ? 'get_comments' : 'get_posts',
      type: type
    }

    if (Object.getOwnPropertyDescriptor(window, ns + '_load_posts_query')) {
      data.query_args = window[ns + '_load_posts_query']
    }

    if (Object.getOwnPropertyDescriptor(window, ns + '_load_posts_query_static')) {
      data.query_args_static = window[ns + '_load_posts_query_static']
    }

    /* Arguments */

    const args = {
      url: n.ajax_url,
      data: data,
      next: next,
      loaders: loaders,
      ppp: ppp,
      total: total,
      filters: filters,
      insertInto: insertInfo
    }

    if (pagination) {
      args.prev = prev
      args.current = current
      args.page = page
      args.tot = tot

      args.filterPostData = function (data) {
        const { page = false } = data

        if (page) {
          const d = Object.assign({}, data)

          d.paged = page

          return d
        }

        return data
      }

      args.filterPushUrlParams = function (state, data) {
        const {
          query_args = false,
          filters = false,
          page = 0
        } = data

        const queryArgs = query_args
        const params = {}
        const delStr = 'load_more_delete_param'

        if (queryArgs && filters) {
          for (const q in queryArgs) {
            const qa = queryArgs[q]
            let actualV = 'null'
            let vNull = false

            if (Object.getOwnPropertyDescriptor(filters, q)) {
              actualV = filters[q].value
            }

            if (actualV === 'null') {
              vNull = true
            }

            for (const a in qa) {
              let v = qa[a]

              if (v === '%value') { v = vNull ? delStr : actualV }

              if (v === '%value:int') { v = vNull ? delStr : parseInt(actualV) }

              if (Array.isArray(v)) {
                v.forEach(vv => {
                  for (const aa in vv) {
                    let vvv = vNull ? delStr : vv[aa]

                    if (vvv === '%value') { vvv = actualV }

                    if (vvv === '%value:int') { vvv = parseInt(actualV) }

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

      args.filterControlUrls = function (args) {
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

          if (prevParamsArr.length) {
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

          if (nextParamsArr.length) {
            next += `?${nextParamsArr.join('&')}`
          }
        }

        return {
          prev: prev,
          next: next
        }
      }
    }

    if (noResults) {
      const containers = []
      const buttons = []

      noResults.forEach(n => {
        const b = n.querySelector('.js-p-no-res__btn')

        if (b) { buttons.push(b) }

        containers.push(n)
      })

      args.noResults = {
        containers: containers,
        buttons: buttons
      }
    }

    if (error) {
      args.error = error
    }

    const lm = new LoadMore(args)
  }
} // end initialize

document.addEventListener('DOMContentLoaded', initialize)
