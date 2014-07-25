define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/choices/SimpleAssociableChoice.AssociateInteraction',
    'taoQtiItem/qtiCreator/widgets/choices/simpleAssociableChoice/Widget'
], function(_, SimpleAssociableChoice, SimpleChoiceWidget){
    
    var CreatorSimpleChoice = _.clone(SimpleAssociableChoice);

    CreatorSimpleChoice.render = function(choice, options){
        
        return SimpleChoiceWidget.build(
            choice,
            SimpleAssociableChoice.getContainer(choice),
            this.getOption('choiceOptionForm'),
            options
        );
    };

    return CreatorSimpleChoice;
});