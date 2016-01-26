/**
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 */
define([
    'lodash',
    'context',
    'taoQtiItem/qtiCommonRenderer/renderers/config',
    'taoItems/assets/manager',
    'taoItems/assets/strategies'
], function(_,  context, commonRenderConfig, assetManagerFactory, assetStrategies){
    'use strict';

    //asset manager using base url
    var assetManager = assetManagerFactory([
        assetStrategies.taomedia,
        assetStrategies.external,
        assetStrategies.base64,
        assetStrategies.baseUrl
    ], {baseUrl : ''});

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
        'customInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/PortableCustomInteraction',
        'endAttemptInteraction' : 'taoQtiItem/qtiCreator/renderers/interactions/EndAttemptInteraction',
        'infoControl' : 'taoQtiItem/qtiCreator/renderers/PortableInfoControl',
        'include' : 'taoQtiItem/qtiCreator/renderers/Include',
        'gap' : 'taoQtiItem/qtiCreator/renderers/choices/Gap',
        'gapText' : 'taoQtiItem/qtiCreator/renderers/choices/GapText',
        'hottext' : 'taoQtiItem/qtiCreator/renderers/choices/Hottext'
    }, commonRenderConfig.locations);

    return {
        name : 'creatorRenderer',
        locations : locations,
        options : {
            assetManager : assetManager
        }
    };
});
