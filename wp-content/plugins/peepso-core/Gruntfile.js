module.exports = function( grunt ) {
	var coreScripts, moreScripts;

	coreScripts = [
		'assets/js/peepso-pre.js',
		'assets/js/observer.js',
		'assets/js/npm-expanded.js',
		'assets/js/util.js'
	];

	moreScripts = [
		'assets/js/*.js',

		// Exclude core scripts.
		'!assets/js/peepso-core.js',
		'!assets/js/peepso-pre.js',
		'!assets/js/observer.js',
		'!assets/js/npm.js',
		'!assets/js/npm-expanded.js',
		'!assets/js/util.js',

		// Exclude minified scripts.
		'!assets/js/*.min.js'
	];

	// Load tasks.
	require( 'matchdep' ).filterDev( [ 'grunt-*' ] ).forEach( grunt.loadNpmTasks );

	// Register grunt tasks.
	grunt.registerTask( 'default', [ 'concat', 'uglify' ]);
	grunt.registerTask( 'css', [ 'sass' ]);

	// Set grunt task configurations.
	grunt.initConfig({

		concat: {
			core: {
				src: coreScripts,
				dest: 'assets/js/peepso-core.js'
			}
		},

		sass: {
			dist: {
				options: {
					style: 'compressed',
					sourcemap: 'none'
				},
				files: {
					'templates/css/template.css': 'assets/scss/styles.scss',
					'templates/css/template-rounded.css': 'assets/scss/styles-rounded.scss',
					'templates/css/template-dark.css': 'assets/scss/styles-dark.scss',
					'templates/css/template-dark-rounded.css': 'assets/scss/styles-dark-rounded.scss',

					// RTL
					'templates/css/template-rtl.css': 'assets/scss/styles-rtl.scss',
					'templates/css/template-rtl-rounded.css': 'assets/scss/styles-rtl-rounded.scss',
					'templates/css/template-dark-rounded.css': 'assets/scss/styles-dark-rounded.scss',
					'templates/css/template-dark-rtl-rounded.css': 'assets/scss/styles-dark-rtl-rounded.scss',
				}
			}
		},

		uglify: {
			core: {
				options: {
					report: 'none',
					sourceMap: true
				},
				files: [{
					src: [ 'assets/js/peepso-core.js' ],
					expand: true,
					ext: '.min.js',
					extDot: 'last'
				}],
			},
			more: {
				options: {
					report: 'none',
					sourceMap: false
				},
				files: [{
					src: moreScripts,
					expand: true,
					ext: '.min.js',
					extDot: 'last'
				}],
			}
		}

	});

};
