define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/choices/SimpleChoice.ChoiceInteraction',
    'taoQtiItem/qtiCreator/widgets/choices/simpleChoice/Widget'
], function(_, SimpleChoice, SimpleChoiceWidget){
    
    var CreatorSimpleChoice = _.clone(SimpleChoice);

    CreatorSimpleChoice.render = function(choice, options){
        
        SimpleChoiceWidget.build(
            choice,
            SimpleChoice.getContainer(choice),
            this.getOption('choiceOptionForm'),
            options
        );
    };

    return CreatorSimpleChoice;
});