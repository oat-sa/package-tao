define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Active'
], function($, stateFactory, Active){

    return stateFactory.extend(Active, function(){

        var _widget = this.widget,
            $container = _widget.$container;

        _widget.beforeStateInit(function(e, element, state){

            var serial = element.getSerial();
            if((state.name === 'active' && serial !== _widget.serial) || state.name === 'choice'){

                if(_widget.element.qtiClass === 'rubricBlock'){
                    //exclude
                    var composingElts = _widget.element.getComposingElements();
                    if(!composingElts[element.serial]){
                        _widget.changeState('sleep');
                    }
                }else{
                    //call sleep whenever other widget is active
                    _widget.changeState('sleep');
                }

            }

        }, 'otherActive');

        $container.on('mouseenter.active', function(e){
            e.stopPropagation();
            $container.parent().trigger('mouseleave.sleep');
        }).on('mouseleave.active', function(e){
            e.stopPropagation();
            $container.parent().trigger('mouseenter.sleep');
        }).on('click.active', function(e){
            e.stopPropagation();
        });

    }, function(){

        this.widget.$container.off('.active');
        $('#item-editor-panel').off('.active.' + this.widget.serial);

        this.widget.offEvents('otherActive');
    });

});
