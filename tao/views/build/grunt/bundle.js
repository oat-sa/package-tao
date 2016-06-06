module.exports = function(grunt) {

    var requirejs   = grunt.config('requirejs') || {};
    var clean       = grunt.config('clean') || {};
    var copy        = grunt.config('copy') || {};

    var root        = grunt.option('root');
    var libs        = grunt.option('mainlibs');
    var ext         = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);
    var out         = 'output';

    /**
     * General options
     */
    requirejs.options = {
        optimize: 'uglify2',
        uglify2: {
            mangle : false,
            output: {
                'max_line_len': 666
            }
        },
        //optimize : 'none',
        preserveLicenseComments: false,
        optimizeAllPluginResources: true,
        findNestedDependencies : true,
        skipDirOptimize: true,
        optimizeCss : 'none',
        buildCss : false,
        inlineText: true,
        skipPragmas : true,
        generateSourceMaps : true,
        removeCombined : true
   };

    clean.options =  {
        force : true
    };

    copy.options = {
        process: function (content, srcpath) {
            //because we change the bundle names during copy
            if(/routes\.js$/.test(srcpath)){
                return content.replace('routes.js.map', 'controllers.min.js.map');
            }
            return content;
        }
    };

    grunt.log.verbose.writeln('libs');
    grunt.log.verbose.writeln(libs);

    /**
     * Remove bundled and bundling files
     */
    clean.taobundle = [out];

    /**
     * Compile tao files into a bundle
     */
    requirejs.taobundle = {
        options: {
            baseUrl : '../js',
            dir : out,
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'tao' : '.' },
            modules : [{
                name: 'main',
                include: ['lib/require'],
                deps : libs,
                exclude : ['json!i18ntr/messages.json',  'mathJax', 'mediaElement'],
            }, {
                name: 'controller/routes',
                include : ext.getExtensionsControllers(['tao']),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taobundle = {
        files: [
            { src: [out + '/main.js'],                  dest: '../js/main.min.js' },
            { src: [out + '/main.js.map'],              dest: '../js/main.min.js.map' },
            { src: [out + '/controller/routes.js'],     dest: '../js/controllers.min.js' },
            { src: [out + '/controller/routes.js.map'], dest: '../js/controllers.min.js.map' }
        ],
        options : {
            process: function (content, srcpath) {
                //because we change the bundle names during copy
                if(/main\.js$/.test(srcpath)){
                    return content.replace('main.js.map', 'main.min.js.map');
                }
                if(/routes\.js$/.test(srcpath)){
                    return content.replace('routes.js.map', 'controllers.min.js.map');
                }

                return content;
            }
        }
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taobundle', ['clean:taobundle', 'requirejs:taobundle', 'copy:taobundle']);
};
