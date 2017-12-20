const gulp = require('gulp');
const babel = require('gulp-babel');
const rename = require('gulp-rename');
const uglify = require('gulp-uglify');
const wrap = require('gulp-wrap');

gulp.task('js', function() {
    return gulp.src('js/router.js')
        .pipe(babel())
        .pipe(wrap({ src: 'js/router.template.js' }))
        .pipe(gulp.dest('public/js'))
        .pipe(rename((path) => {
            path.extname = '.min.js';
        }))
        .pipe(uglify())
        .pipe(gulp.dest('public/js'));
});

gulp.task('default', function() {
    return gulp.start(['js']);
});
