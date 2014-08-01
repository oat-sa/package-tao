define(['jquery', 'filemanager/fmRunner'], function($, FmRunner){
    'use strict';

    return {
        start: function(){
            var lastFocussed;
           $(':text').live('focus',function(){
                    lastFocussed = this;
            });
            
            var options = {
                type: 'file', 
                showselect: false
            };
            if(lastFocussed){
                options.elt = lastFocussed;
            }
            
            FmRunner.load(options, function(element, url){
                if(lastFocussed){
                    $(lastFocussed).val($(lastFocussed).val() + url);
                }
            });
        }
    };
});
