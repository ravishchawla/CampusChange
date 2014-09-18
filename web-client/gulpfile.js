var gulp = require('gulp');

var connect = require('gulp-connect');
var jshint = require('gulp-jshint');

var del = require('del');

gulp.task('clean', function (cb) {
	del('dist', cb);
});

gulp.task('lint', function () {
	gulp.src('./app/**/*.js', '!./app/bower**')
});

gulp.task('build', ['lint'], function () {
	gulp.src(['./app/bower/**/*.js', './app/bower/**/*.css'])
	.pipe(gulp.dest('dist/bower'));

	gulp.src(['./app/*.html'])
	.pipe(gulp.dest('dist'));
	
	gulp.src(['./app/css/*.css'])
	.pipe(gulp.dest('dist/css'));
	
	gulp.src('./app/img/**')
	.pipe(gulp.dest('dist/img'));
});

gulp.task('debug', function (cb) {
	connect.server({
		root: 'app/',
		port: 8000
	});
});

gulp.task('release', ['build'], function (cb) {
	connect.server({
		root: 'dist/',
		port: 8000
	});
})