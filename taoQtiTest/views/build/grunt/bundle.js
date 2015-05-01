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
    clean.taoqtitestbundle = [out];
    
    /**
     * Compile tao files into a bundle 
     */
    requirejs.taoqtitestbundle = {
        options: {
            baseUrl : '../js',
            dir : out,
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'taoQtiTest' : root + '/taoQtiTest/views/js', 'taoQtiItem' : root + '/taoQtiItem/views/js' },
            modules : [{
                name: 'taoQtiTest/controller/routes',
                include : ext.getExtensionsControllers(['taoQtiTest']),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taoqtitestbundle = {
        files: [
            { src: [out + '/taoQtiTest/controller/routes.js'],  dest: root + '/taoQtiTest/views/js/controllers.min.js' },
            { src: [out + '/taoQtiTest/controller/routes.js.map'],  dest: root + '/taoQtiTest/views/js/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taoqtitestbundle', ['clean:taoqtitestbundle', 'requirejs:taoqtitestbundle', 'copy:taoqtitestbundle']);
};
