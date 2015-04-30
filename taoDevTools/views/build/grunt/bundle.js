module.exports = function(grunt) { 

    var requirejs   = grunt.config('requirejs') || {};
    var clean       = grunt.config('clean') || {};
    var copy        = grunt.config('copy') || {};

    var root        = grunt.option('root');
    var libs        = grunt.option('mainlibs');
    var ext         = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);
    var out         = 'output';

    /**
     * Remove bundled and bundling files
     */
    clean.taodevtoolsbundle = [out];
    
    /**
     * Compile tao files into a bundle 
     */
    requirejs.taodevtoolsbundle = {
        options: {
            baseUrl : '../js',
            dir : out,
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'taoDevTools' : root + '/taoDevTools/views/js' },
            modules : [{
                name: 'taoDevTools/controller/routes',
                include : ext.getExtensionsControllers(['taoDevTools']),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taodevtoolsbundle = {
        files: [
            { src: [out + '/taoDevTools/controller/routes.js'],  dest: root + '/taoDevTools/views/js/controllers.min.js' },
            { src: [out + '/taoDevTools/controller/routes.js.map'],  dest: root + '/taoDevTools/views/js/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taodevtoolsbundle', ['clean:taodevtoolsbundle', 'requirejs:taodevtoolsbundle', 'copy:taodevtoolsbundle']);
};
