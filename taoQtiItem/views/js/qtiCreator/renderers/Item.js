define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/Item',
    'taoQtiItem/qtiCreator/widgets/item/Widget',
    'tpl!taoQtiItem/qtiCreator/tpl/item'
], function(_, CommonRenderer, Widget, tpl){

    var CreatorItem = _.clone(CommonRenderer);
    
    var _normalizeItemBody = function _normalizeItemBody($itemBody) {
        
        $itemBody.children().each(function(){
            var $child = $(this);
            //must be a grid-row for editing:
            if(!$child.hasClass('grid-row')){
                $child.wrap('<div class="grid-row"><div class="col-12"></div></div>');
            }
        });
        
        return $itemBody;
    };
    
    CreatorItem.template = tpl;
    
    CreatorItem.render = function(item, options){
        
        var $itemContainer = CommonRenderer.getContainer(item);
        
        _normalizeItemBody($itemContainer.find('.qti-itemBody'));
        
        options = options || {};
        options.state = 'active';//the item widget never sleeps ! <- this sounds very scary!
        options.renderer = this;
        
        return Widget.build(
            item,
            $itemContainer,
            this.getOption('itemOptionForm'),
            options
        );
    };

    return CreatorItem;
});