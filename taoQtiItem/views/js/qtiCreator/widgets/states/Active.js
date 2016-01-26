define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/helpers/content'
], function($, stateFactory, contentHelper){

    return stateFactory.create('active', function(){

        var _widget = this.widget,
            container = _widget.$container[0],
            item = this.widget.element.getRelatedItem();

        //move to sleep state by clicking anywhere outside the interaction 
        $('#item-editor-panel').on('mousedown.active.' + _widget.serial, function(e){
            if (
                container !== e.target 
                && !$.contains(container, e.target) 
                && !$.contains($('#modalFeedbacks')[0], e.target) //if click triggered inside the #modalFeedback then state must not be changed.
            ){
                _widget.changeState('sleep');
            }
        }).on('beforesave.qti-creator.active', function(){
            _widget.changeState('sleep');
        }).on('styleedit.active', function(){
            _widget.changeState('sleep');
        });

        if(item && item.data('widget')){
            //in item editing context:
            item.data('widget').$container.on('resizestart.gridEdit.active beforedragoverstart.gridEdit.active', function(){
                _widget.changeState('sleep');
            });
        }

    }, function(){
        
        contentHelper.changeInnerWidgetState(this.widget, 'sleep');
        
        this.widget.$container.off('.active');
        $('#item-editor-panel').off('.active.'+ this.widget.serial);
        
        var item = this.widget.element.getRelatedItem();
        if(item && item.data('widget')){
            item.data('widget').$container.off('.active');
        }
        
    });
});
