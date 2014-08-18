define([
    'jquery'
], function($){
    'use strict';

    /**
     * Show elements temporarily
     *
     * @param $elements
     * @private
     */
    var _show = function($elements){
        
        var $element;

        $elements.each(function(){
            $element = $(this);
            $element.data('originalProperties', {
                display : $element.css('display'),
                position : $element.css('position'),
                left : $element.css('left'),
                width: $element.css('width'),
                height: $element.css('height')
            });

            $element.css({
                position : 'relative',
                left : '-10000px',
                width: 'auto',
                height: 'auto',
                display: 'inline-block'
            });
        });
    };


    /**
     * Hide elements after size has been taken
     *
     * @param $elements
     * @private
     */
    var _hide = function($elements){
        
        var $element;

        $elements.each(function(){
            $element = $(this);

            $element.css($elements.data('originalProperties'));

            $element.removeData('originalProperties');
        });

    };


    /**
     * Measure the outer size of the container while all elements are displayed
     *
     * @param $element
     * @returns {{width: *, height: *}}
     * @private
     */
    var _measure = function($element){
        return {
            width : $element.outerWidth(),
            height : $element.outerHeight()
        };
    };


    /**
     * Return the size value, also trigger an event for convenience
     *
     * @param {jQueryElement} $container -
     * @param {Function} callback - with the size in parameter
     * @returns {Object} the size {width: *, height: *}
     */
    var measure = function($container, callback){
        var size;

        if($container && $container.length){ 
            _show($container);
            size = _measure($container);
            _hide($container);

            callback.call($container[0], size);
            $container.trigger('measured.sizeFinder', size);
        }
        return size;
    };

    return {
        measure : measure
    };
});
