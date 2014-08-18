define([
    'jquery'
], function($){
    
    'use strict'

    function ucfirst(str){
        return str.charAt(0).toUpperCase() + str.substr(1);
    }

    var adaptSize = (function(){

        var height = function(elements){
            _resize(elements, {height : 0});
        };

        var width = function(elements){
            _resize(elements, {width : 0});
        };

        var both = function(elements){
            _resize(elements, {height : 0, width : 0});
        };

        var _resize = function(elements, dimensions){

            var dimension;

            if(!(elements instanceof $)){
                elements = $(elements);
            }

            // This whole function is based on calculating the largest height/width.
            // Therefor the elements need to have style: height/width to be removed
            // since otherwise we could never track when something is actually getting smaller than before.
            elements.each(function(){
                var element = $(this);
                for(dimension in dimensions){
                    element[dimension]('auto');
                }
            });

            elements.each(function(){
                var element = $(this);
                for(dimension in dimensions){
                    dimensions[dimension] = Math.max(dimensions[dimension], element['outer' + ucfirst(dimension)]());
                }
            });

            elements.css(dimensions);
        };

        return {
            width : width,
            height : height,
            both : both
        }

    })();
    
    return adaptSize;
});