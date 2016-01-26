var path = require('path');

module.exports = function(grunt, root){

    return {
        getExtensions : function getExtensions(clientSide) {
            var options = {
                cwd: root,
                filter: function(src) {
                    return grunt.file.isDir(src) && grunt.file.exists(src + '/manifest.php') &&
                            (!clientSide || grunt.file.exists(src + '/views/js/controller/routes.js'));
                }
            };
            return grunt.file.expand(options, '*');
        },

        getExtensionPath: function getExtensionPath(extension) {
            extension = extension || 'tao';
            return root + '/' + extension;
        },

        getExtensionSources : function getExtensionSources(extension, filePattern, amdify) {
            var extPath = this.getExtensionPath(extension);

            var jsSources = grunt.file.expand({cwd: extPath}, filePattern);
            jsSources.forEach(function(source, index) {
                var path = extPath + '/' + source;
                if(amdify && amdify === true){
                    path = path.replace(extPath + '/views/js', extension === 'tao' ? '': extension).replace(/\.js$/, '').replace(/^\//, '');
                }
                jsSources[index] = path;

            });
            return jsSources;
        },

        getExtensionsSources : function getExtensionsSources(filePattern, amdify){
            var self = this;
            var sources = [];
            this.getExtensions(true).forEach(function(extension){
                sources = sources.concat(self.getExtensionSources(extension, filePattern, amdify));
            });
            return sources;
        },

       getExtensionsPaths : function getExtensionsPaths(extensions){
           var self = this;
           var paths = { };
           extensions = extensions || self.getExtensions(true);
           extensions.forEach(function(extension){
               var jsPath = self.getExtensionPath(extension) + '/views/js';
               var cssPath = self.getExtensionPath(extension) + '/views/css';
               if(grunt.file.exists(jsPath)){
                   paths[extension] = path.relative('../js', jsPath);
               }
               if(grunt.file.exists(cssPath)){
                   paths[extension + 'Css'] = path.relative('../js', cssPath);
               }
           });
           return paths;
       },


        // OUT OF DATE !!!

       getExtensionsControllers : function getExtensionsControllers(extensions){
           var self = this;
           var modules = [];
           extensions = extensions || self.getExtensions(true);
           extensions.forEach(function(extension){
                var extPath = self.getExtensionPath(extension);
                modules = modules.concat(self.getExtensionSources(extension, 'views/js/controller/**/*.js').map(function(source){
                    return source.replace(extPath + '/views/js', extension).replace(/\.js$/, '').replace(/^\//, '');
                }));
            });
            return modules;
       },

       getExtensionsLibs : function getExtensionsLibs(extensions){
           var self = this;
           var modules = [];
           extensions = extensions || self.getExtensions(true);
           extensions.forEach(function(extension){
                var extPath = self.getExtensionPath(extension);
                modules = modules.concat(self.getExtensionSources(extension, ['views/js/*.js', '!views/js/*.min.js', '!views/js/test/**/*.js']).map(function(source){
                    return source.replace(extPath + '/views/js', extension === 'tao' ? '': extension).replace(/\.js$/, '').replace(/^\//, '');
                }));
            });
            return modules;
       }
   };
};

