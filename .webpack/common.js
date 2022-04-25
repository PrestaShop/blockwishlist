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
const VueLoaderPlugin = require('vue-loader/lib/plugin');
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
    button: ['./_dev/front/js/components/Button'],
    create: './_dev/front/js/components/Create',
    rename: './_dev/front/js/components/Rename',
    addtowishlist: './_dev/front/js/components/AddToWishlist',
    productslist: [
      './_dev/front/js/container/ProductsListContainer',
      './_dev/front/js/components/Pagination',
      './_dev/front/js/components/Toast',
      './_dev/front/js/components/Delete',
    ],
    wishlistcontainer: [
      './_dev/front/js/container/WishlistContainer',
      './_dev/front/js/components/Create',
      './_dev/front/js/components/Delete',
      './_dev/front/js/components/Toast',
      './_dev/front/js/components/Share',
      './_dev/front/js/components/Rename',
    ],
    wishlist: ['./_dev/front/scss/common.scss'],
    product: [
      './_dev/front/js/pages/list',
      './_dev/front/js/components/Button',
      './_dev/front/js/components/Toast',
      './_dev/front/js/components/Login',
      './_dev/front/js/components/Create',
      './_dev/front/js/components/AddToWishlist',
    ],
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
    extensions: ['.js', '.vue', '.json', '.mjs', '.ts'],
    alias: {
      '@js': path.resolve(__dirname, '../_dev/front/js'),
      '@pages': path.resolve(__dirname, '../_dev/front/js/pages'),
      '@graphqlFiles': path.resolve(__dirname, '../_dev/front/js/graphql'),
      '@components': path.resolve(__dirname, '../_dev/front/js/components'),
      '@containers': path.resolve(__dirname, '../_dev/front/js/container'),
      '@constants': path.resolve(__dirname, '../_dev/front/js/constants'),
      '@scss': path.resolve(__dirname, '../_dev/front/scss'),
      '@PSJs': path.resolve(
        __dirname,
        '../../../admin-dev/themes/new-theme/js',
      ),
      '@node_modules': path.resolve(__dirname, '../node_modules'),
      vue: 'vue/dist/vue.esm.js',
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
        include: path.resolve(__dirname, '../_dev'),
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: [['env', {useBuiltIns: 'usage', modules: false}]],
              plugins: ['transform-object-rest-spread', 'transform-runtime'],
            },
          },
        ],
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader',
      },
      {
        test: /\.ts?$/,
        loader: 'ts-loader',
        options: {
          appendTsSuffixTo: [/\.vue$/],
          onlyCompileBundledFiles: true,
        },
        exclude: /node_modules/,
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
      {
        test: /\.scss$/,
        include: /js/,
        use: ['vue-style-loader', 'css-loader', 'sass-loader'],
      },
      // FILES
      {
        test: /.(jpg|png|woff2?|eot|otf|ttf|svg|gif)$/,
        loader: 'file-loader?name=[hash].[ext]',
      },
    ],
  },
  optimization: {
    splitChunks: {
      cacheGroups: {
        graphql: {
          test: /[\\/]node_modules[\\/](graphql|graphql-tag|graphql-tools|graphql-type-json)[\\/]/,
          name: 'graphql',
          chunks: 'all',
        },
        vendors: {
          // eslint-disable-next-line max-len
          test: /[\\/]node_modules[\\/](core-js|apollo-utilities|apollo-client|apollo-link|apollo-cache-inmemory|apollo-link-http|apollo-link-schema|vue|vue-apollo)[\\/]/,
          name: 'vendors',
          chunks: 'all',
        },
      },
    },
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
    new VueLoaderPlugin(),
  ],
};
