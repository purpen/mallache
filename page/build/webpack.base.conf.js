var path = require('path')
var utils = require('./utils')
var config = require('../config')
var vueLoaderConfig = require('./vue-loader.conf')
var os = require('os')
var HappyPack = require('happypack')
var happThreadPool = HappyPack.ThreadPool({ size: os.cpus().length })
// var nodeExternals = require('webpack-node-externals')
process.noDeprecation = true
var webpack = require('webpack')
// var ignoreFiles = new webpack.IgnorePlugin(/\.\.dll.js$/)
// var ignoreFiles = new webpack.IgnorePlugin(/\.\/pdfmake.dll.js$/)
// var ignoreFiles = new webpack.IgnorePlugin(/\.\/vfs_fonts.dll.js$/)
// var ignoreFiles = new webpack.IgnorePlugin(/pdfmake.min$/, /vfs_fonts$/)
function resolve(dir) {
  return path.join(__dirname, '..', dir)
}

module.exports = {
  entry: {
    app: ['babel-polyfill', './src/main.js']
  },
  externals: {
  },
  // externals: [nodeExternals()],
  output: {
    path: config.build.assetsRoot,
    filename: '[name].js',
    publicPath: process.env.NODE_ENV === 'production'
      ? config.build.assetsPublicPath
      : config.dev.assetsPublicPath
  },
  cache: true,
  resolve: {
    extensions: ['.js', '.vue', '.json', '.styl'],
    alias: {
      'vue$': 'vue/dist/vue.esm.js',
      '@': resolve('src'),
      'components': resolve('src/components'),
      'assets': resolve('src/assets'),
      'static': resolve('/static')
    }
  },
  // 增加一个plugins
  plugins: [
    // ignoreFiles,
    new HappyPack({
      id: 'js',
      threads: 4,
      loaders: [
        {
          loader: 'babel-loader',
          query: {
            presets: ['es2015', 'stage-2']
          }
        }
      ],
      threadPool: happThreadPool
    }),
    new HappyPack({
      id: 'eslint',
      threads: 4,
      loaders: [{
        loader: 'eslint-loader',
        // here you can place eslint-loader options:
        options: {
          formatter: require('eslint-friendly-formatter')
        }
      }],
      threadPool: happThreadPool
    }),
    new webpack.DllReferencePlugin({
      context: __dirname,
      manifest: require('../static/js/vendor/core-mainfest.json') // 指向这个json
    }),
    new webpack.DllReferencePlugin({
      context: __dirname,
      manifest: require('../static/js/vendor/pdfmake-mainfest.json') // 指向这个json
    }),
    new webpack.DllReferencePlugin({
      context: __dirname,
      manifest: require('../static/js/vendor/vfs_fonts-mainfest.json') // 指向这个json
    })
  ],
  module: {
    rules: [
      {
        test: /\.(js|vue)$/,
        loader: 'happypack/loader?id=eslint',
        enforce: "pre",
        include: [resolve('src'), resolve('test')]
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
          loaders: {
            js: 'happypack/loader?id=js' // 将loader换成happypack
          }
        }
      },
      {
        test: /\.js$/,
        loader: ['happypack/loader?id=js'],
        exclude: [/node_modules/]
      },
      {
        test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
        loader: 'url-loader',
        options: {
          limit: 10000,
          name: utils.assetsPath('img/[name].[hash:5].[ext]')
        }
      },
      {
        test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
        loader: 'url-loader',
        options: {
          limit: 10000,
          name: utils.assetsPath('fonts/[name].[hash:5].[ext]')
        }
      }
    ],
    noParse: /node_modules\/(jquery|pdf.js)/
  }
}
