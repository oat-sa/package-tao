/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2014-2015 (original work) Open Assessment Technlogies SA;
 *
 */

/**
 * Config of the QTI XML renderer
 *
 * @author Sam Sipasseuth <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'taoItems/assets/manager'
], function(assetManagerFactory){
    'use strict';

    //all assets are normalized (relative)
    var assetManager = assetManagerFactory([{
        name : 'nomalize',
        handle: function normalizeStrategy(url){
            if(url){
                return url.toString().replace(/^\.?\//, '');
            }
        }
    }]);

    /**
     * The XML Renderer config
     */
    return {
        name : 'xmlRenderer',
        locations : {
            '_container' : 'taoQtiItem/qtiXmlRenderer/renderers/Container',
            'assessmentItem' : 'taoQtiItem/qtiXmlRenderer/renderers/Item',
            'stylesheet' : 'taoQtiItem/qtiXmlRenderer/renderers/Stylesheet',
            'responseDeclaration' : 'taoQtiItem/qtiXmlRenderer/renderers/ResponseDeclaration',
            'outcomeDeclaration' : 'taoQtiItem/qtiXmlRenderer/renderers/OutcomeDeclaration',
            'responseProcessing' : 'taoQtiItem/qtiXmlRenderer/renderers/ResponseProcessing',
            '_simpleFeedbackRule' : 'taoQtiItem/qtiXmlRenderer/renderers/responses/SimpleFeedbackRule',
            'math' : 'taoQtiItem/qtiXmlRenderer/renderers/Math',
            'img' : 'taoQtiItem/qtiXmlRenderer/renderers/Img',
            'object' : 'taoQtiItem/qtiXmlRenderer/renderers/Object',
            'modalFeedback' : 'taoQtiItem/qtiXmlRenderer/renderers/feedbacks/ModalFeedback',
            'rubricBlock' : 'taoQtiItem/qtiXmlRenderer/renderers/RubricBlock',
            'associateInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/AssociateInteraction',
            'choiceInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/ChoiceInteraction',
            'extendedTextInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/ExtendedTextInteraction',
            'gapMatchInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/GapMatchInteraction',
            'graphicAssociateInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/GraphicAssociateInteraction',
            'graphicGapMatchInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/GraphicGapMatchInteraction',
            'graphicOrderInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/GraphicOrderInteraction',
            'hotspotInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/HotspotInteraction',
            'hottextInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/HottextInteraction',
            'inlineChoiceInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/InlineChoiceInteraction',
            'matchInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/MatchInteraction',
            'mediaInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/MediaInteraction',
            'orderInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/OrderInteraction',
            'selectPointInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/SelectPointInteraction',
            'sliderInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/SliderInteraction',
            'textEntryInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/TextEntryInteraction',
            'uploadInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/UploadInteraction',
            'prompt' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/Prompt',
            'associableHotspot' : 'taoQtiItem/qtiXmlRenderer/renderers/choices/AssociableHotspot',
            'gap' : 'taoQtiItem/qtiXmlRenderer/renderers/choices/Gap',
            'gapImg' : 'taoQtiItem/qtiXmlRenderer/renderers/choices/GapImg',
            'gapText' : 'taoQtiItem/qtiXmlRenderer/renderers/choices/GapText',
            'hotspotChoice' : 'taoQtiItem/qtiXmlRenderer/renderers/choices/HotspotChoice',
            'hottext' : 'taoQtiItem/qtiXmlRenderer/renderers/choices/Hottext',
            'inlineChoice' : 'taoQtiItem/qtiXmlRenderer/renderers/choices/InlineChoice',
            'simpleAssociableChoice' : 'taoQtiItem/qtiXmlRenderer/renderers/choices/SimpleAssociableChoice',
            'simpleChoice' : 'taoQtiItem/qtiXmlRenderer/renderers/choices/SimpleChoice',
            'customInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/PortableCustomInteraction',
            'endAttemptInteraction' : 'taoQtiItem/qtiXmlRenderer/renderers/interactions/EndAttemptInteraction',
            'infoControl' : 'taoQtiItem/qtiXmlRenderer/renderers/PortableInfoControl',
            'include' : 'taoQtiItem/qtiXmlRenderer/renderers/Include'
        },
        options : {
            assetManager : assetManager
        }
    };
});
