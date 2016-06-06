module.exports = function(grunt) { 

    var sass    = grunt.config('sass') || {};
    var watch   = grunt.config('watch') || {};
    var notify  = grunt.config('notify') || {};
    var root    = grunt.option('root') + '/taoRevision/views/';

    sass.taorevision = { };
    sass.taorevision.files = { };
    sass.taorevision.files[root + 'css/revision.css'] = root + 'scss/revision.scss';

    watch.taorevisionsass = {
        files : [root + 'views/scss/**/*.scss'],
        tasks : ['sass:taorevision', 'notify:taorevisionsass'],
        options : {
            debounceDelay : 1000
        }
    };

    notify.taorevisionsass = {
        options: {
            title: 'Grunt SASS', 
            message: 'SASS files compiled to CSS'
        }
    };

    grunt.config('sass', sass);
    grunt.config('watch', watch);
    grunt.config('notify', notify);

    //register an alias for main build
    grunt.registerTask('taorevisionsass', ['sass:taorevision']);
};
