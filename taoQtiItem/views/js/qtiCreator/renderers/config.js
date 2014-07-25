define(['lodash', 'taoQtiItem/qtiCommonRenderer/renderers/config'], function(_, commonRenderConfig){

    var locations = _.defaults({
        '_container' : 'taoQtiItem/qtiCreator/renderers/Container',
        'assessmentItem' : 'taoQtiItem/qtiCreator/renderers/Item',
        'rubricBlock' : 'taoQtiItem/qtiCreator/renderers/RubricBlock',
        'img' : 'taoQtiItem/qtiCreator/renderers/Img',
        'math' : 'taoQtiItem/qtiCreator/renderers/Math',
        'object' : 'taoQtiItem/qtiCreator/renderers/Object',
        'modalFeedback' : 'taoQtiItem/qtiCreator/renderers/ModalFeedback',
        'choiceInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/ChoiceInteraction',
        'orderInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/OrderInteraction',
        'matchInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/MatchInteraction',
        'associateInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/AssociateInteraction',
        'inlineChoiceInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/InlineChoiceInteraction',
        'textEntryInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/TextEntryInteraction',
        'hotspotInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/HotspotInteraction',
        'selectPointInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/SelectPointInteraction',
        'graphicOrderInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/GraphicOrderInteraction',
        'graphicAssociateInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/GraphicAssociateInteraction',
        'graphicGapMatchInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/GraphicGapMatchInteraction',
        'mediaInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/MediaInteraction',
        'uploadInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/UploadInteraction',
        'sliderInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/SliderInteraction',
        'extendedTextInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/ExtendedTextInteraction',
        'simpleChoice.choiceInteraction' : 'taoQtiItem/qtiCreator/renderers/choices/SimpleChoice.ChoiceInteraction',
        'simpleChoice.orderInteraction' : 'taoQtiItem/qtiCreator/renderers/choices/SimpleChoice.OrderInteraction',
        'simpleAssociableChoice.associateInteraction' : 'taoQtiItem/qtiCreator/renderers/choices/SimpleAssociableChoice.AssociateInteraction',
        'simpleAssociableChoice.matchInteraction' : 'taoQtiItem/qtiCreator/renderers/choices/SimpleAssociableChoice.MatchInteraction',
        'gapMatchInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/GapMatchInteraction',
        'hottextInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/HottextInteraction',
        'gap' : 'taoQtiItem/qtiCreator/renderers/choices/Gap',
        'gapText' : 'taoQtiItem/qtiCreator/renderers/choices/GapText',
        'hottext' : 'taoQtiItem/qtiCreator/renderers/choices/Hottext'
    }, commonRenderConfig.locations);

    return {
        name : 'creatorRenderer',
        locations : locations
    };
});
