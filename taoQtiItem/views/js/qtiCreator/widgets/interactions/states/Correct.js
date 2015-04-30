define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Correct'
], function(stateFactory, Correct){

    var InteractionStateCorrect = stateFactory.create(Correct, function(){
        //use default [data-edit="correct"].show();
    }, function(){
        //use default [data-edit="correct"].hide();
    });

    return InteractionStateCorrect;
});
