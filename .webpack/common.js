/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
const path = require('path');
const webpack = require('webpack');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');

module.exports = {
  externals: {
    jquery: 'jQuery',
    prestashop: 'prestashop'
  },
  entry: {
    list: './_dev/front/js/pages/list',
    button: './_dev/front/js/components/Button',
    create: './_dev/front/js/components/Create',
    addtowishlist: './_dev/front/js/components/AddToWishlist',
    wishlist: ['./_dev/front/scss/common.scss']
  },
  output: {
    path: path.resolve(__dirname, '../public'),
    filename: '[name].bundle.js',
    libraryTarget: 'window',
    library: '[name]',

    sourceMapFilename: '[name].[hash:8].map',
    chunkFilename: '[id].[hash:8].js'
  },
  resolve: {
    extensions: ['.js', '.vue', '.json', '.mjs'],
    alias: {
      '@js': path.resolve(__dirname, '../_dev/front/js'),
      '@pages': path.resolve(__dirname, '../_dev/front/js/pages'),
      '@graphqlFiles': path.resolve(__dirname, '../_dev/front/js/graphql'),
      '@components': path.resolve(__dirname, '../_dev/front/js/components'),
      '@scss': path.resolve(__dirname, '../_dev/front/scss'),
      '@node_modules': path.resolve(__dirname, '../node_modules'),
      vue: 'vue/dist/vue.esm.js'
    }
  },
  module: {
    rules: [
      {
        test: /\.mjs$/,
        include: /node_modules/,
        type: 'javascript/auto'
      },
      {
        test: /\.js$/,
        include: path.resolve(__dirname, '../_dev'),
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: [['env', {useBuiltIns: 'usage', modules: false}]],
              plugins: ['transform-object-rest-spread']
            }
          }
        ]
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader'
      },
      {
        test: /\.css$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader
          },
          'css-loader'
        ]
      },
      {
        test: /\.scss$/,
        include: /scss/,
        exclude: /js/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              sourceMap: true
            }
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true
            }
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: true
            }
          }
        ]
      },
      {
        test: /\.scss$/,
        include: /js/,
        use: ['vue-style-loader', 'css-loader', 'sass-loader']
      },
      // FILES
      {
        test: /.(jpg|png|woff2?|eot|otf|ttf|svg|gif)$/,
        loader: 'file-loader?name=[hash].[ext]'
      }
    ]
  },
  plugins: [
    new FixStyleOnlyEntriesPlugin(),
    new CleanWebpackPlugin({
      root: path.resolve(__dirname, '../'),
      exclude: ['theme.rtlfix']
    }),
    new MiniCssExtractPlugin({filename: '[name].css'}),
    new webpack.ProvidePlugin({
      moment: 'moment', // needed for bootstrap datetime picker
      $: 'jquery', // needed for jquery-ui
      jQuery: 'jquery'
    }),
    new VueLoaderPlugin()
  ]
};
