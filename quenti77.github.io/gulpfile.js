var gulp = require('gulp');
var plug = require('gulp-load-plugins')();

var pathOptions = {
    css: 'styles/dist/',
    scss: 'styles/sass/**/*.scss'
};

var sftpOptions = {
    host: 'local.dev',
    user: 'dev',
    pass: 'dev',
    remotePath: '/home/dev/www/tchat/styles/dist/'
};

gulp.task('sass', function () {
    return gulp
        .src(pathOptions.scss)
        .pipe(plug.sass({
            outputStyle: 'compressed',
            errLogToConsole: true
        }).on('error', plug.sass.logError))
        .pipe(plug.autoprefixer({
            browsers: ['last 3 versions']
        }))
        .pipe(gulp.dest(pathOptions.css))
        .pipe(plug.size());
});

gulp.task('watchScss', function () {
    return gulp.watch(pathOptions.scss, ['sass']);
});

//gulp.task('default', ['watchScss', 'watchSftp']);
gulp.task('default', ['watchScss']);
