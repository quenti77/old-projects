// External require
let gulp = require('gulp')

let autoprefixer = require('gulp-autoprefixer')
let babel = require('gulp-babel')
let concat = require('gulp-concat')
let sass = require('gulp-sass')
let size = require('gulp-size')
let uglify = require('gulp-uglify')

// Options
let config = {
    path: {
        dist: 'public/dist',
        style: 'src/css/**/*.scss',
        script: 'src/js/*.js'
    }
}

gulp.task('sass', () => {
    return gulp
        .src(config.path.style)
        .pipe(sass({
            outputStyle: 'compressed',
            errLogToConsole: true
        }).on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['last 4 versions']
        }))
        .pipe(gulp.dest(config.path.dist))
        .pipe(size())
})

gulp.task('js', () => {
    return gulp
        .src(config.path.script)
        .pipe(babel({
            presets: ['es2015']
        }))
        .pipe(concat('main.js'))
        .pipe(uglify())
        .pipe(gulp.dest(config.path.dist))
        .pipe(size())
})

gulp.task('watchSass', () => {
    return gulp.watch(config.path.style, ['sass'])
})

gulp.task('watchJS', () => {
    return gulp.watch(config.path.script, ['js'])
})

gulp.task('default', ['watchSass', 'watchJS'])
