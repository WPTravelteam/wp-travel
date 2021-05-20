const defaultConfig = require("@wordpress/scripts/config/webpack.config");

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const path = require('path');

module.exports = (env, options) => {
  const fileSuffix = options.mode && 'development' === options.mode ? '.dev' : '';
  let entries = {};
    entries['admin-trip-options' + fileSuffix ] = [
      './app/src/admin/trip-edit/index.js',
      './app/src/admin/trip-edit/sass/main.scss',
    ];
    entries['admin-settings' + fileSuffix ] = [
      './app/src/admin/settings/index.js',
      './app/src/admin/settings/sass/main.scss',
    ];
    entries['frontend-booking-widget' + fileSuffix ] = [
      './app/src/frontend/booking-widget/index.js',
      './app/src/frontend/booking-widget/sass/main.scss',
    ];
  return {
    ...defaultConfig,
    entry: entries,
      output:{
        ...defaultConfig.output,
        path: path.resolve( process.cwd(), 'app/build' )
      },
    plugins: [
      ...defaultConfig.plugins,
      new MiniCssExtractPlugin({
        // Options similar to the same options in webpackOptions.output
        // all options are optional
        filename: '[name].css',
        chunkFilename: '[id].css',
        ignoreOrder: false, // Enable to remove warnings about conflicting order
      }),
    ],
    module: {
      ...defaultConfig.module,
      rules: [
        ...defaultConfig.module.rules,
        {
          test: /\.scss$/,
          use: [
            { loader: 'style-loader' },
            MiniCssExtractPlugin.loader,
            { loader: 'css-loader' },
            { loader: 'postcss-loader' },
            {
              loader: 'sass-loader', options: { sourceMap: true }
            }
          ],
        },
      ]
    }
  }
};
