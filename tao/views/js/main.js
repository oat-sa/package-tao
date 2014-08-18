(function(window){
    //the url of the app config is set into the data-config attr of the loader.
    var appConfig = document.getElementById('amd-loader').getAttribute('data-config');
    require([appConfig], function(){
        
        require(['jquery', 'lodash', 'context', 'router', 'ui', 'core/history'], 
            function ($, _, context, router, ui, history) {

                //fix backspace going back into the history
                history.fixBrokenBrowsers();

                //contextual loading, do a dispatch each time an ajax request loads an HTML page
                $(document).ajaxComplete(function(event, request, settings){
                    if(_.contains(settings.dataTypes, 'html')){
                       router.dispatch(settings.url, function(){
                           ui.startDomComponent($('.tao-scope'));
                       });
                    }
                });
               
                //dispatch also the current page
                router.dispatch(window.location.href);
                
                //initialize new components
                ui.startEventComponents($('.tao-scope'));
                
        });
    });
}(window));
