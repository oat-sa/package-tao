define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/choices/GapText',
    'taoQtiItem/qtiCreator/widgets/choices/gapText/Widget'
], function(_, GapText, GapTextWidget){
    
    var CreatorGapText = _.clone(GapText);

    CreatorGapText.render = function(choice, options){
        
        return GapTextWidget.build(
            choice,
            GapText.getContainer(choice),
            this.getOption('choiceOptionForm'),
            options
        );
    };

    return CreatorGapText;
});