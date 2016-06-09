define(['jquery', 'lodash', 'core/dataattrhandler', 'tooltipster'], function($, _, DataAttrHandler){
    'use strict';
    
    var themes = ['dark', 'default', 'info', 'warning', 'error', 'success'];

    /**
    * Look up for tooltips and initialize them
    * 
    * @public
    * @param {jQueryElement} $container - the root context to lookup inside
    */
    return function lookupSelecter($container){
       
        $('[data-tooltip]', $container).each(function(){
            var $elt = $(this);
            var $target = DataAttrHandler.getTarget('tooltip', $elt);
            var theme = _.contains(themes, $elt.data('tooltip-theme')) ? $elt.data('tooltip-theme') : 'default';
            $elt.tooltipster({
                theme: 'tao-' + theme  + '-tooltip',
                content: $target,          
                contentAsHTML: $target.children().length > 0,
                delay: 350,
                trigger: 'hover'
            });
        });
    };
});
