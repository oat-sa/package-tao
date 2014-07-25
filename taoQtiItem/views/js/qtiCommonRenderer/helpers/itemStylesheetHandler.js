define([
    'jquery',
    'lodash'
], function($, _){
    'use strict';

    var itemStylesheetHandler = (function(){

        var informLoaded = _.throttle(function(){
            $(document).trigger('customcssloaded.styleeditor');
        }, 10, {leading : false});

        var attach = function(stylesheets) {
            var $head = $('head'), link;

             $('body').addClass('tao-scope');

             // relative links with cache buster
            _(stylesheets).forEach(function(stylesheet){

                var $link = $(stylesheet.render());
                var href = $link.attr('href');
                var sep  = href.indexOf('?') > -1 ? '&' : '?';



                if(href.indexOf('/') === 0) {
                    href = href.slice(1);
                }
                
                href +=  sep + (new Date().getTime()).toString();

                //we need to set the href after the link is appended to the head (for our dear IE)
                $link.removeAttr('href')
                     .appendTo($head)
                     .attr('href', href);

                //wait for the styles to applies
                _.delay(informLoaded, 10);
            });
        };

        return {
            attach: attach
        };

    }());

    return itemStylesheetHandler;
});
