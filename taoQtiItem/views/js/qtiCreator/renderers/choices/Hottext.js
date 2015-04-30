define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/choices/Hottext',
    'taoQtiItem/qtiCreator/widgets/choices/hottext/Widget'
], function(_, Hottext, HottextWidget){
    
    var CreatorHottext = _.clone(Hottext);

    CreatorHottext.render = function(choice, options){
        
        return HottextWidget.build(
            choice,
            Hottext.getContainer(choice),
            this.getOption('choiceOptionForm'),
            options
        );
    };

    return CreatorHottext;
});