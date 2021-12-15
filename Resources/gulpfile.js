const gulp = require('gulp');
const babel = require('gulp-babel');
const rename = require('gulp-rename');
const uglify = require('gulp-uglify');
const wrap = require('gulp-wrap');

gulp.task('js', function() {
    return gulp.src('js/router.ts')
        .pipe(babel({
            presets: ["@babel/preset-env", "@babel/preset-typescript"],
            plugins: ["@babel/plugin-transform-object-assign"]
        }))
        .pipe(wrap({ src: 'js/router.template.js' }))
        .pipe(gulp.dest('public/js'))
        .pipe(rename({ extname: '.min.js' }))
        .pipe(uglify())
        .pipe(gulp.dest('public/js'));
});

gulp.task('default', gulp.series('js'));
