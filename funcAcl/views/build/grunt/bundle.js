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
    clean.funcaclbundle = [out];

    /**
     * Compile tao files into a bundle
     */
    requirejs.funcaclbundle = {
        options: {
            baseUrl : '../js',
            dir : out,
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'funcAcl' : root + '/funcAcl/views/js' },
            modules : [{
                name: 'funcAcl/controller/routes',
                include : ext.getExtensionsControllers(['funcAcl']),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.funcaclbundle = {
        files: [
            { src: [ out + '/funcAcl/controller/routes.js'],  dest: root + '/funcAcl/views/js/controllers.min.js' },
            { src: [ out + '/funcAcl/controller/routes.js.map'],  dest: root + '/funcAcl/views/js/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('funcaclbundle', ['clean:funcaclbundle', 'requirejs:funcaclbundle', 'copy:funcaclbundle']);
};
