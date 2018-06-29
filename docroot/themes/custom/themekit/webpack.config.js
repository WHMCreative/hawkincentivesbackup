const webpack = require("webpack");
const resolve = require("resolve");
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const autoprefixer = require('autoprefixer');

var entryPoints = {
  themekit: './js/src/theme.js',
  style: './sass/style.scss',
  wysiwyg: './sass/wysiwyg.scss',
  'card-catalog': './js/src/v-card-catalog/card-catalog.js'
  // hotelFilters: "./js/src/v-hotel-filters/hotel-filters.js",
};

var compiledEntries = {};

for (var prop in entryPoints) {
  compiledEntries[prop] = entryPoints[prop];
  // compiledEntries[prop + ".min"] = entryPoints[prop];
}

var config = {
  context: __dirname,
  entry: compiledEntries,

  output: {
    path: __dirname + '/dist',
    filename: "[name].js"
  },
  resolve: {
    extensions: ['.js', '.vue', '.json'],
    alias: {
      'vue$': 'vue/dist/vue.esm.js'
    }
  },
  externals: {
    jquery: 'jQuery'
  },

  devtool: 'cheap-source-map',
  plugins: [
    // The below will shim global jquery, the first two lines will replace $/jQuery when require('jquery') is called
    // The third line, which we probably will always need with Drupal, then uses the window.jQuery instead of the
    // module jquery when require('jquery') is called
    new webpack.ProvidePlugin({
      $: "jquery",
      jQuery: "jquery",
      "window.jQuery": "jquery"
    }),
    new webpack.optimize.CommonsChunkPlugin({
      name: "commons",
      // (the commons chunk name)

      filename: "commons.js",
      // (the filename of the commons chunk)

      // minChunks: 3,
      // (Modules must be shared between 3 entries)

      // chunks: ["pageA", "pageB"],
      // (Only use these entries)
    }),
    new BrowserSyncPlugin({
      // browse to http://localhost:3000/ during development,
      // ./public directory is being served
      host: 'localhost',
      port: 3000,
      proxy: 'bhk-d8.drupalvm',
      https: false,
      files: ["./sass/**/*.scss", "./js/**/*.js", "./js/**/*.vue"]
    }),
    new ExtractTextPlugin({ // define where to save the file
      filename: 'css/[name].css',
      allChunks: true,
    }),
    autoprefixer,
  ],
  module: {
    loaders: [
      {
        test: /\.vue$/,
        loader: 'vue'
      },
      {
        test: /\.js$/,
        // must add exceptions to this exlude statement for anything that needs to be transpiled by babel
        exclude: /node_modules\/(?!foundation-sites)/,
        loader: 'babel-loader',
        query: {
          presets: ["env"]
        }
      }
    ],
    rules: [
      {
        test: /\.vue$/,
        loader: 'vue-loader'
      },
      { // regular css files
        test: /\.css$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: [
            {
              loader: 'css-loader',
              options: {
                import: false,
                importLoaders: 1,
                minimize: false,
                sourceMap: true,
                url: false,
              },
            },
            {
              loader: 'postcss-loader',
              options: {
                sourceMap: true,
              },
            },
          ],
        }),
      },
      { // sass / scss loader for webpack
        test: /\.scss$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: [
            {
              loader: 'css-loader',
              options: {
                url: false,
                minimize: true,
                sourceMap: true
              }
            },
            {
              loader: 'sass-loader',
              options: {
                sourceMap: true,
                includePaths: ['node_modules/foundation-sites/scss']
              }
            }
          ]
        })
      }
    ]
  }
};

module.exports = config;
