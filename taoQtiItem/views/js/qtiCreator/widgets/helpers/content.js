define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCreator/helper/creatorRenderer',
    'taoQtiItem/qtiCreator/model/helper/container',
    'taoQtiItem/qtiCreator/editor/gridEditor/content'
], function($, _, creatorRenderer, containerHelper, gridContentHelper){

    var contentHelper = {};
    
    contentHelper.createElements = function(container, $container, data, callback){

        var $dummy = $('<div>').html(data);

        containerHelper.createElements(container, gridContentHelper.getContent($dummy), function(newElts){

            creatorRenderer.get().load(function(){

                for(var serial in newElts){

                    var elt = newElts[serial],
                        $placeholder = $container.find('.widget-box[data-new][data-qti-class=' + elt.qtiClass + ']'),
                        $widget,
                        widget;
                        
                    elt.setRenderer(this);
                    elt.render($placeholder);

                    //render widget
                    widget = elt.postRender();
                    $widget = widget.$original;

                    //inform height modification
                    $widget.trigger('contentChange.gridEdit');

                    if(_.isFunction(callback)){
                        callback(widget);
                    }
                    
                }

            }, this.getUsedClasses());
        });

    };
    
    contentHelper.changeInnerWidgetState = function _changeInnerWidgetState(outerWidget, state){
        
         var selector = [];
        _.each(['img', 'math', 'object'], function(qtiClass){
            selector.push('[data-html-editable] .widget-'+qtiClass); 
        });
        
        outerWidget.$container.find(selector.join(',')).each(function(){
            var innerWidget = $(this).data('widget');
            if(innerWidget){
                innerWidget.changeState(state);
            }
        });
    };
    
    return contentHelper;
});