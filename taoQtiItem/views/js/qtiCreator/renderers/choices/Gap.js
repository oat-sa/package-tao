define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/choices/Gap',
    'taoQtiItem/qtiCreator/widgets/choices/gap/Widget'
], function(_, Gap, GapWidget){
    
    var CreatorGap = _.clone(Gap);

    CreatorGap.render = function(choice, options){
        
        return GapWidget.build(
            choice,
            Gap.getContainer(choice),
            this.getOption('choiceOptionForm'),
            options
        );
    };

    return CreatorGap;
});