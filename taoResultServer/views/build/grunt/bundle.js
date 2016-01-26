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
    clean.taoresultserverbundle = [out];
    
    /**
     * Compile tao files into a bundle 
     */
    requirejs.taoresultserverbundle = {
        options: {
            baseUrl : '../js',
            dir : out,
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'taoResultServer' : root + '/taoResultServer/views/js' },
            modules : [{
                name: 'taoResultServer/controller/routes',
                include : ext.getExtensionsControllers(['taoResultServer']),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taoresultserverbundle = {
        files: [
            { src: [out + '/taoResultServer/controller/routes.js'],  dest: root + '/taoResultServer/views/js/controllers.min.js' },
            { src: [out + '/taoResultServer/controller/routes.js.map'],  dest: root + '/taoResultServer/views/js/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taoresultserverbundle', ['clean:taoresultserverbundle', 'requirejs:taoresultserverbundle', 'copy:taoresultserverbundle']);
};
