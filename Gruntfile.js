module.exports = function(grunt) {

    // 1. All configuration goes here
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        concat: {
	            dist: {
		        src: [
                'library/js/app.js'
		        ],
		        dest: 'library/js/build/production.js', // build a file with all js in a folder named build
		    }
        },

        uglify: {
		    build: {
		        src: 'library/js/build/production.js',
		        dest: 'library/js/build/production.min.js',
		    }
		},

		sass: {                              // Task
	    dist: {                            // Target
	      options: {                       // Target options
	        style: 'expanded',
	        lineNumbers: true,
	        loadPath: require('node-bourbon').includePaths,
	        loadPath: require('node-neat').includePaths,
	        compass: true
	      },
	      files: {                         // Dictionary of files
	        'library/css/style.css': 'library/scss/style.scss'       // 'destination': 'source'
	      }
	    }
	  },

		watch: {
		    options: {
		        livereload: true,
		    },
		  scripts: {
		       files: ['library/js/*.js', 'library/js/vendor/*.js'],
		       tasks: ['concat', 'uglify'],
		        options: {
		            spawn: false,
		       },
		    },
		    css: {
			    files: ['library/scss/*.scss'],
			    tasks: ['sass'],
			    options: {
			        spawn: false,
			    }
			}
		}

    });

    // 3. Where we tell Grunt we plan to use this plug-in.
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-sass');

    // 4. Where we tell Grunt what to do when we type "grunt" into the terminal.
    grunt.registerTask('default', ['watch', 'concat', 'uglify', 'sass']);

};
