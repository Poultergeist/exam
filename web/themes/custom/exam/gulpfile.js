const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const sourcemaps = require('gulp-sourcemaps');

const paths = {
    scss: './scss/**/*.scss',
    css: './css',
};

gulp.task('sass', function () {
    return gulp.src(paths.scss)
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest(paths.css));
});

gulp.task('watch', function () {
    gulp.watch(paths.scss, gulp.series('sass'));
});

gulp.task('default', gulp.series('sass', 'watch'));
