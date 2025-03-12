/**
 * Build - Esbuild
 */

/* Imports */

import postcss from 'postcss'
import autoprefixer from 'autoprefixer'
import esbuild from 'esbuild'
import { sassPlugin } from 'esbuild-sass-plugin'

/* Entry points */

const styleScript = {
  style: true,
  script: true
}

const scriptOnly = {
  script: true
}

const styleOnly = {
  style: true
}

const assets = {
  'Global/Global': styleScript,
  'Global/GlobalNoJs': styleOnly,
  'Components/Hero/Hero': scriptOnly,
  'Components/Filters/Filters': styleScript,
  'Components/Loader/Loader': styleOnly,
  'Components/Notice/Notice': styleScript,
  'Components/TableOfContents/TableOfContents': styleScript,
  'Objects/Overflow/Overflow': styleScript,
  'Objects/Slider/Slider': styleScript,
  'Objects/Tabs/Tabs': styleScript
}

const entryPoints = {}

for (const a in assets) {
  const {
    style = false,
    script = false
  } = assets[a]

  if (style) {
    entryPoints[`css/${a}`] = `HT/${a}.scss`
  }

  if (script) {
    entryPoints[`js/${a}`] = `HT/${a}.ts`
  }
}

/* Args */

const args = {
  entryPoints,
  logLevel: 'info',
  outdir: './assets',
  minify: true,
  bundle: true,
  // sourcemap: true,
  splitting: true,
  format: 'esm',
  target: 'es6',
  external: ['*.woff', '*.woff2', '*.svg'],
  plugins: [
    sassPlugin({
      async transform (source) {
        const { css } = await postcss(
          [
            autoprefixer
          ]
        ).process(
          source
        )

        return css
      }
    })
  ]
}

/* Watch */

let watch = false

process.argv.forEach((arg) => {
  watch = arg === '--watch'
})

if (watch) {
  const context = await esbuild.context(args)
  await context.watch()
}

/* Build */

if (!watch) {
  await esbuild.build(args)
}
