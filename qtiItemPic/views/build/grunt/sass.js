module.exports = function(grunt) { 

    var sass    = grunt.config('sass') || {};
    var watch   = grunt.config('watch') || {};
    var notify  = grunt.config('notify') || {};
    var root    = grunt.option('root') + '/qtiItemPic/views/';

    sass.qtiitempic = {
        options : {
            loadPath : ['../scss/', root + 'scss/inc']
        },
        files : {}        
    };
    sass.qtiitempic.files[root + 'css/pic-manager.css'] = root + 'scss/pic-manager.scss';

    watch.qtiitempicsass = {
        files : [root + 'scss/**/*.scss'],
        tasks : ['sass:qtiitempic', 'notify:qtiitempicsass'],
        options : {
            debounceDelay : 1000
        }
    };

    notify.qtiitempicsass = {
        options: {
            title: 'Grunt SASS', 
            message: 'SASS files compiled to CSS'
        }
    };

    grunt.config('sass', sass);
    grunt.config('watch', watch);
    grunt.config('notify', notify);

    grunt.registerTask('qtiitempicsass', ['sass:qtiitempic']);
};
