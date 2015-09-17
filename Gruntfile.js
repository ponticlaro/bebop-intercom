/*global module:false*/
module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    meta: {
      js_path: 'assets/js',
    },
    jshint: {
      all: [
        '<%= meta.js_path %>/bebop-intercom.js'
      ]
    },
    concat: {
      main: {
        src: [
          '<%= meta.js_path %>/bebop-intercom.js'
        ],
        dest: '<%= meta.js_path %>/bebop-intercom.min.js'
      }
    },
    uglify: {
      main: {
        src: '<%= concat.main.dest %>',
        dest: '<%= concat.main.dest %>'
      }
    }
  });

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');

  // Default task.
  grunt.registerTask('default', ['jshint', 'concat', 'uglify']);

};
