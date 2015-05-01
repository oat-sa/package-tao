define(['taoQtiItem/qtiCreator/widgets/states/factory', 'taoQtiItem/qtiCreator/widgets/interactions/states/Sleep'], function(stateFactory, SleepState){

    var InlineInteractionStateSleep = stateFactory.create(SleepState, function(){

        var _widget = this.widget,
            $container = this.widget.$original;

        $container.on('click.qti-widget.sleep', function(e){
            e.stopPropagation();
            //if active == false do this: (else nothing)
            //show toolbar, prompt widget and choice widgets and property form

            //init default mode: question
            _widget.changeState('question');
        });

        //add listener to display proper hover style
        $container.on('mouseenter.sleep', function(e){
            $container.addClass('hover');
            $container.parent().trigger('mouseleave.sleep');
        }).on('mouseleave.sleep', function(){
            $container.removeClass('hover');
            $container.parent().trigger('mouseenter.sleep');
        });

    }, function(){

        this.widget.$original.removeClass('hover').off('.sleep');
    });

    return InlineInteractionStateSleep;
});