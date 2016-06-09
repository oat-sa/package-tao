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
    clean.taorevisionbundle = [out];
    
    /**
     * Compile tao files into a bundle 
     */
    requirejs.taorevisionbundle = {
        options: {
            baseUrl : '../js',
            dir : out,
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'taoRevision' : root + '/taoRevision/views/js' },
            modules : [{
                name: 'taoRevision/controller/routes',
                include : ext.getExtensionsControllers(['taoRevision']),
                exclude : ['mathJax'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taorevisionbundle = {
        files: [
            { src: [out + '/taoRevision/controller/routes.js'],  dest: root + '/taoRevision/views/js/controllers.min.js' },
            { src: [out + '/taoRevision/controller/routes.js.map'],  dest: root + '/taoRevision/views/js/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taorevisionbundle', ['clean:taorevisionbundle', 'requirejs:taorevisionbundle', 'copy:taorevisionbundle']);
};
