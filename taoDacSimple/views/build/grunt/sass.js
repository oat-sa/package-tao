module.exports = function(grunt) { 

    var sass    = grunt.config('sass') || {};
    var watch   = grunt.config('watch') || {};
    var notify  = grunt.config('notify') || {};
    var root    = grunt.option('root') + '/taoDacSimple/views/';

    sass.taodacsimple = { };
    sass.taodacsimple.files = { };
    sass.taodacsimple.files[root + 'css/admin.css'] = root + 'scss/admin.scss';

    watch.taodacsimplesass = {
        files : [root + 'views/scss/**/*.scss'],
        tasks : ['sass:taodacsimple', 'notify:taodacsimplesass'],
        options : {
            debounceDelay : 1000
        }
    };

    notify.taodacsimplesass = {
        options: {
            title: 'Grunt SASS', 
            message: 'SASS files compiled to CSS'
        }
    };

    grunt.config('sass', sass);
    grunt.config('watch', watch);
    grunt.config('notify', notify);

    //register an alias for main build
    grunt.registerTask('taodacsimplesass', ['sass:taodacsimple']);
};
