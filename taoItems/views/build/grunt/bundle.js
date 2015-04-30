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
    clean.taoitemsbundle = [out];

    /**
     * Compile tao files into a bundle
     */
    requirejs.taoitemsbundle = {
        options: {
            baseUrl : '../js',
            dir : out,
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'taoItems' : root + '/taoItems/views/js' },
            modules : [{
                name: 'taoItems/controller/routes',
                include : ext.getExtensionsControllers(['taoItems']),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taoitemsbundle = {
        files: [
            { src: [out + '/taoItems/controller/routes.js'],  dest: root + '/taoItems/views/js/controllers.min.js' },
            { src: [out + '/taoItems/controller/routes.js.map'],  dest: root + '/taoItems/views/js/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taoitemsbundle', ['clean:taoitemsbundle', 'requirejs:taoitemsbundle', 'copy:taoitemsbundle']);
};
