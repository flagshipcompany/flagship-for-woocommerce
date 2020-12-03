var gulp = require('gulp'),  
    util = require('util'),
    exec = require('child_process').exec;

gulp.task('phpunit', function() {
    exec('phpunit', function(error, stdout) {
        util.log(stdout); 
    });
}); 

gulp.task('default', function() {
    gulp.watch('tests/**/*.php', { debounceDelay: 2000 }, ['phpunit']);   
});