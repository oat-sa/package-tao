module.exports = function(grunt) {

    var qunit       = grunt.config('qunit') || {};
    var root        = grunt.option('root');
    var testUrl     = 'http://127.0.0.1:' + grunt.option('testPort');

    /**
     * tests to run
     */
    qunit.taoitemstest = {
        options : {
            urls : [ testUrl + '/taoItems/views/js/test/runner/api/test.html']
        }
    };

    grunt.config('qunit', qunit);

    // bundle task
    grunt.registerTask('taoitemstest', ['qunit:taoitemstest']);
};
