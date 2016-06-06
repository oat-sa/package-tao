/**
 * Define the location of all qti classes used in the QTI Creator
 */
define(['lodash', 'taoQtiItem/qtiItem/core/qtiClasses'], function(_, qtiClasses){
    "use strict";
    //clone the qtiClasses instead of modifying it by direct extend:
    return _.defaults({
        'assessmentItem' : 'taoQtiItem/qtiCreator/model/Item',
        '_container' : 'taoQtiItem/qtiCreator/model/Container',
        'img' : 'taoQtiItem/qtiCreator/model/Img',
        'math' : 'taoQtiItem/qtiCreator/model/Math',
        'object' : 'taoQtiItem/qtiCreator/model/Object',
        'rubricBlock' : 'taoQtiItem/qtiCreator/model/RubricBlock',
        'modalFeedback' : 'taoQtiItem/qtiCreator/model/feedbacks/ModalFeedback',
        'choiceInteraction' : 'taoQtiItem/qtiCreator/model/interactions/ChoiceInteraction',
        'orderInteraction' : 'taoQtiItem/qtiCreator/model/interactions/OrderInteraction',
        'associateInteraction' : 'taoQtiItem/qtiCreator/model/interactions/AssociateInteraction',
        'matchInteraction' : 'taoQtiItem/qtiCreator/model/interactions/MatchInteraction',
        'inlineChoiceInteraction' : 'taoQtiItem/qtiCreator/model/interactions/InlineChoiceInteraction',
        'simpleChoice' : 'taoQtiItem/qtiCreator/model/choices/SimpleChoice',
        'simpleAssociableChoice' : 'taoQtiItem/qtiCreator/model/choices/SimpleAssociableChoice',
        'inlineChoice' : 'taoQtiItem/qtiCreator/model/choices/InlineChoice',
        'mediaInteraction' : 'taoQtiItem/qtiCreator/model/interactions/MediaInteraction',
        'uploadInteraction' : 'taoQtiItem/qtiCreator/model/interactions/UploadInteraction',
        'textEntryInteraction' : 'taoQtiItem/qtiCreator/model/interactions/TextEntryInteraction',
        'sliderInteraction' : 'taoQtiItem/qtiCreator/model/interactions/SliderInteraction',
        'extendedTextInteraction' : 'taoQtiItem/qtiCreator/model/interactions/ExtendedTextInteraction',
        'hotspotInteraction' : 'taoQtiItem/qtiCreator/model/interactions/HotspotInteraction',
        'selectPointInteraction' : 'taoQtiItem/qtiCreator/model/interactions/SelectPointInteraction',
        'graphicInteraction' : 'taoQtiItem/qtiCreator/model/interactions/GraphicOrderInteraction',
        'graphicAssociateInteraction' : 'taoQtiItem/qtiCreator/model/interactions/GraphicAssociateInteraction',
        'graphicGapMatchInteraction' : 'taoQtiItem/qtiCreator/model/interactions/GraphicGapMatchInteraction',
        'graphicOrderInteraction' : 'taoQtiItem/qtiCreator/model/interactions/GraphicOrderInteraction',
        'hotspotChoice' : 'taoQtiItem/qtiCreator/model/choices/HotspotChoice',
        'gapImg' : 'taoQtiItem/qtiCreator/model/choices/GapImg',
        'associableHotspot' : 'taoQtiItem/qtiCreator/model/choices/AssociableHotspot',
        'gapMatchInteraction' : 'taoQtiItem/qtiCreator/model/interactions/GapMatchInteraction',
        'hottextInteraction' : 'taoQtiItem/qtiCreator/model/interactions/HottextInteraction',
        'hottext' : 'taoQtiItem/qtiCreator/model/choices/Hottext',
        'gapText' : 'taoQtiItem/qtiCreator/model/choices/GapText',
        'gap' : 'taoQtiItem/qtiCreator/model/choices/Gap',
        'responseDeclaration' : 'taoQtiItem/qtiCreator/model/variables/ResponseDeclaration',
        'responseProcessing' : 'taoQtiItem/qtiCreator/model/ResponseProcessing',
        'customInteraction' : 'taoQtiItem/qtiCreator/model/interactions/PortableCustomInteraction',
        'endAttemptInteraction' : 'taoQtiItem/qtiCreator/model/interactions/EndAttemptInteraction',
        'infoControl' : 'taoQtiItem/qtiCreator/model/PortableInfoControl',
        'include' : 'taoQtiItem/qtiCreator/model/Include'
    }, qtiClasses);

});
