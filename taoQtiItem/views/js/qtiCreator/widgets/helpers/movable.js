define([
    'jquery',
    'taoQtiItem/qtiCreator/editor/gridEditor/draggable',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/grip'
], function($, draggable, gripTpl){

    var movable = {
        create : function(widget){

            var item = widget.element.getRelatedItem(),
                $container = widget.$container,
                $itemBody;

            var $grip = $(gripTpl({
                serial : widget.serial
            }));

            $container.append($grip);

            $grip.on('mouseenter.qti-widget', function(){

                if(!$itemBody || !$itemBody.length){
                    $itemBody = item.data('widget').$container;
                }

                //create movable
                //enable the new element to be movable
                draggable.createMovable($container, $itemBody);

            }).on('mouseleave.qti-widget', function(){
                //destroy movable
                draggable.destroy($container);
            });
        }
    }
    
    return movable;
});