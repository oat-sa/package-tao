module.exports = function(grunt) { 

    var sass    = grunt.config('sass') || {};
    var watch   = grunt.config('watch') || {};
    var notify  = grunt.config('notify') || {};
    var root    = grunt.option('root') + '/taoMediaManager/views/';

    sass.taomediamanager = { };
    sass.taomediamanager.files = { };
    sass.taomediamanager.files[root + 'css/media.css'] = root + 'scss/media.scss';

    watch.taomediamanagersass = {
        files : [root + 'views/scss/**/*.scss'],
        tasks : ['sass:taomediamanager', 'notify:taomediamanagersass'],
        options : {
            debounceDelay : 1000
        }
    };

    notify.taomediamanagersass = {
        options: {
            title: 'Grunt SASS', 
            message: 'SASS files compiled to CSS'
        }
    };

    grunt.config('sass', sass);
    grunt.config('watch', watch);
    grunt.config('notify', notify);

    //register an alias for main build
    grunt.registerTask('taomediamanagersass', ['sass:taomediamanager']);
};
