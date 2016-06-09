/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
(function(window){
    'use strict';

    //the url of the app config is set into the data-config attr of the loader.
    var appConfig = document.getElementById('amd-loader').getAttribute('data-config');
    //loads the config
    require([appConfig], function(){

        /**
         * Main entry point for the backend.
         * Initialize high level components like the router, the error messages and the ui components listeners
         */
        require(['jquery', 'i18n', 'lodash', 'context', 'helpers', 'router', 'ui', 'core/history', 'ui/feedback', 'layout/logout-event'],
            function ($, __, _, context, helpers, router, ui, history, feedback, logoutEvent) {

                var $doc = $(document);
                var $container = $('body > .content-wrap');

                //fix backspace going back into the history
                history.fixBrokenBrowsers();

                //contextual loading, do a dispatch each time an ajax request loads an HTML page
                $doc.ajaxComplete(function(event, request, settings){
                    if(_.contains(settings.dataTypes, 'html')){

                       var urls = [settings.url];
                       var forward = request.getResponseHeader('X-Tao-Forward');
                       if(forward){
                           urls.push(forward);
                       }

                       router.dispatch(urls, function(){
                           ui.startDomComponent($container);
                       });
                    }
                });

                //dispatch also the current page (or the forward)
                router.dispatch(helpers._url(context.action, context.module, context.extension));

                //intercept errors
                //TODO this should belongs to the Router
                $doc.ajaxError(function (event, request, settings, exception) {

                    var errorMessage = __('Unknown Error');

                    if (request.status === 404 && settings.type === 'HEAD') {
                        //consider it as a "test" to check if resource exists
                        return;

                    } else if (request.status === 404 || request.status === 500) {
                        try {
                            // is it a common_AjaxResponse? Let's "duck type"
                            var ajaxResponse = $.parseJSON(request.responseText);
                            if (ajaxResponse !== null &&
                                typeof ajaxResponse.success !== 'undefined' &&
                                typeof ajaxResponse.type !== 'undefined' &&
                                typeof ajaxResponse.message !== 'undefined' &&
                                typeof ajaxResponse.data !== 'undefined') {

                                errorMessage = request.status + ': ' + ajaxResponse.message;
                            }
                            else {
                                errorMessage = request.status + ': ' + request.responseText;
                            }

                        }
                        catch (exception) {
                            // It does not seem to be valid JSON.
                            errorMessage = request.status + ': ' + request.responseText;
                        }
                    }
                    
                    if (request.status === 403) {
                        logoutEvent();
                    } else {
                        feedback().error(errorMessage);
                    }
                });

                //initialize new components
                ui.startEventComponents($container);

        });
    });
}(window));
