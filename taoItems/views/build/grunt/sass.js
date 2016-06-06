module.exports = function(grunt) {
    'use strict';

    var sass    = grunt.config('sass') || {};
    var watch   = grunt.config('watch') || {};
    var notify  = grunt.config('notify') || {};
    var root    = grunt.option('root') + '/taoItems/views/';

    sass.taoitems = { };
    sass.taoitems.files = { };
    sass.taoitems.files[root + 'css/preview.css'] = root + 'scss/preview.scss';

    watch.taoitemssass = {
        files : [root + 'scss/**/*.scss'],
        tasks : ['sass:taoitems', 'notify:taoitemssass'],
        options : {
            debounceDelay : 1000
        }
    };

    notify.taoitemssass = {
        options: {
            title: 'Grunt SASS',
            message: 'SASS files compiled to CSS'
        }
    };

    grunt.config('sass', sass);
    grunt.config('watch', watch);
    grunt.config('notify', notify);

    //register an alias for main build
    grunt.registerTask('taoitemssass', ['sass:taoitems']);
};
