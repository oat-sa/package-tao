define(['lodash', 'taoQtiItem/qtiDefaultRenderer/renderers/config'], function(_, defaultRenderConfig){
    var locations = _.extend(_.clone(defaultRenderConfig.locations), {
        'assessmentItem' : 'taoQtiItem/qtiCommonRenderer/renderers/Item',
        '_container' : 'taoQtiItem/qtiCommonRenderer/renderers/Container',
        '_simpleFeedbackRule' : false,
        'stylesheet' : 'taoQtiItem/qtiCommonRenderer/renderers/Stylesheet',
        'outcomeDeclaration' : 'taoQtiItem/qtiCommonRenderer/renderers/OutcomeDeclaration',
        'responseDeclaration' : 'taoQtiItem/qtiCommonRenderer/renderers/ResponseDeclaration',
        'responseProcessing' : 'taoQtiItem/qtiCommonRenderer/renderers/ResponseProcessing',
        'img' : 'taoQtiItem/qtiCommonRenderer/renderers/Img',
        'math' : 'taoQtiItem/qtiCommonRenderer/renderers/Math',
        'object' : 'taoQtiItem/qtiCommonRenderer/renderers/Object',
        'rubricBlock' : 'taoQtiItem/qtiCommonRenderer/renderers/RubricBlock',
        'modalFeedback' : 'taoQtiItem/qtiCommonRenderer/renderers/ModalFeedback',
        'prompt' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/Prompt',
        'choiceInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/ChoiceInteraction',
        'extendedTextInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/ExtendedTextInteraction',
        'orderInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/OrderInteraction',
        'associateInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/AssociateInteraction',
        'matchInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/MatchInteraction',
        'textEntryInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/TextEntryInteraction',
        'sliderInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/SliderInteraction',
        'inlineChoiceInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/InlineChoiceInteraction',
        'simpleChoice.choiceInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/choices/SimpleChoice.ChoiceInteraction',
        'simpleChoice.orderInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/choices/SimpleChoice.OrderInteraction',
        'hottext' : 'taoQtiItem/qtiCommonRenderer/renderers/choices/Hottext',
        'gap' : 'taoQtiItem/qtiCommonRenderer/renderers/choices/Gap',
        'gapText' : 'taoQtiItem/qtiCommonRenderer/renderers/choices/GapText',
        'simpleAssociableChoice.matchInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/choices/SimpleAssociableChoice.MatchInteraction',
        'simpleAssociableChoice.associateInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/choices/SimpleAssociableChoice.AssociateInteraction',
        'inlineChoice' : 'taoQtiItem/qtiCommonRenderer/renderers/choices/InlineChoice',
        'hottextInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/HottextInteraction',
        'hotspotInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/HotspotInteraction',
        'gapMatchInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/GapMatchInteraction',
        'selectPointInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/SelectPointInteraction',
        'graphicOrderInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicOrderInteraction',
        'mediaInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/MediaInteraction',
        'uploadInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/UploadInteraction',
        'graphicGapMatchInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicGapMatchInteraction',
        'gapImg' : 'taoQtiItem/qtiCommonRenderer/renderers/choices/GapImg',
        'graphicAssociateInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicAssociateInteraction',
        'customInteraction' : 'taoQtiItem/qtiCommonRenderer/renderers/interactions/CustomInteraction.amd'
    });
    return {
        name:'commonRenderer',
        locations : locations
    };
});




