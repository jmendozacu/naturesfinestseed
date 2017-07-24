// Include main components
var gulp = require('gulp'),
    gutil = require('gulp-util'),
    lr = require('tiny-lr'),
    replace = require('gulp-replace');

// Include CSS components
var sass = require('gulp-sass'),
    prefixer = require('gulp-autoprefixer'),
    minifycss = require('gulp-minify-css');

// Include JS components
var include = require('gulp-include'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify');

// Include utilities
var rename = require('gulp-rename'),
    livereload = require('gulp-livereload'),
    server = lr();

// Source and Target directories
var sourceSASS = 'src/scss',
    targetCSS = 'css';

var sourceJS = 'src/js',
    targetJS = 'js';

// SASS compilation
gulp.task('styles', function(){
  gulp.src(sourceSASS + '/styles.scss')
    .pipe(sass().on('error', gutil.log))
    .pipe(prefixer('last 10 versions'))
    .pipe(minifycss({ keepSpecialComments: 1 }))
    .pipe(gulp.dest(targetCSS))
    .pipe(livereload(server));
});

// JS compilation
gulp.task('js', function(){
  gulp.src(sourceJS + '/app.js')
    .pipe(include())
    .pipe(concat('scripts-min.js'))
    .pipe(uglify({mangle:true}).on('error', gutil.log))
    .pipe(gulp.dest(targetJS))
    .pipe(livereload(server));
});

// LiveReload
gulp.task('livereload', function(next){
  server.listen(function(err){
    if(err) return console.error(err);
    next();
  });
});

// Watch for Sass and JS changes and run the respective compilers automatically
gulp.task('watch', function(){
  gulp.watch(sourceSASS + '**/*.scss', ['styles']);
  gulp.watch(sourceJS + '/app.js', ['js']);
})

// Default Task
gulp.task('default', ['livereload', 'styles', 'js', 'watch']);