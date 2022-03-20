/**
 * Webpack config
 */

/* Imports */

const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const path = require('path')

/* State */

const prod = true

/* Namespace */

const n = 'ht'

/* Output path */

const outputPath = path.resolve(__dirname, 'assets', 'public')

/* Asset paths */

const assetsPath = path.resolve(__dirname, 'assets', 'src')
const formationPath = prod ? '@alanizcreative/formation/src' : path.resolve(__dirname, '../../../formation/src')

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
                  'ie >= 11'
                ]
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
            targets: { chrome: '58', ie: '11' }
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
                  'ie >= 11'
                ]
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
