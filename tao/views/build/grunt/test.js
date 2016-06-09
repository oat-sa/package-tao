module.exports = function(grunt) {
    'use strict';


    var root        = grunt.option('root');
    var testPort    = grunt.option('testPort');
    var reportOutput= grunt.option('reports');
    var ext         = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);
    var fs          = require('fs');
    var path        = require('path');
    var qunit       = grunt.config('qunit') || {};
    var testUrl     = 'http://127.0.0.1:' + testPort;

    /*
     * Global tasks/config
     */
    qunit.options = {
        inject: './config/phantomjs-bridge.js',
        force: true
    };

    //convert QUnit report to JUnit reports for Jenkins
    grunt.config('qunit_junit', {
        options : {
            dest : reportOutput,

            fileNamer : function(url){
                return url
                    .replace(testUrl + '/', '')
                    .replace('/test.html', '')
                    .replace(/\//g, '.');
            },

            classNamer : function (moduleName, url) {
                return url
                    .replace(testUrl + '/', '')
                    .replace('views/js/test/', '')
                    .replace('/test.html', '')
                    .replace(/\//g, '.');
            }
        }
    });

    //starts a static web server to serve assets for tests and the requirejs config
    grunt.config('connect', {
        test: {
            options: {
                protocol : 'http',
                hostname : '127.0.0.1',
                port: testPort,
                base: root,
                middleware: function(connect, options, middlewares) {

                    var rjsConfig = require('../config/requirejs.test.json');
                    rjsConfig.baseUrl = testUrl + '/tao/views/js';
                    ext.getExtensions().forEach(function(extension){
                        rjsConfig.paths[extension] = '../../../' + extension + '/views/js';
                        rjsConfig.paths[extension + 'Css'] = '../../../' + extension + '/views/css';
                    });

                    // inject a mock for the requirejs config
                    middlewares.unshift(function(req, res, next) {
                        if (/\/tao\/ClientConfig\/config/.test(req.url)){
                            res.writeHead(200, { 'Content-Type' : 'application/javascript'});
                            return res.end('require.config(' + JSON.stringify(rjsConfig) + ')');
                        }
                        return next();
                    });

                    //allow post requests
                    middlewares.unshift(function(req, res, next) {
                        if (req.method.toLowerCase() === 'post') {
                            var filepath = path.join(options.base[0], req.url);
                            if (fs.existsSync(filepath)) {
                                fs.createReadStream(filepath).pipe(res);
                                return;
                            }
                        }
                        return next();
                    });


                    return middlewares;
                }
            }
        }
    });

    /*
     * Single file test
     */
    qunit.single = {
        options : {
            console: true,
            force:   false,
            urls:    [testUrl + grunt.option('test')]
        }
    };


    /*
     * Tao extension tests
     */

    var testRunners = root + '/tao/views/js/test/**/test.html';
    var testFiles = root + '/tao/views/js/test/**/test.js';

    //extract unit tests
    var extractTests = function extractTests(){
        return grunt.file.expand([testRunners]).map(function(path){
            return path.replace(root, testUrl);
        });
    };

    /**
     * tests to run
     */
    qunit.taotest = {
        options : {
            console : true,
            urls : extractTests()
        }
    };

    grunt.config('qunit', qunit);

    // test task
    grunt.registerTask('taotest', ['qunit:taotest']);
};
