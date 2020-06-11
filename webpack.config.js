const defaultConfig = require("@wordpress/scripts/config/webpack.config");

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const path = require('path');
const { readdirSync, existsSync } = require('fs')


module.exports = {
  ...defaultConfig,
  entry: 
    {
      'admin-trip-options':[
        './app/src/admin/trip-edit/index.js',
        './app/src/admin/trip-edit/sass/main.scss',
      ],

      'frontend-booking-widget':[
        './app/src/frontend/booking-widget/index.js',
        './app/src/frontend/booking-widget/sass/main.scss',
      ],
    },
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
};