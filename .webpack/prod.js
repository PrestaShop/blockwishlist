const webpack = require('webpack');
const TerserPlugin = require('terser-webpack-plugin');
const common = require('./common.js');

/**
 * Returns the production webpack config,
 * by merging production specific configuration with the common one.
 *
 */
function prodConfig() {
  const prod = Object.assign(common, {
    stats: 'minimal',
    optimization: {
      minimizer: [
        new TerserPlugin({
          sourceMap: true,
          terserOptions: {
            output: {
              comments: /@license/i
            }
          },
          extractComments: false
        })
      ]
    }
  });

  // Required for Vue production environment
  prod.plugins.push(
    new webpack.DefinePlugin({
      'process.env.NODE_ENV': JSON.stringify('production')
    })
  );

  return prod;
}

module.exports = prodConfig;
