module.exports = function(grunt) {
    'use strict';

    /*
     * IMPORTANT : This file is just the launcher, each task is defined in extension/views/build/grunt/
     * for example the SASS task is defined in tao/views/build/grunt/sass.js for the main behavior and in taoQtiItem/views/build/grunt/sass.js for extension specific configuration.
     *
     */

    //track build time
    require('time-grunt')(grunt);

    // load all grunt tasks matching the `grunt-*` pattern
    require('load-grunt-tasks')(grunt);

    //set up contextual config
    var root                = require('path').resolve('../../../');                 //tao dist root
    var ext                 = require('./tasks/helpers/extensions')(grunt, root);   //extension helper
    var currentExtension    = grunt.option('extension') || 'tao';                   //target extension, add "--extension name" to CLI if needed
    var reportOutput        = grunt.option('reports') || 'reports';                 //where reports are saved
    var testPort            = grunt.option('testPort') || 8082;                     //the port to run test web server, override with "--testPort value" to CLI if needed

    //make the options avialable in sub tasks definitions
    grunt.option('root', root);
    grunt.option('currentExtension', currentExtension);
    grunt.option('testPort', testPort);


    //Resolve some shared AMD modules
    var libsPattern =  ['views/js/*.js', 'views/js/core/**/*.js', 'views/js/ui/**/*.js', 'views/js/layout/**/*.js', 'views/js/util/**/*.js', '!views/js/main.*', '!views/js/*.min*', '!views/js/test/**/*.js'];
    var libs        = ext.getExtensionSources('tao', libsPattern, true).concat([
        'jquery',
        'jqueryui',
        'filereader',
        'store',
        'select2',
        'lodash',
        'async',
        'moment',
        'handlebars',
        'ckeditor',
        'class',
        'jwysiwyg',
        'jquery.tree',
        'jquery.timePicker',
        'jquery.cookie',
        'jquery.fileDownload',
        'raphael',
        'scale.raphael',
        'tooltipster',
        'history']);

    grunt.option('mainlibs', libs);

    //extract tao version
    var constants = grunt.file.read('../../includes/constants.php');
    var taoVersion = constants.match(/'TAO_VERSION'\,\s?'(.*)'/)[1];
    grunt.log.write('Found tao version ' + taoVersion);

     // Load local tasks.
    grunt.loadTasks('tasks');


    // load separated configs into each extension
    var sassTasks   = [];
    var bundleTasks = [];
    var testTasks   = [];
    ext.getExtensions().forEach(function(extension){
        grunt.log.debug(extension);
        var extensionKey = extension.toLowerCase();
        var gruntDir = root + '/' + extension + '/views/build/grunt';
        if(grunt.file.exists(gruntDir)){
            grunt.verbose.write('Load tasks from gruntDir ' + gruntDir);
            grunt.loadTasks(gruntDir);
        }

        //register all bundle tasks under a bigger one
        if(grunt.task.exists(extensionKey + 'bundle')){
            bundleTasks.push(extensionKey + 'bundle');
        }

        //register all sass tasks under a bigger one
        if(grunt.task.exists(extensionKey + 'sass')){
            sassTasks.push(extensionKey + 'sass');
        }

        //register all test tasks under a bigger one
        if(grunt.task.exists(extensionKey + 'test')){
            testTasks.push(extensionKey + 'test');
        }
    });

    //task to run by extensions concurrently
    grunt.config('concurrent', {
        build : ['bundleall', 'sassall']
    });

    grunt.config('qunit_junit', {
        options : {
            dest : reportOutput,
            namer : function(url){

                return url
                    .replace('http://127.0.0.1:' + testPort + '/', '')
                    .replace('/test.html', '')
                    .replace(/\//g, '.');
            }
        }
    });

    //to start a static web server
    grunt.config('connect', {
        test: {
            options: {
                protocol : 'http',
                hostname : '127.0.0.1',
                port: testPort,
                base: root,
                middleware: function(connect, options, middlewares) {

                    var rjsConfig = require('./config/requirejs.test.json');
                    rjsConfig.baseUrl = 'http://127.0.0.1:' + testPort + '/tao/views/js';
                    ext.getExtensions().forEach(function(extension){
                        rjsConfig.paths[extension] = '../../../' + extension + '/views/js';
                        rjsConfig.paths[extension + 'Css'] = '../../../' + extension + '/views/css';
                    });

                    // inject that mock the requirejs config
                    middlewares.unshift(function(req, res, next) {
                        if (/\/tao\/ClientConfig\/config/.test(req.url)){
                            res.writeHead(200, { 'Content-Type' : 'application/javascript'});
                            return res.end('require.config(' + JSON.stringify(rjsConfig) + ')');
                        }
                        return next();
                    });

                    return middlewares;
                }
            }
        }
    });

    /*
     * Create task alias
     */
    grunt.registerTask('sassall', "Compile all sass files", sassTasks);
    grunt.registerTask('bundleall', "Compile all js files", bundleTasks);
    grunt.registerTask('testall', "Run all tests", ['connect:test', 'junit_qunit'].concat(testTasks));
    grunt.registerTask('build', "The full build sequence", ['concurrent:build']);
};
