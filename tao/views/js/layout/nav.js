
define(['jquery',], function($) {
    'use strict';

    /**
     * This component manage the navigation bar of TAO.
     * 
     * @exports layout/nav
     */
    return {

        /**
         * Initialize the navigation bar
         */
        init : function(){
            var $container = $('header.dark-bar > nav');

            //here the bindings are controllers or even the name of any AMD file to load
            $('[data-action]', $container).off('click').on('click', function(e){
                e.preventDefault();
                var binding = $(this).data('action');
                if(binding){
                    require([binding], function(controller){
                        if(controller &&  typeof controller.start === 'function'){
                            controller.start();
                        }
                    });
                }
            });
        }
    };
});
