module.exports = function(grunt) {
    'use strict';

    var requirejs   = grunt.config('requirejs') || {};
    var clean       = grunt.config('clean') || {};
    var copy        = grunt.config('copy') || {};

    var root        = grunt.option('root');
    var libs        = grunt.option('mainlibs');
    var ext         = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);
    var out         = 'output';

    var paths = {
        'taoTests':      root + '/taoTests/views/js',
        'taoQtiTest':    root + '/taoQtiTest/views/js',
        'taoQtiTestCss': root + '/taoQtiTest/views/css',
        'taoQtiItem'    : root + '/taoQtiItem/views/js',
        'taoQtiItemCss' : root + '/taoQtiItem/views/css',
        'taoItems'      : root + '/taoItems/views/js',
        'qtiCustomInteractionContext' : root + '/taoQtiItem/views/js/runtime/qtiCustomInteractionContext',
        'qtiInfoControlContext' : root + '/taoQtiItem/views/js/runtime/qtiInfoControlContext',
    };

    var runtimeLibsPattern  = ['views/js/qtiItem/core/**/*.js', 'views/js/qtiCommonRenderer/renderers/**/*.js',  'views/js/qtiCommonRenderer/helpers/**/*.js'];
    var runtimeLibs         = ext.getExtensionSources('taoQtiItem', runtimeLibsPattern, true);

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
            paths : paths,
            modules : [{
                name: 'taoQtiTest/controller/routes',
                include : ext.getExtensionsControllers(['taoQtiTest']),
                exclude : ['mathJax', 'mediaElement', 'taoQtiTest/controller/runner/runner'].concat(libs)
            }]
        }
    };

    /**
     * Compile the new item runner as a standalone library
     */
    requirejs.qtitestrunner = {
        options: {
            baseUrl : '../js',
            mainConfigFile : './config/requirejs.build.js',
            findNestedDependencies : true,
            paths : paths,
            include: runtimeLibs.concat([ 'tpl', 'json']),
            excludeShallow : ['mathJax', 'ckeditor'],
            exclude : ['json!i18ntr/messages.json'],
            name: "taoQtiTest/controller/runner/runner",
            out: out + "/qtiTestRunner.min.js"
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taoqtitestbundle = {
        files: [
            { src: [out + '/taoQtiTest/controller/routes.js'],  dest: root + '/taoQtiTest/views/js/controllers.min.js' },
            { src: [out + '/taoQtiTest/controller/routes.js.map'],  dest: root + '/taoQtiTest/views/js/controllers.min.js.map' },
            { src: [out + '/qtiTestRunner.min.js'],  dest: root + '/taoQtiTest/views/js/qtiTestRunner.min.js' },
            { src: [out + '/qtiTestRunner.min.js.map'],  dest: root + '/taoQtiTest/views/js/qtiTestRunner.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taoqtitestbundle', ['clean:taoqtitestbundle', 'requirejs:taoqtitestbundle', 'requirejs:qtitestrunner', 'copy:taoqtitestbundle']);
};
