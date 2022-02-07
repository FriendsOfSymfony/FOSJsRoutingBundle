const gulp = require('gulp');
const rename = require('gulp-rename');
const uglify = require('gulp-uglify');
const wrap = require('gulp-wrap');
const ts = require('gulp-typescript')

gulp.task('ts', function() {
    return gulp.src('js/router.ts')
        .pipe(ts({
            noImplicitAny: true,
        }))
        .pipe(gulp.dest('public/js'));
});

gulp.task('min', function() {
    return gulp.src('public/js/!(*.min).js')
        .pipe(wrap({ src: 'js/router.template.js' }))
        .pipe(gulp.dest('public/js'))
        .pipe(rename({ extname: '.min.js' }))
        .pipe(uglify())
        .pipe(gulp.dest('public/js'));
});

gulp.task('default', gulp.series('ts', 'min'));
