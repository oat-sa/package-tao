module.exports = function(grunt) {

    var jshint  = grunt.config('jshint') || {};
    var root    = grunt.option('root');
    var currentExtension = grunt.option('currentExtension');
    var reportOutput = grunt.option('reports') || 'reports';
    var extensionRoot = root + '/' + currentExtension + '/';

    jshint.options = {
        jshintrc : '.jshintrc'
    };

    /**
     * grunt jshint:file --file /path/to/file/to/lint
     */

    jshint.file = {
         src : grunt.option('file')
    };

    /**
     * grunt jshint:extension --extension taoQtiTest
     */
    jshint.extension = {
        src : [
            extensionRoot + '/views/js/**/*.js',
            '!' + extensionRoot + 'views/js/**/*.min.js',
            '!' + extensionRoot + 'views/js/**/*.src.js',
            '!' + extensionRoot + 'views/js/test/**/*.js',
            '!' + extensionRoot + 'views/js/lib/**/*.js',
            '!' + extensionRoot + 'views/js/portableSharedLibraries/**/*.js',
            '!' + extensionRoot + 'views/js/**/jquery.*.js'
        ]
    };

    //grunt  jenkins reporter
    jshint.extensionreport = {
        options : {
            force : true,
            reporter: 'checkstyle',
            reporterOutput:  reportOutput + '/' + currentExtension + '-checkstyle.xml'
        },
        src : jshint.extension.src
    };

    grunt.config('jshint', jshint);
};
