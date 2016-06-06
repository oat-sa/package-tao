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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicGapMatchInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicGapMatchInteraction/Widget'
], function(_, GraphicGapMatchInteraction, GraphicGapMatchInteractionWidget){
    'use strict';

    /**
     * The CreatorGraphicGapMatchInteraction  Renderer
     * @extends taoQtiItem/qtiCommonRenderer/renderers/interactions/GraphicGapMatchInteraction
     * @exports taoQtiItem/qtiCreator/renderers/interactions/GraphicGapMatchInteraction
     */
    var CreatorGraphicGapMatchInteraction = _.clone(GraphicGapMatchInteraction);

    /**
     * Render the interaction for authoring.
     * It delegates the rendering to the widget layer.
     * @param {Object} interaction - the model
     * @param {Object} [options] - extra options
     */
    CreatorGraphicGapMatchInteraction.render = function(interaction, options){
        options = options || {};
        options.baseUrl = this.getOption('baseUrl');
        options.choiceForm = this.getOption('choiceOptionForm');
        options.uri = this.getOption('uri');
        options.lang = this.getOption('lang');
        options.mediaManager = this.getOption('mediaManager');
        options.assetManager = this.getAssetManager();

        GraphicGapMatchInteractionWidget.build(
            interaction,
            GraphicGapMatchInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorGraphicGapMatchInteraction;
});
