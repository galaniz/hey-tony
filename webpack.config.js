/**
 * Webpack config
 */

/* Imports */

const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const CopyWebpackPlugin = require('copy-webpack-plugin')
const webpack = require('webpack')
const path = require('path')

/* Namespace */

const n = 'ht'

/* Output path */

const outputPath = path.resolve(__dirname, 'assets', 'public')

/* Asset paths */

const assetsPath = path.resolve(__dirname, 'assets', 'src')
const formationPath = '@alanizcreative/formation/src'

/* Resolve to root */

const resolve = {
  alias: {
    Formation: formationPath
  },
  extensions: [
    '.sass',
    '.scss',
    '.css',
    '.js',
    '.json',
    '.jsx'
  ]
}

/* Rules */

const rules = [
  {
    test: /\.(css|sass|scss)$/,
    use: [
      {
        loader: MiniCssExtractPlugin.loader
      },
      {
        loader: 'css-loader',
        options: {
          url: false,
          importLoaders: 1
        }
      },
      {
        loader: 'postcss-loader',
        options: {
          postcssOptions: {
            plugins: {
              'postcss-preset-env': {
                browsers: [
                  'last 3 versions',
                  'edge >= 16'
                ],
                stage: 4
              },
              cssnano: {},
              'postcss-combine-duplicated-selectors': {}
            }
          }
        }
      },
      {
        loader: 'sass-loader',
        options: {
          implementation: require('sass'),
          sassOptions: {
            includePaths: [
              assetsPath,
              'node_modules'
            ]
          }
        }
      }
    ]
  }
]

const rulesCompat = [
  {
    test: /\.js$/,
    loader: 'babel-loader',
    options: {
      presets: [
        [
          '@babel/preset-env',
          {
            targets: { chrome: '60', edge: '16' }
          }
        ]
      ]
    }
  },
  {
    test: /\.(css|sass|scss)$/,
    use: [
      {
        loader: MiniCssExtractPlugin.loader
      },
      {
        loader: 'css-loader',
        options: {
          url: false,
          importLoaders: 1
        }
      },
      {
        loader: 'postcss-loader',
        options: {
          postcssOptions: {
            plugins: {
              'postcss-preset-env': {
                browsers: [
                  'last 3 versions',
                  'edge >= 16'
                ],
                stage: 4
              },
              cssnano: {},
              'postcss-combine-duplicated-selectors': {}
            }
          }
        }
      },
      {
        loader: 'sass-loader',
        options: {
          implementation: require('sass'),
          sassOptions: {
            includePaths: [
              assetsPath,
              'node_modules'
            ]
          }
        }
      }
    ]
  }
]

/* Output environment */

const outputCompatEnv = {
  arrowFunction: false,
  bigIntLiteral: false,
  const: false,
  destructuring: false,
  dynamicImport: false,
  forOf: false,
  module: false
}

/* Copy patterns */

const copyPatterns = [
  {
    from: '*.svg',
    context: path.resolve(__dirname, 'assets', 'src', 'svg'),
    to ({ context, absoluteFilename }) {
      return path.resolve(__dirname, 'assets/public/svg/[name][ext]')
    }
  }
]

/* Entries */

let entries = []

entries.push({
  name: n,
  paths: [
    './assets/src/index.scss',
    './assets/src/index-compat.js'
  ]
})

entries.push({
  name: n,
  paths: [
    './assets/src/index.js'
  ]
})

entries = entries.map(e => {
  const obj = {}

  obj[e.name] = e.paths

  return obj
})

/* Style comment */

const styleBanner =
`/*!
Theme Name: Avada Child
Text Domain: avada-child
Version: 1.0.0
Tested up to: 6.0
Requires at least: 5.0
Requires PHP: 7.2.0
Description: Theme for heyTony based on Avada
Author: Graciela Alaniz
Author URI: https://alanizcreative.com/
Theme URI: https://heytony.ca/
License: MIT License
License URI: https://opensource.org/licenses/MIT
Template: Avada
*/`

/* Exports */

module.exports = [
  {
    mode: 'production',
    entry: entries[0],
    output: {
      path: outputPath,
      publicPath: '/',
      filename: 'js/[name]-compat.js',
      environment: outputCompatEnv,
      chunkFormat: 'array-push'
    },
    module: {
      rules: rulesCompat
    },
    resolve: resolve,
    target: ['web', 'es5'],
    plugins: [
      new MiniCssExtractPlugin({
        filename: '../../style.css'
      }),
      new webpack.BannerPlugin({
        raw: true,
        banner: styleBanner
      }),
      new CopyWebpackPlugin({
        patterns: copyPatterns
      })
    ]
  },
  {
    mode: 'production',
    entry: entries[1],
    output: {
      path: outputPath,
      publicPath: '/',
      filename: 'js/[name].js',
      chunkFormat: 'array-push'
    },
    module: {
      rules: rules
    },
    resolve: resolve
  }
]
