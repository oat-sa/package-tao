define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/helpers/content'
], function($, stateFactory, contentHelper){

    return stateFactory.create('active', function(){

        var _widget = this.widget,
            container = _widget.$container[0],
            item = this.widget.element.getRelatedItem();

        //move to sleep state by clicking everywhere outside the interaction 
        $('#item-editor-panel').on('click.active', function(e){
            if(container !== e.target && !$.contains(container, e.target)){
                _widget.changeState('sleep');
            }
        }).on('styleedit.active', function(){
            _widget.changeState('sleep');
        });

        if(item){
            //in item editing context:
            item.data('widget').$container.on('resizestart.gridEdit.active beforedragoverstart.gridEdit.active', function(){
                _widget.changeState('sleep');
            });
        }

    }, function(){
        
        contentHelper.changeInnerWidgetState(this.widget, 'sleep');
        
        this.widget.$container.off('.active');
        $('#item-editor-panel').off('.active');
        
        var item = this.widget.element.getRelatedItem();
        if(item){
            item.data('widget').$container.off('.active');
        }
        
    });
});
