module.exports = function(grunt) {

    var requirejs   = grunt.config('requirejs') || {};
    var clean       = grunt.config('clean') || {};
    var copy        = grunt.config('copy') || {};
    var replace     = grunt.config('replace') || {};
    var uglify      = grunt.config('uglify') || {};

    var root        = grunt.option('root');
    var libs        = grunt.option('mainlibs');
    var ext         = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);
    var out         = 'output';

    /**
     * Resolve AMD modules in the current extension
     */
    var creatorLibsPattern  = ['views/js/qtiCreator/**/*.js', 'views/js/qtiXmlRenderer/renderers/**/*.js', '!views/js/qtiCreator/test/**/*.js'];
    var creatorLibs         = ext.getExtensionSources('taoQtiItem', creatorLibsPattern, true);

    var runtimeLibsPattern  = ['views/js/qtiItem/core/**/*.js', 'views/js/qtiCommonRenderer/renderers/**/*.js',  'views/js/qtiCommonRenderer/helpers/**/*.js'];
    var runtimeLibs         = ext.getExtensionSources('taoQtiItem', runtimeLibsPattern, true);

    var paths = {
        'taoQtiItem' : root + '/taoQtiItem/views/js',
        'taoQtiItemCss' :  root + '/taoQtiItem/views/css',
        'taoItems' : root + '/taoItems/views/js',
        'qtiCustomInteractionContext' : root + '/taoQtiItem/views/js/runtime/qtiCustomInteractionContext',
        'qtiInfoControlContext' : root + '/taoQtiItem/views/js/runtime/qtiInfoControlContext',
    };

    /**
     * Remove bundled and bundling files
     */
    clean.taoqtiitembundle = [out];

    /**
     * Controller
     */
    requirejs.taoqtiitembundle = {
        options: {
            baseUrl : '../js',
            dir : out,
            mainConfigFile : './config/requirejs.build.js',
            paths : paths,
            modules : [{
                name: 'taoQtiItem/controller/routes',
                include : ext.getExtensionsControllers(['taoQtiItem']).concat(creatorLibs),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }]
        }
    };

    /**
     * Compile the qti runtime
     */
    requirejs.qtiruntime = {
        options: {
            baseUrl : '../js',
            dir: out,
            mainConfigFile : './config/requirejs.build.js',
            paths : paths,
            modules : [{
                name: 'taoQtiItem/runtime/qtiBootstrap',
                include: runtimeLibs,
                exclude : ['json!i18ntr/messages.json', 'mathJax', 'mediaElement', 'ckeditor'],
            }]
        }
    };

    /**
     * Compile the new item runner as a standalone library
     */
    requirejs.qtinewrunner = {
        options: {
            baseUrl : '../js',
            mainConfigFile : './config/requirejs.build.js',
            findNestedDependencies : true,
            uglify2: {
                mangle : false,
                output: {
                    'max_line_len': 400
                }
            },
            wrap : {
                start : '',
                end : "define(['taoQtiItem/runner/qtiItemRunner'], function(runner){ return runner; });"
            },
            wrapShim: true,
            inlineCss : true,
            paths : {
                'taoQtiItem'    : root + '/taoQtiItem/views/js',
                'taoQtiItemCss' : root + '/taoQtiItem/views/css',
                'taoItems'      : root + '/taoItems/views/js',
                'taoCss'        : root + '/tao/views/css',
                'jquery'        : 'lib/jqueryamd-1.8.3',
                'taoQtiItemCss/qti' : root + '/taoQtiItem/views/css/qti-runner',
                'qtiCustomInteractionContext' : root + '/taoQtiItem/views/js/runtime/qtiCustomInteractionContext',
                'qtiInfoControlContext' : root + '/taoQtiItem/views/js/runtime/qtiInfoControlContext',
            },
            excludeShallow : ['mathJax', 'ckeditor'],
            include: runtimeLibs.concat([ 'tpl', 'json']),
            name: "taoQtiItem/runner/qtiItemRunner",
            out: out + "/qtiItemRunner.min.js"
        }
    };

    /**
     * Compile the new item runner as a standalone library
     */
    requirejs.qtiscorer = {
        options: {
            baseUrl : '../js',
            mainConfigFile : './config/requirejs.build.js',
            //optimize: "none",
            findNestedDependencies : true,
            uglify2: {
                mangle : false,
                output: {
                    'max_line_len': 400
                }
            },
            wrap : {
                start : '',
                end : "define(['taoQtiItem/scoring/qtiScorer'], function(scorer){ return scorer; });"
            },
            wrapShim: true,
            paths : paths,
            include: ['lodash'],
            name: "taoQtiItem/scoring/qtiScorer",
            out: out + "/qtiScorer.min.js"
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taoqtiitembundle = {
        files: [
            { src: [ out + '/taoQtiItem/controller/routes.js'],  dest: root + '/taoQtiItem/views/js/controllers.min.js' },
            { src: [ out + '/taoQtiItem/controller/routes.js.map'],  dest: root + '/taoQtiItem/views/js/controllers.min.js.map' }
        ]
    };

    /**
     * copy the bundles to the right place
     */
    copy.qtiruntime = {
        files: [
            { src: [ out + '/taoQtiItem/runtime/qtiBootstrap.js.map'],  dest: root + '/taoQtiItem/views/js/runtime/qtiBootstrap.min.js.map' }
        ]
    };

    //the qti loader is uglify outside the r.js to split the file loading (qtiLoader.min published within the item and qtiBootstrap shared)
    uglify.qtiruntime = {
        options : {
            force : true
        },
        files : [
            { dest : out + '/qtiLoader.min.js', src : ['../js/lib/require.js', root + '/taoQtiItem/views/js/runtime/qtiLoader.js'] }
        ]
    };

    //we need to change the names of AMD modules to referr to minimified verrsions
    replace.qtiruntime = {
         options: {
             patterns: [{
                match : 'qtiBootstrap',
                replacement:  'qtiBootstrap.min',
                expression: false
             }],
             force : true,
             prefix: ''
         },
         files : [
             { src: [ out + '/taoQtiItem/runtime/qtiBootstrap.js'],  dest: root + '/taoQtiItem/views/js/runtime/qtiBootstrap.min.js' },
             { src: [ out + '/qtiLoader.min.js'],  dest: root + '/taoQtiItem/views/js/runtime/qtiLoader.min.js' }
         ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);
    grunt.config('uglify', uglify);
    grunt.config('replace', replace);

    // bundle task
    grunt.registerTask('qtiruntime', ['clean:taoqtiitembundle', 'requirejs:qtiruntime', 'uglify:qtiruntime', 'replace:qtiruntime', 'copy:qtiruntime']);
    grunt.registerTask('taoqtiitembundle', ['clean:taoqtiitembundle', 'requirejs:taoqtiitembundle', 'copy:taoqtiitembundle', 'clean:taoqtiitembundle', 'requirejs:qtiruntime', 'uglify:qtiruntime', 'replace:qtiruntime', 'copy:qtiruntime']);

};
