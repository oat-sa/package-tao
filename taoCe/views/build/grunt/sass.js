module.exports = function(grunt) { 

    var sass    = grunt.config('sass') || {};
    var watch   = grunt.config('watch') || {};
    var notify  = grunt.config('notify') || {};
    var root    = grunt.option('root') + '/taoCe/views/';

    sass.taoce = { };
    sass.taoce.files = { };
    sass.taoce.files[root + 'css/home.css'] = root + 'scss/home.scss';

    watch.taocesass = {
        files : [root + 'views/scss/**/*.scss'],
        tasks : ['sass:taoce', 'notify:taocesass'],
        options : {
            debounceDelay : 1000
        }
    };

    notify.taocesass = {
        options: {
            title: 'Grunt SASS', 
            message: 'SASS files compiled to CSS'
        }
    };

    grunt.config('sass', sass);
    grunt.config('watch', watch);
    grunt.config('notify', notify);

    //register an alias for main build
    grunt.registerTask('taocesass', ['sass:taoce']);
};
