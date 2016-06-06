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
    clean.taogroupsbundle = [out];
    
    /**
     * Compile tao files into a bundle 
     */
    requirejs.taogroupsbundle = {
        options: {
            baseUrl : '../js',
            dir : out,
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'taoGroups' : root + '/taoGroups/views/js' },
            modules : [{
                name: 'taoGroups/controller/routes',
                include : ext.getExtensionsControllers(['taoGroups']),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taogroupsbundle = {
        files: [
            { src: [out + '/taoGroups/controller/routes.js'],  dest: root + '/taoGroups/views/js/controllers.min.js' },
            { src: [out + '/taoGroups/controller/routes.js.map'],  dest: root + '/taoGroups/views/js/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taogroupsbundle', ['clean:taogroupsbundle', 'requirejs:taogroupsbundle', 'copy:taogroupsbundle']);
};
