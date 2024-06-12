/**
 * Objects - Slider Browser
 */

/* Imports */

import { setItems } from '@alanizcreative/formation/lib/utils/utils'
import { Slider } from '@alanizcreative/formation/lib/objects/Slider/Slider'

/**
 * Function - initialize slider browser
 *
 * @return {void}
 */
const SliderBrowser = (): void => {
  /* Get DOM elements */

  const s = setItems([
    {
      context: '.js-slider',
      main: '.o-slider',
      track: '.o-slider__track',
      height: '.o-slider__h',
      panels: [
        '[role="tabpanel"]'
      ],
      items: [
        '.o-slider__inner'
      ],
      nav: [
        '[role="tab"]'
      ]
    }
  ])

  /* Instantiate */

  if (s.length > 0) {
    s.forEach(slider => {
      const {
        main,
        track,
        height,
        panels,
        items,
        nav
      } = slider

      const loop = main !== null ? main.hasAttribute('data-loop') : false
      const breakpoints = [
        {
          breakpoint: 0,
          items: 1
        },
        {
          breakpoint: 600,
          items: 2
        },
        {
          breakpoint: 900,
          items: 3
        }
      ]

      return new Slider({
        container: main,
        tabs: nav,
        panels,
        duration: 500,
        slider: main,
        track,
        targetHeight: height,
        loop,
        groupItems: loop ? [] : items,
        breakpoints: loop ? [] : breakpoints
      })
    })
  }
}

SliderBrowser()
