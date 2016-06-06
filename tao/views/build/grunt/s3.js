module.exports = function(grunt) {
    'use strict';

    var awsConfig  = require('../config/aws.json');

    var compress = grunt.config('compress') || {};
    var awsS3    = grunt.config('aws_s3') || {};
    var clean    = grunt.config('clean') || {};
    var root     = grunt.option('root');
    var concurrency = grunt.option('s3-concurrency') || 20;                  // run the cli with --s3-concurrency=N
    var ext      = require('../tasks/helpers/extensions')(grunt, root);   //extension helper
    var out      = 'output';


    clean.s3 = [out];

    var patterns = [];
    ext.getExtensions().forEach(function(extension){
        patterns.push(extension + '/views/js/**/*');
        patterns.push(extension + '/views/css/**/*');
        patterns.push(extension + '/views/img/**/*');
        patterns.push(extension + '/views/locales/**/*');
        patterns.push(extension + '/views/media/**/*');
        patterns.push('!'  + extension + '/views/js/test/**/*');
    });

    grunt.log.debug(grunt.file.expand({ cwd : root }, patterns));

    compress.s3 = {
        options: {
            mode: 'gzip',
            pretty: true
        },
        cwd : root,
        expand: true,
        src: patterns,
        dest: out
    };

    awsS3.options = {
        accessKeyId: awsConfig.s3.accessKeyId,
        secretAccessKey: awsConfig.s3.secretKey,
        region: awsConfig.s3.region,
        uploadConcurrency: concurrency,
        bucket: awsConfig.s3.bucket
    };
    awsS3.clean = {
        files: [{
            dest: awsConfig.s3.path + '**/*',
            action: 'delete'
        }]
    };
    awsS3.upload = {
        files: [{
            expand: true,
            cwd: out,
            src: patterns,
            dest : awsConfig.s3.path,
            params: {
                ContentEncoding: 'gzip'
            }
        }, {
            expand: true,
            cwd: root,
            src: patterns,
            dest : awsConfig.s3.path
        }]
    };

    grunt.loadNpmTasks('grunt-aws-s3');

    grunt.config('clean', clean);
    grunt.config('aws_s3', awsS3);
    grunt.config('compress', compress);
};
