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
    clean.taomediamanagerbundle = [out];
    
    /**
     * Compile tao files into a bundle 
     */
    requirejs.taomediamanagerbundle = {
        options: {
            baseUrl : '../js',
            dir : out,
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'taoMediaManager' : root + '/taoMediaManager/views/js' },
            modules : [{
                name: 'taoMediaManager/controller/routes',
                include : ext.getExtensionsControllers(['taoMediaManager']),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taomediamanagerbundle = {
        files: [
            { src: [out + '/taoMediaManager/controller/routes.js'],  dest: root + '/taoMediaManager/views/js/controllers.min.js' },
            { src: [out + '/taoMediaManager/controller/routes.js.map'],  dest: root + '/taoMediaManager/views/js/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taomediamanagerbundle', ['clean:taomediamanagerbundle', 'requirejs:taomediamanagerbundle', 'copy:taomediamanagerbundle']);
};
