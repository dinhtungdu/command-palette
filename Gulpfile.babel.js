import gulp from 'gulp';
import notify from 'gulp-notify';
import beeper from 'beeper';
import plumber from 'gulp-plumber';
import sourcemaps from 'gulp-sourcemaps';
import sass from 'gulp-sass';
import postcss from 'gulp-postcss';
import autoprefixer from 'autoprefixer';
import mqpacker from 'css-mqpacker';
import browserSync from 'browser-sync';
import csso from 'gulp-csso';
import svgmin from 'gulp-svgmin';
import svgstore from 'gulp-svgstore';
import cheerio from 'gulp-cheerio';
import imagemin from 'gulp-imagemin';
import spritesmith from 'gulp.spritesmith';
import eslint from 'gulp-eslint';
import stylelint from 'gulp-stylelint';
import wpPot from 'gulp-wp-pot';
import sort from 'gulp-sort';
import uglify from 'gulp-uglify';
import browserify from 'browserify';
import babelify from 'babelify';
import buffer from 'gulp-buffer';
import tap from 'gulp-tap';
import log from 'fancy-log';
import zip from 'gulp-zip';
import del from 'del';
import replace from 'gulp-replace';

import * as pkg from './package.json';

const paths = {
	images: [
		'./assets/src/images/*',
		'./assets/src/images/**/*',
		'!assets/src/images/sprites',
		'!assets/src/images/sprites/*',
		'!assets/src/images/icons',
		'!assets/src/images/icons/*'
	],
	svg: './assets/src/images/icons/*.svg',
	scss: './assets/src/scss/**/*.scss',
	js: './assets/src/js/**/*.js',
	css: './assets/css/**/*.css',
	sprites: './assets/src/images/sprites/*.png',
	php: [ './*.php', './**/*.php', '!./vendor/**/*.php' ]
};

/**
 * Handle errors and alert the user.
 */
const handleErrors = err => {
	notify.onError( {
		title: 'Task Failed [<%= error.message %>',
		message: 'See console.',
		sound: 'Sosumi' // See: https://github.com/mikaelbr/node-notifier#all-notification-options-with-their-defaults
	} )( err );

	beeper(); // Beep 'sosumi' again.

	// Prevent the 'watch' task from stopping.
	this.emit( 'end' );
};

/**
 * Compile Sass and run stylesheet through PostCSS.
 *
 * https://www.npmjs.com/package/gulp-sass
 * https://www.npmjs.com/package/gulp-postcss
 * https://www.npmjs.com/package/gulp-autoprefixer
 * https://www.npmjs.com/package/css-mqpacker
 */
export function compileStyles() {
	return gulp
		.src( paths.scss, { since: gulp.lastRun( compileStyles ) } )
		.pipe( plumber( { errorHandler: handleErrors } ) )
		.pipe( sourcemaps.init() )
		.pipe(
			sass( {
				includePaths: [],
				errLogToConsole: true,
				outputStyle: 'expanded' // Options: nested, expanded, compact, compressed
			} )
		)
		.pipe(
			postcss([
				autoprefixer( {
					browsers: [ 'last 2 version' ]
				} ),
				mqpacker( {
					sort: true
				} )
			])
		)
		.pipe( sourcemaps.write( '.', {
			includeContent: false,
			sourceRoot: '../..'
		} ) )
		.pipe( gulp.dest( './assets/css/' ) )
		.pipe( browserSync.stream() );
}

/**
 * Minify and optimize style.css.
 *
 * https://www.npmjs.com/package/gulp-csso
 */
export function minifyStyles() {
	return gulp
		.src( paths.css )
		.pipe( plumber( { errorHandler: handleErrors } ) )
		.pipe( csso() )
		.pipe( gulp.dest( './assets/css/' ) );
}

/**
 * Transform ES6+ to browser JS
 *
 * @returns {*}
 */
export function compileScripts() {
	return gulp
		.src( paths.js, { read: false, since: gulp.lastRun( compileScripts ) } )
		.pipe(
			tap( function( file ) {
				log.info( 'Bundling ' + file.path );
				file.contents = browserify( file.path, { debug: true } )
					.transform( 'babelify', { presets: [ '@babel/preset-env' ] } )
					.bundle();
			} )
		)
		.pipe( buffer() )
		.pipe( sourcemaps.init( { loadMaps: true } ) )
		.pipe( sourcemaps.write( '.', {
			includeContent: false,
			sourceRoot: '../..'
		} ) )
		.pipe( gulp.dest( './assets/js' ) );
}

/**
 * Minify script files using UglifyJS
 * @returns {*}
 */
export function minifyScripts() {
	return gulp
		.src( './assets/js/**/*.js' )
		.pipe( plumber( { errorHandler: handleErrors } ) )
		.pipe( uglify() )
		.pipe( gulp.dest( './assets/js/' ) );
}

/**
 * Minify, concatenate, and clean SVG icons.
 *
 * https://www.npmjs.com/package/gulp-svgmin
 * https://www.npmjs.com/package/gulp-svgstore
 * https://www.npmjs.com/package/gulp-cheerio
 */
export function generateIcons() {
	return gulp
		.src( paths.svg )
		.pipe( plumber( { errorHandler: handleErrors } ) )
		.pipe( svgmin() )
		.pipe( svgstore( { inlineSvg: true } ) )
		.pipe(
			cheerio( {
				run: $ => {
					$( 'svg' ).attr( 'style', 'display:none' );
					$( '[fill]' ).removeAttr( 'fill' );
					$( 'path' ).removeAttr( 'class' );
				},
				parserOptions: { xmlMode: true }
			} )
		)
		.pipe( gulp.dest( './assets/images/' ) )
		.pipe( browserSync.stream() );
}

/**
 * Copy image from src/images to images. Tend to be used in development.
 *
 * @returns {*}
 */
export function copyImages() {
	return gulp
		.src( paths.images, { since: gulp.lastRun( copyImages ) } )
		.pipe( plumber( { errorHandler: handleErrors } ) )
		.pipe( gulp.dest( './assets/images' ) )
		.pipe( browserSync.stream() );
}

/**
 * Optimize images with imagemin.
 *
 * https://www.npmjs.com/package/gulp-imagemin
 */
export function optimizeImages() {
	return gulp
		.src( paths.images )
		.pipe( plumber( { errorHandler: handleErrors } ) )
		.pipe(
			imagemin( {
				optimizationLevel: 5,
				progressive: true,
				interlaced: true
			} )
		)
		.pipe( gulp.dest( './assets/images' ) );
}

/**
 * Concatenate images into a single PNG sprite.
 *
 * https://www.npmjs.com/package/gulp.spritesmith
 */
export function generateSprites() {
	return gulp
		.src( paths.sprites )
		.pipe( plumber( { errorHandler: handleErrors } ) )
		.pipe(
			spritesmith( {
				imgName: 'sprites.png',
				cssName: '../src/scss/base/_sprites.scss',
				algorithm: 'binary-tree'
			} )
		)
		.pipe( gulp.dest( 'assets/images/' ) )
		.pipe( browserSync.stream() );
}

/**
 * JavaScript linting.
 *
 * https://www.npmjs.com/package/gulp-eslint
 */
export function lintScripts() {
	return gulp
		.src( paths.js, { since: gulp.lastRun( lintScripts ) } )
		.pipe(
			eslint( {
				globals: [ 'jQuery', '$' ],
				envs: [ 'browser' ]
			} )
		)
		.pipe( eslint.format() );
}

/**
 * SCSS linting.
 *
 * https://www.npmjs.com/package/sass-lint
 */
export function lintStyles() {
	return gulp
		.src(
			[
				'./assets/src/**/*.scss',
				'!./assets/src/scss/base/_normalize.scss',
				'!./assets/src/scss/base/_sprites.scss'
			],
			{ since: gulp.lastRun( lintStyles ) }
		)
		.pipe(
			stylelint( {
				reporters: [ { formatter: 'string', console: true } ]
			} )
		);
}

/**
 * Scan the theme and create a POT file.
 *
 * https://www.npmjs.com/package/gulp-wp-pot
 */
export function i18n() {
	const domainName = pkg.name;
	const packageName = pkg.title;

	return gulp
		.src( paths.php )
		.pipe( plumber( { errorHandler: handleErrors } ) )
		.pipe( sort() )
		.pipe(
			wpPot( {
				domain: domainName,
				package: packageName
			} )
		)
		.pipe( gulp.dest( './languages/' + domainName + '.pot' ) );
}

/**
 * Process tasks and reload browsers on file changes.
 *
 * https://www.npmjs.com/package/browser-sync
 */
export function watch() {

	// Kick off BrowserSync.
	browserSync( {
		open: false, // Open project in a new tab?
		injectChanges: true, // Auto inject changes instead of full reload.
		proxy: 'localhost', // Use http://_s.com:3000 to use BrowserSync.
		watchOptions: {
			debounceDelay: 1000 // Wait 1 second before injecting.
		}
	} );

	// Run tasks when files change.
	gulp.watch( paths.images, copyImages );
	gulp.watch( paths.svg, generateIcons );
	gulp.watch( paths.scss, compileStyles );
	gulp.watch( paths.js, compileScripts );
	gulp.watch( paths.sprites, generateSprites );
}

/**
 * Build dist files for release
 */
export const build = gulp.parallel(
	generateIcons,
	generateSprites,
	gulp.series( compileStyles, minifyStyles ),
	gulp.series( compileScripts, minifyScripts ),
	optimizeImages,
	i18n
);

const includedFiles = [
	'./**/*',
	'!./assets/**/*.map',
	'!./assets/src/',
	'!./assets/src/**',
	'!./node_modules/',
	'!./node_modules/**',
	'!./tmp/',
	'!./tmp/**',
	'!./releases/',
	'!./releases/**',
	'!./tests/',
	'!./tests/**',
	'!./bin/',
	'!./bin/**',
	'!./composer.json',
	'!./composer.lock',
	'!./Gulpfile.babel.js',
	'!./package.json',
	'!./package-lock.json',
	'!./phpcs.xml',
	'!./wpacceptance.json',
	'!./phpunit.xml'
];

export function preparePlugin() {
	return gulp
		.src( includedFiles )
		.pipe( gulp.dest( './plugin/' ) );
}
export function cleanPlugin() {
	return del([ './plugin' ]);
}
export const prepare = gulp.series( cleanPlugin, preparePlugin );

/**
 * Copy build files to tmp folder for creating archive
 * @returns {*}
 */
export function copyBuild() {
	return gulp
		.src( includedFiles )
		.pipe( gulp.dest( `./tmp/${pkg.name}` ) );
}

/**
 * Zip the build
 * @returns {*}
 */
export function zipBuild() {
	return gulp
		.src( './tmp/**/*' )
		.pipe( zip( `${pkg.name}-${pkg.version}.zip` ) )
		.pipe( gulp.dest( './releases' ) );
}

/**
 * Delete tmp folder
 * @returns {*}
 */
export function cleanBuild() {
	return del([ './tmp' ]);
}

/**
 * Combine three tasks above to make the release.
 */
export const release = gulp.series( copyBuild, zipBuild, cleanBuild );

export function version() {
	return gulp
		.src( `./${pkg.name}.php` )
		.pipe(
			replace( /Version:(\s*?)[a-zA-Z0-9\.\-\+]+$/m, 'Version:$1' + pkg.version )
		)
		.pipe(
			replace( /@version(\s*?)[a-zA-Z0-9\.\-\+]+$/m, '@version$1' + pkg.version )
		)
		.pipe(
			replace(
				/VERSION(\s*?)=(\s*?['"])[a-zA-Z0-9\.\-\+]+/gm,
				'VERSION$1=$2' + pkg.version
			)
		)
		.pipe( gulp.dest( './' ) );
}

export default gulp.parallel(
	generateIcons,
	generateSprites,
	copyImages,
	i18n,
	compileStyles,
	compileScripts
);
