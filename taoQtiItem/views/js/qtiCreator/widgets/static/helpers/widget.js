define(['jquery', 'lodash'], function($, _){

    var helper = {
        buildInlineContainer : function(widget){
            
            var float = '';
            if(widget.element.hasClass('lft')){
                float = ' lft';
            }else if(widget.element.hasClass('rgt')){
                float = ' rgt';
            }
            
            var $wrap = $('<span>', {
                'data-serial' : widget.element.serial,
                'class' : 'widget-box widget-inline widget-'+widget.element.qtiClass+float,
                'data-qti-class' : widget.element.qtiClass,
                'contenteditable' : 'false'
            });
            widget.$container = widget.$original.wrap($wrap).parent();
            if(widget.$container.length){
                var textNode = widget.$container[0].nextSibling;
                if(textNode){
                    //@todo : make text cursor positioning after an inline widget easier
                    textNode.nodeValue = ' '+textNode.nodeValue;
                }
            }
        },
        buildBlockContainer : function(widget){
            
            //absolutely need a div here (not span), otherwise mathjax will break
            var $wrap = $('<div>', {
                'data-serial' : widget.element.serial,
                'class' : 'widget-box widget-block widget-'+widget.element.qtiClass,
                'data-qti-class' : widget.element.qtiClass
            });
            widget.$container = widget.$original.wrap($wrap).parent();
        },
        createToolbar : function(widget, toolbarTpl){
        
            if(_.isFunction(toolbarTpl)){

                var $tlb = $(toolbarTpl({
                    serial : widget.serial,
                    state : 'active'
                }));

                widget.$container.append($tlb);

                $tlb.find('[data-role="delete"]').on('click.widget-box', function(e){
                    e.stopPropagation();//to prevent direct deleting;
                    widget.changeState('deleting');
                });

            }else{
                throw 'the toolbarTpl must be a handlebars function';
            }
        }
    };

    return helper;
});
