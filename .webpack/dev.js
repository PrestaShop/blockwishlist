const path = require('path');
const common = require('./common.js');
const { merge } = require('webpack-merge');

/**
 * Returns the development webpack config,
 * by merging development specific configuration with the common one.
 */
const devConfig = () => (merge(
    common,
    {
      devtool: 'inline-source-map',
      devServer: {
        hot: true,
        contentBase: path.resolve(__dirname, '/../public'),
        publicPath: '/',
      },
    },
  )
);

module.exports = devConfig;
