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
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');

module.exports = {
  externals: {
    jquery: 'jQuery',
    prestashop: 'prestashop',
    blockwishlistModule: 'blockwishlistModule',
    removeFromWishlistUrl: 'removeFromWishlistUrl',
    wishlistAddProductToCartUrl: 'wishlistAddProductToCartUrl',
    wishlistUrl: 'wishlistUrl',
  },
  entry: {
    wishlist: ['./_dev/front/js', './_dev/front/scss/common.scss'],
    backoffice: [
      './_dev/back/js/backoffice.js',
      './_dev/back/scss/backoffice.scss',
    ],
    form: ['./_dev/back/js/form.js', './_dev/back/scss/backoffice.scss'],
  },
  output: {
    path: path.resolve(__dirname, '../public'),
    filename: '[name].bundle.js',
    libraryTarget: 'window',
    library: '[name]',
    sourceMapFilename: '[name].[hash:8].map',
    chunkFilename: '[name].js',
  },
  resolve: {
    extensions: ['.js', '.json', '.mjs'],
    alias: {
      '@js': path.resolve(__dirname, '../_dev/front/js'),
    },
  },
  module: {
    rules: [
      {
        test: /\.mjs$/,
        include: /node_modules/,
        type: 'javascript/auto',
      },
      {
        test: /\.js$/,
        include: path.resolve(__dirname, '../_dev/back'),
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: [['@babel/preset-env', {useBuiltIns: 'usage', modules: false}]],
              plugins: ['@babel/plugin-transform-runtime'],
            },
          },
        ],
      },
      {
        // Supported browsers run the front office code without polyfill
        test: /\.js$/,
        include: path.resolve(__dirname, '../_dev/front'),
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: [['@babel/preset-env', {targets: {esmodules: true}, bugfixes: true, modules: false}]],
            },
          },
        ],
      },
      {
        test: /\.css$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
          },
          'css-loader',
        ],
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
              sourceMap: true,
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,
            },
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: true,
            },
          },
        ],
      },
      // FILES
      {
        test: /.(jpg|png|woff2?|eot|otf|ttf|svg|gif)$/,
        loader: 'file-loader?name=[hash].[ext]',
      },
    ],
  },
  plugins: [
    new FixStyleOnlyEntriesPlugin(),
    new CleanWebpackPlugin({
      root: path.resolve(__dirname, '../'),
      exclude: ['theme.rtlfix'],
    }),
    new MiniCssExtractPlugin({filename: '[name].css'}),
    new webpack.ProvidePlugin({
      moment: 'moment', // needed for bootstrap datetime picker
      $: 'jquery', // needed for jquery-ui
      jQuery: 'jquery',
    }),
  ],
};
