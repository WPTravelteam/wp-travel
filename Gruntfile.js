/* jshint node:true */
module.exports = function (grunt) {

	const hbs = require('handlebars');
	const fs = require('fs');

	/**
	 * FIles added to WordPress SVN, don't inlucde 'assets/**' here.
	 * @type {Array}
	 */
	svn_files_list = [
		'assets/**',
		'!assets/js/src/**',
		'!assets/sass/**',
		'!assets/css/*.map',
		'i18n/**',
		'inc/**',
		'!inc/extended/**',
		'templates/**',
		'upgrade/**',
		'loco.xml',
		'readme.txt',
		'wp-travel.php',
		'wpml-config.xml',
		'!inc/extended/app/src/**',
		'!inc/extended/package.json',
		'!inc/extended/postcss.config.js',
		'!inc/extended/webpack.config.js',
		'!inc/extended/yarn.lock',
		'!inc/extended/yarn-error.log',
		'!app/src/**',
		'!yarn-error.log',
		'!yarn.lock',
		'!postcss.config.js',
		'!webpack.config.js',
		'app/build/**',
		'!app/build/*.map',
		'app/inc/**',
		'app/assets/**',
		'core/**'
	];

	/**
	 * Let's add a couple of more files to github.
	 * @type {Array}
	 */
	git_files_list = svn_files_list.concat([
		'bash',
		'\.editorconfig',
		'\.gitattributes',
		'\.gitignore',
		'\.gitlab-ci.yml',
		'\.jshintrc',
		'assets/js/src/**',
		'Gruntfile.js',
		'package-lock.json',
		'package.json',
		'push_dot_org.sh',
		'postcss.config.js',
		'yarn.lock',
		'webpack.config.js'
	]);

	let package_json = fs.readFileSync('package.json');
	package_json = JSON.parse(package_json);

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),
		clean: {
			deploy: ['build'],
			post_build: [
				'build'
			],
			postx_build: [
				'bundle/<%= pkg.version %>/<%= pkg.name %>'
			]
		},
		copy: {
			build_it: {
				options: {
					mode: true
				},
				expand: true,
				src: svn_files_list,
				dest: 'bundle/<%= pkg.version %>/<%= pkg.name %>/'
			},
			deploy: {
				src: [
					'**',
					'!.*',
					'!*.md',
					'1*.map',
					'!.*/**',
					'!tmp/**',
					'!Gruntfile.js',
					'!test.php',
					'!package.json',
					'!node_modules/**',
					'!tests/**',
					'!docs/**',
					'!assets/sass/**',
					'!assets/js/src/**',
					'!inc/extended/node_modules/**',
					'!inc/extended/app/src/**',
					'!inc/extended/package.json',
					'!inc/extended/postcss.config.js',
					'!inc/extended/webpack.config.js',
					'!inc/extended/yarn.lock',
					'!inc/extended/yarn-error.log',
					'!app/src/**',
					'!yarn-error.log',
					'!yarn.lock',
					'!postcss.config.js',
					'!webpack.config.js'
				],
				dest: 'deploy/<%= pkg.name %>',
				expand: true,
				dot: true
			}
		},
		// Setting folder templates.
		dirs: {
			js: 'assets/js',
			css: 'assets/css',
			images: 'assets/images'
		},
		"file-creator": {
			"folder": {
				".gitattributes": function (fs, fd, done) {
					var glob = grunt.file.glob;
					var _ = grunt.util._;
					fs.writeSync(fd, '# We don\'t want these files in our "plugins.zip", so tell GitHub to ignore them when the user click on Download ZIP' + '\n');
					_.each(git_files_list.diff(svn_files_list), function (filepattern) {
						glob.sync(filepattern, function (err, files) {
							_.each(files, function (file) {
								fs.writeSync(fd, '/' + file + ' export-ignore' + '\n');
							});
						});
					});
				}
			}
		},
		// Other options.
		options: {
			text_domain: 'wp-travel'
		},
		// Generate POT files.
		makepot: {
			target: {
				options: {
					type: 'wp-plugin',
					domainPath: 'i18n/languages',
					exclude: ['deploy/.*', 'node_modules/.*', 'bundle/.*', 'wordpress/.*', 'vendor/.*'],
					updateTimestamp: false,
					potHeaders: {
						'report-msgid-bugs-to': '',
						'x-poedit-keywordslist': true,
						'language-team': '',
						'Language': 'en_US',
						'X-Poedit-SearchPath-0': '../../<%= pkg.name %>',
						'plural-forms': 'nplurals=2; plural=(n != 1);',
						'Last-Translator': 'WEN Solutions <info@wensolutions.com>'
					}
				}
			}
		},

		// Update text domain.
		addtextdomain: {
			options: {
				textdomain: '<%= options.text_domain %>',
				updateDomains: true
			},
			target: {
				files: {
					src: [
						'*.php',
						'**/*.php',
						'!node_modules/**',
						'!deploy/**',
						'!tests/**',
						'!app/src',
						'!app/build'
					]
				}
			}
		},

		// Check textdomain errors.
		checktextdomain: {
			options: {
				text_domain: '<%= options.text_domain %>',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src: [
					'**/*.php',
					'!node_modules/**',
					'!deploy/**',
					'!app/build',
					'!app/src'
				],
				expand: true
			}
		},

		uglify: {
			options: {
				mangle: {
					reserved: ['jQuery', 'Backbone', 'wp_travel']
				}
			},
			my_target: {
				files: {
					'app/assets/js/wp-travel-frontend.bundle.js': [
						'app/assets/js/lib/datepicker/datepicker.js', // jquery-datepicker-lib
						'app/assets/js/lib/jquery.magnific-popup/jquery.magnific-popup.min.js',
						'app/assets/js/lib/slick/slick.min.js',
						'app/assets/js/wp-travel-accordion.js',//wp-travel-accordion ['jquery','jquery-ui-accordion']
						'app/assets/js/lib/parsley/parsley.min.js',
						'app/assets/js/booking.js',//wp-travel-booking ['jquery']
						'app/assets/js/lib/isotope/isotope.pkgd.js',
						'app/assets/js/wp-travel-front-end.js', // wp-travel-script ['jquery', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng', 'jquery-ui-accordion']
						'app/assets/js/cart.js',//wp-travel-cart ['wp-util','jquery-datepicker-lib', 'jquery-datepicker-lib-eng']
						'app/assets/js/wp-travel-view-mode.js',
						'app/assets/js/wp-travel-widgets.js',
						'app/assets/js/collapse.js',//collapse-js ['jquery]
						'app/assets/js/lib/hc-sticky/hc-sticky.js',
						'app/assets/js/wp-travel-front-end-map.js',
					],
					'app/assets/js/booking.min.js': ['app/assets/js/booking.js'],
					'app/assets/js/wp-travel-widgets.min.js': ['app/assets/js/wp-travel-widgets.js'],
					'app/assets/js/wp-travel-accordion.min.js': ['app/assets/js/wp-travel-accordion.js'],
					'app/assets/js/lib/easy-responsive-tabs/easy-responsive-tabs.min.js': ['app/assets/js/lib/easy-responsive-tabs/easy-responsive-tabs.js'],
					'app/assets/js/collapse.min.js': ['app/assets/js/collapse.js'],
					'app/assets/js/wp-travel-view-mode.min.js': ['app/assets/js/wp-travel-view-mode.js'],
					'app/assets/js/payment.min.js': ['app/assets/js/payment.js'],
					'app/assets/js/booking.min.js': ['app/assets/js/booking.js'],

					'app/assets/js/jquery.wptraveluploader.min.js': ['app/assets/js/jquery.wptraveluploader.js'],
					'app/assets/js/wp-travel-back-end.min.js': ['app/assets/js/wp-travel-back-end.js'],
					'app/assets/js/wp-travel-front-end.min.js': ['app/assets/js/wp-travel-front-end.js'],
					'app/assets/js/wp-travel-front-end-map.min.js': ['app/assets/js/wp-travel-front-end-map.js'],
					'app/assets/js/wp-travel-media-upload.min.js': ['app/assets/js/wp-travel-media-upload.js'],
					'app/assets/js/wp-travel-tabs.min.js': ['app/assets/js/wp-travel-tabs.js'],
					'app/assets/js/wp-travel-fields-scripts.min.js': ['app/assets/js/wp-travel-fields-scripts.js'],
					'app/assets/js/cart.min.js': ['app/assets/js/cart.js']
				}
			}
		},
		sass: {
			options: {
				// sourcemap: 'none',
				style: 'expanded',
				lineNumbers: false
			},
			dist: {
				files: {
					'app/build/main.css': 'assets/sass/main.scss',
					'inc/coupon/assets/css/wp-travel-coupons-backend.css': 'inc/coupon/assets/css/sass/wp-travel-coupons-backend.scss',
					'inc/coupon/assets/css/wp-travel-coupons-frontend.css': 'inc/coupon/assets/css/sass/wp-travel-coupons-frontend.scss',
				}
			}
		},
		watch: {
			css: {
				files: ['assets/sass/**/*.scss'],
				tasks: ['sass'],
			},
			babel: {
				files: ['app/assets/js/src/*.js'], // only for checkout js
				tasks: ['babel'],
			}
		},
		// CSS minification.
		cssmin: {
			target: {
				files: {
					'app/build/wp-travel-frontend.bundle.css': [
						'app/assets/css/lib/magnific-popup/magnific-popup.css', // wp-travel-popup
						'app/assets/css/lib/easy-responsive-tabs/easy-responsive-tabs.css', // easy-responsive-tabs
						'app/assets/css/lib/datepicker/datepicker.css', //
						'app/assets/css/lib/slick/slick.min.css',
						'app/build/wp-travel-front-end.css',
					],
					'app/assets/css/lib/font-awesome/css/wp-travel-fonts.bundle.css': [
						'app/assets/css/lib/font-awesome/css/fontawesome-all.css',
						'app/assets/css/lib/font-awesome/css/wp-travel-fa-icons.css'
					],
					// 'assets/css/wp-travel-rtl-back-end.min.css': ['assets/css/wp-travel-rtl-back-end.css'],
					// 'assets/css/wp-travel-rtl-front-end.min.css': ['assets/css/wp-travel-rtl-front-end.css'],
					// 'assets/css/wp-travel-rtl-tabs.min.css': ['assets/css/wp-travel-rtl-tabs.css'],
					// 'assets/css/wp-travel-rtl-user-styles.min.css': ['assets/css/wp-travel-rtl-user-styles.css'],
					// 'app/assets/css/lib/magnific-popup/magnific-popup.min.css': ['app/assets/css/lib/magnific-popup/magnific-popup.css'],
					// 'app/assets/css/lib/easy-responsive-tabs/easy-responsive-tabs.min.css': ['app/assets/css/lib/easy-responsive-tabs/easy-responsive-tabs.css'],

					'inc/coupon/assets/css/wp-travel-coupons-backend.min.css': ['inc/coupon/assets/css/wp-travel-coupons-backend.css'],
					'inc/coupon/assets/css/wp-travel-coupons-frontend.min.css': ['inc/coupon/assets/css/wp-travel-coupons-frontend.css'],
				}
			}
		},

		// Check JS.
		jshint: {
			// options: grunt.file.readJSON( '.jshintrc' ),
			all: [
				'Gruntfile.js',
				'<%= dirs.js %>/*.js',
				'!<%= dirs.js %>/*.min.js'
			]
		},

		babel: {
			options: {
				sourceMap: false,
				presets: ['@babel/preset-env'],
				sourceType: 'script'
			},
			dist: {
				files: {
					'app/assets/js/cart.js': 'app/assets/js/src/_checkout.js'
				}
			}
		},

		// Compress files.
		compress: {
			deploy: {
				expand: true,
				options: {
					archive: 'deploy/<%= pkg.name %>-<%= pkg.version %>.zip'
				},
				cwd: 'deploy/<%= pkg.name %>/',
				src: ['**/*', '!bundle/**'],
				dest: '<%= pkg.name %>/'
			}
		},

		zip: {
			// 'build/<%= pkg.name %>-<%= pkg.version %>.zip': [svn_files_list]
			'using-delate': {
				cwd: 'bundle/<%= pkg.version %>/',
				src: ['bundle/<%= pkg.version %>/<%= pkg.name %>/**'],
				dest: 'bundle/<%= pkg.name %>-<%= pkg.version %>.zip',
				compression: 'DEFLATE'
			}
		},

		rtlcss: {
			myTask: {
				// task options
				options: {
					// generate source maps
					map: {
						inline: false
					},
					// rtlcss options
					opts: {
						clean: false
					},
					// rtlcss plugins
					plugins: [],
					// save unmodified files
					saveUnmodified: true,
				},
				expand: true,
				cwd: 'assets/css',
				dest: 'assets/css/rtl/',
				src: ['assets/css/*.css']
			}
		},

		// writefile: {
		// 	options: {
		// 		// data: 'path/to/data.json',
		// 		data: package_json,
		// 	},
		// 	main: {
		// 		files: [{
		// 			src: 'bash/push_dot_org.hbs',
		// 			dest: `push_dot_org.sh`,
		// 		}]
		// 	}
		// }

	});

	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-wp-i18n');
	grunt.loadNpmTasks('grunt-checktextdomain');
	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-file-creator');
	grunt.loadNpmTasks('grunt-svn-export');
	grunt.loadNpmTasks('grunt-push-svn');
	grunt.loadNpmTasks('grunt-writefile');
	grunt.loadNpmTasks('grunt-babel');

	// Load in `grunt-zip`
	grunt.loadNpmTasks('grunt-zip');

	grunt.loadNpmTasks('grunt-rtlcss');

	// Register tasks.
	grunt.registerTask('default', []);

	grunt.registerTask('gitattributes', ['file-creator']);
	// grunt.registerTask('babel', ['babel']);

	grunt.registerTask('assets', [
		'babel',
		'uglify',
		// 'sass',
		'cssmin',
	]);

	grunt.registerTask('precommit', [
		'jshint',
		'checktextdomain'
	]);

	grunt.registerTask('textdomain', [
		'addtextdomain',
		'makepot'
	]);
	grunt.registerTask('minify', [
		'uglify',
		'cssmin',
	]);

	grunt.registerTask('rtlcss', [
		'rtlcss',
	]);

	grunt.registerTask('deploy', [
		'clean:deploy',
		'copy:deploy',
		'compress:deploy'
	]);

	grunt.registerTask('pre_vcs', ['assets', 'textdomain']);
	grunt.registerTask('pre_release', ['pre_vcs']);
	grunt.registerTask('release', ['push_svn']);
	grunt.registerTask('post_release', ['clean:post_build']);

	grunt.registerTask('build', ['clean:deploy', 'pre_release', 'copy:build_it', 'zip']);
};

/**
 * Helper
 */
// from http://stackoverflow.com/a/4026828/1434155
Array.prototype.diff = function (a) {
	return this.filter(function (i) {
		return a.indexOf(i) < 0;
	});
};
