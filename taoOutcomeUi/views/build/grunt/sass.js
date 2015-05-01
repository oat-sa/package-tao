module.exports = function(grunt) { 

    var sass    = grunt.config('sass') || {};
    var watch   = grunt.config('watch') || {};
    var notify  = grunt.config('notify') || {};
    var root    = grunt.option('root') + '/taoOutcomeUi/views/';

    //override load path
    sass.taooutcomeui = {
        options : {
            loadPath : ['../scss/']
        },
        files : {}        
    };

    //files goes heres
    sass.taooutcomeui.files[root + 'css/icon.css'] = root + 'scss/icon.scss';


    watch.taooutcomeuisass = {
        files : [root + 'scss/**/*.scss'],
        tasks : ['sass:taoqtiitem', 'notify:taoqtiitemsass'],
        options : {
            debounceDelay : 1000
        }
    };

    notify.taooutcomeuisass = {
        options: {
            title: 'Grunt SASS', 
            message: 'SASS files compiled to CSS'
        }
    };

    grunt.config('sass', sass);
    grunt.config('watch', watch);
    grunt.config('notify', notify);
    
    //register an alias for main build
    grunt.registerTask('taooutcomeuisass', ['sass:taooutcomeui']);
};
