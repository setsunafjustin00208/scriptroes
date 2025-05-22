module.exports = function(grunt) {
  grunt.initConfig({
    sass: {
      dist: {
        options: {
          style: 'compressed'
        },
        files: [{
          expand: true,
          cwd: 'scss',
          src: ['**/*.scss'],
          dest: '../public/resources/css',
          ext: '.min.css'
        }]
      }
    },
    uglify: {
      dist: {
        options: {
          mangle: true,
          compress: true,
          beautify: false
        },
        files: [{
          expand: true,
          cwd: 'js',
          src: ['**/*.js'],
          dest: '../public/resources/js',
          ext: '.min.js'
        }]
      }
    },
    copy: {
      fonts: {
        expand: true,
        cwd: 'fonts',
        src: '**',
        dest: '../public/resources/fonts/'
      },
      fontawesome: {
        expand: true,
        cwd: '../node_modules/@fortawesome/fontawesome-free/webfonts',
        src: '**',
        dest: '../public/resources/webfonts/'
      },
      fontawesome_css: {
        expand: true,
        cwd: '../node_modules/@fortawesome/fontawesome-free/css',
        src: ['all.min.css'],
        dest: '../public/resources/css/'
      }
    },
    watch: {
      sass: {
        files: ['scss/**/*.scss'],
        tasks: ['sass']
      },
      js: {
        files: ['js/**/*.js'],
        tasks: ['uglify']
      },
      fonts: {
        files: ['fonts/**'],
        tasks: ['copy:fonts']
      },
      fontawesome: {
        files: ['../node_modules/@fortawesome/fontawesome-free/webfonts/**', '../node_modules/@fortawesome/fontawesome-free/css/all.min.css'],
        tasks: ['copy:fontawesome', 'copy:fontawesome_css']
      }
    },
    browserify: {
      dist: {
        files: {
          '../public/resources/js/main.bundle.js': ['js/main.js']
        },
        options: {
          transform: [
            ['babelify', { presets: ['@babel/preset-env'] }],
            ['uglifyify', { global: true }]
          ],
          browserifyOptions: { debug: false }
        }
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-browserify');

  grunt.registerTask('default', ['browserify', 'sass', 'uglify', 'copy']);
};