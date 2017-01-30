module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {
									dist: {
          options: {
           style: 'expended'
          },
          files: {
           './assets/css/wp-travel-back-end.css' : './assets/css/sass/wp-travel-back-end.scss',
           './assets/css/wp-travel-tabs.css' : './assets/css/sass/wp-travel-tabs.scss',
          }
									}
								},
        cssmin: {
         target: {
           files: {
            './assets/css/wp-travel-back-end.min.css': ['./assets/css/wp-travel-back-end.css'],
            './assets/css/wp-travel-tabs.min.css': ['./assets/css/wp-travel-tabs.css'],
           }
         }
        },
        uglify: {
         my_target: {
          files: {
           './assets/js/wp-travel-back-end.min.js' : './assets/js/wp-travel-back-end.js',
           './assets/js/wp-travel-media-upload.min.js' : './assets/js/wp-travel-media-upload.js',
           './assets/js/wp-travel-media-tabs.min.js' : './assets/js/wp-travel-media-tabs.js',
          }
         }
        },
        watch: {
         css: {
          files: 'assets/css/sass/*.scss',
          tasks: ['sass']
         },
         cssmin: {
          files: 'assets/css/*.css',
          tasks: ['cssmin']
         },
         js: {
          files: 'assets/js/*.js',
          tasks: ['uglify']
         }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    grunt.registerTask('assets', ['sass', 'cssmin', 'uglify'] );
};
