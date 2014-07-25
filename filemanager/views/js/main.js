(function(){
    //the url of the app config is set into the data-config attr of the loader.
    var appConfig = document.getElementById('amd-loader').getAttribute('data-config');
    require([appConfig], function(){
        require(['filemanager/filemanager'], function(fileManager){
            fileManager.start(fileManagerOptions);
        }); 
    });
}());