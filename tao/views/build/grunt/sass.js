module.exports = function(grunt) {

    var sass    = grunt.config('sass') || {};
    var watch   = grunt.config('watch') || {};
    var notify  = grunt.config('notify') || {};

    sass.options = {
        noCache: true,
        unixNewlines : true,
        loadPath : ['../scss/', '../js/lib/'],
        lineNumbers : false,
        style : 'compressed'
    };

    sass.tao = {
        files : {
            '../css/tao-main-style.css' : '../scss/tao-main-style.scss',
            '../css/tao-3.css' : '../scss/tao-3.scss',
            '../css/layout.css' : '../scss/layout.scss',
            '../js/lib/jsTree/themes/css/style.css' : '../js/lib/jsTree/themes/scss/style.scss',
        }
    };

    sass.ckeditor = {
        files : {
            '../js/lib/ckeditor/skins/tao/editor.css' : '../js/lib/ckeditor/skins/tao/scss/editor.scss',
            '../js/lib/ckeditor/skins/tao/dialog.css' : '../js/lib/ckeditor/skins/tao/scss/dialog.scss',
        }
    };

    watch.taosass = {
        files : ['../scss/*.scss', '../scss/**/*.scss', '../js/lib/jsTree/**/*.scss'],
        tasks : ['sass:tao', 'notify:taosass'],
        options : {
            debounceDelay : 1000
        }
    };

    notify.taosass = {
        options: {
            title: 'Grunt SASS',
            message: 'SASS files compiled to CSS'
        }
    };

    grunt.config('sass', sass);
    grunt.config('watch', watch);
    grunt.config('notify', notify);

    //register an alias for main build
    grunt.registerTask('taosass', ['sass:tao']);
};
