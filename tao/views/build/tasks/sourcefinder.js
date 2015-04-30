module.exports = function sourcefinder(grunt) {
    'use strict';
    
    //add the task to Grunt
    grunt.registerMultiTask('sourcefinder', 'Find sources and generate a config file', function(){
        var path        = require('path');
        var extHepler   = require('./helpers/extensions');
        var _           = require('lodash');
        var options     = grunt.task.current.options();
        var done	= grunt.task.current.async();
        var sources     = grunt.task.current.data.sources;
        var currentExt  = grunt.option('extension') || 'tao';   // --extension=taoQTI


        //run the task for Each src/dest fileSet
        grunt.task.current.files.forEach(function(fileSet){
            var root    = path.resolve(fileSet.src + '');
            var dest    = path.resolve(fileSet.dest);
            var ext     = extHepler(grunt, root);
            var config  = {};
            
            grunt.log.debug(root);
            grunt.log.debug(dest);
            
            //expand the sources by key
            _.forEach(sources, function(values, key){
                var expanded = [];
                
                //it could be an array of conf, so force it
                if(!_.isArray(values)){
                    values = [values];
                }
                
                values.forEach(function(value){
                    
                    //expand the pattern for the selected extension(s)
                    var currentFiles = [];
                    if(value.extension === 'all'){
                        currentFiles = ext.getExtensionsSources(value.pattern, value.amdify);
                    } else {
                        var extension = (value.extension === 'current' ? currentExt : value.extension);
                        currentFiles = ext.getExtensionSources(extension, value.pattern, value.amdify);
                    }
                    
                    //apply the replacement on
                    if(_.isFunction(value.replacements)){
                       currentFiles = currentFiles.map(value.replacements);
                    }
                    
                    expanded = expanded.concat(currentFiles); 
                });
                
                
                config[key] = expanded;
                
            });
            grunt.file.write(dest, JSON.stringify(config, null, '  '));
            grunt.log.ok('Sources written to ' + dest);

            if(options.inConfig){
                grunt.config.set(options.inConfig, config);
                grunt.log.ok('Sources avilable in Grunt config under ' + options.inConfig + ' (ie. <%= sources.jshint %>)');
            }
            done();
        });
    });
};
