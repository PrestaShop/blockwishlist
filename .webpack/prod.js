const webpack = require('webpack');
const TerserPlugin = require('terser-webpack-plugin');
const {merge} = require('webpack-merge');
const common = require('./common.js');

/**
 * Returns the production webpack config,
 * by merging production specific configuration with the common one.
 *
 */

const prodConfig = () => merge(common, {
  stats: 'minimal',
  optimization: {
    minimizer: [
      new TerserPlugin({
        sourceMap: true,
        terserOptions: {
          output: {
            comments: /^!/,
          },
        },
        extractComments: false,
      }),
    ],
  },
  plugins: [
    // The license is written once per bundle, instead of once per source file
    new webpack.BannerPlugin({
      banner: [
        'Copyright since 2007 PrestaShop SA and Contributors',
        'PrestaShop is an International Registered Trademark & Property of PrestaShop SA',
        '@license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0',
      ].join('\n'),
    }),
    new webpack.DefinePlugin({
      'process.env.NODE_ENV': JSON.stringify('production'),
    }),
  ],
});

module.exports = prodConfig;
