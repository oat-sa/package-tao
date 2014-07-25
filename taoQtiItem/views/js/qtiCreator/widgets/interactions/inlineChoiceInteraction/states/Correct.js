define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Correct',
    'taoQtiItem/qtiCreator/widgets/interactions/inlineChoiceInteraction/ResponseWidget',
    'lodash'
], function(stateFactory, Correct, responseWidget, _){

    var InlineChoiceInteractionStateCorrect = stateFactory.create(Correct, function(){
        
        var _widget = this.widget,
            response = this.widget.element.getResponseDeclaration();
            
            _widget.$container.find('table').hide();
            
            //render commonRenderer.render()
            responseWidget.create(_widget, function(){
               
                //set response
                responseWidget.setResponse(_widget, _.values(response.getCorrect()));

                //save correct response on change
                _widget.$container.on('responseChange.qti-widget', function(e, data){
                    response.setCorrect(responseWidget.unformatResponse(data.response));
                });
            });

    }, function(){
        
        var $container = this.widget.$container;
        $container.find('table').show();
        $container.off('responseChange.qti-widget');
        
        responseWidget.destroy(this.widget);
    });

    return InlineChoiceInteractionStateCorrect;
});