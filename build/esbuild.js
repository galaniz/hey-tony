/**
 * Build - Esbuild
 */

/* Imports */

import postcss from 'postcss'
import autoprefixer from 'autoprefixer'
import esbuild from 'esbuild'
import { sassPlugin } from 'esbuild-sass-plugin'

/* Entry points */

const entryPoints = {
  style: 'HT/Global/Global.scss'
  // 'assets/js/ht': 'assets/src/index.js',
}

/* Args */

const args = {
  entryPoints,
  logLevel: 'info',
  outdir: './',
  minify: true,
  bundle: true,
  sourcemap: false,
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
