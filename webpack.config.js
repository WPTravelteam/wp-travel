const defaultConfig = require("@wordpress/scripts/config/webpack.config");
// const { CleanWebpackPlugin } = require('clean-webpack-plugin'); // for wordpress script 18
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const path = require('path');

module.exports = (env, options) => {
  const devMode    = options.mode && 'development' === options.mode;
  const fileSuffix = devMode ? '' : '.min';
  // const fileSuffix = ''; // temp fixes.
  let entries = {};
    entries['admin-trip-options' + fileSuffix ] = [
      './app/src/admin/trip-edit/sass/main.scss',
      './app/src/admin/trip-edit/index.js'
    ];
    entries['admin-settings' + fileSuffix ] = [
      './app/src/admin/settings/sass/main.scss',
      './app/src/admin/settings/index.js'
    ];
    entries['frontend-booking-widget' + fileSuffix ] = [
      './app/src/frontend/booking/sass/main.scss',
      './app/src/frontend/booking/index.js'
    ];
    entries['wp-travel-back-end' + fileSuffix ] = [
      './app/assets/js/wp-travel-back-end.js',
      './app/src/admin/sass/main.scss',
    ];
    entries['admin' + fileSuffix ] = [
      './app/assets/js/wp-travel-backend-pointers.js', // just to add js entry point. temp fixes
      './app/src/admin/sass/style.scss', // style which need to inclued in all admin pages. it contents admin menu logo icon class
    ];
    entries['wp-travel-front-end' + fileSuffix ] = [
      './app/assets/js/wp-travel-front-end.js',
      './app/src/frontend/sass/main.scss',
    ];

	entries['wp-travel-front-end-v2' + fileSuffix ] = [
		'./app/src/frontend/sass/v2/main.scss',
	];

    entries['admin-coupon' + fileSuffix ] = [
      './app/src/admin/coupon/index.js',
      './app/src/admin/coupon/sass/main.scss',
    ];

    entries['admin-enquiry' + fileSuffix ] = [
      './app/src/admin/enquiry/index.js',
      ];
    // console.log('entries', entries);
    // entries['legacy-widgets' + fileSuffix ] = [
    //   './app/src/LegacyWidgets/BlockWidgets.js',
    // ];
  entries['wptravel-admin-widgets' + fileSuffix ] = [
    './app/src/admin/widgets/index.js',
    './app/src/admin/widgets/sass/main.scss',
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
      // new CleanWebpackPlugin({
      //   cleanStaleWebpackAssets: false,
      //   protectWebpackAssets: false,
      // }),
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
          test: /\.(png|woff|woff2|eot|ttf|svg)$/,
          use: [
            {
              loader: 'url-loader?limit=100000'
            }
          ]
        },
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
