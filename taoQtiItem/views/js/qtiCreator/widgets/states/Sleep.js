define(['taoQtiItem/qtiCreator/widgets/states/factory'], function(stateFactory){
    
    return stateFactory.create('sleep', function(){
        
        var widget = this.widget,
            $container = this.widget.$container;
        
        //add listener to display proper hover style
        $container.on('mouseenter.sleep', function(e){
            $container.addClass('hover');
            $container.parent().trigger('mouseleave');//note : don't trigger it with namespace otherwise, choice.on(mouseleave.choice) will not be triggered
        }).on('mouseleave.sleep', function(){
            $container.removeClass('hover');
            $container.parent().trigger('mouseenter');//note : same as mouseenter.sleep
        });
        
        if(!widget.isValid()){
            widget.changeState('invalid');
        }
        
    }, function(){

        this.widget.$container.removeClass('hover').off('.sleep');

    });
});