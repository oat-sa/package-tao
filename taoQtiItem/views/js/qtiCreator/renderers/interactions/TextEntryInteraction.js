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

define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/TextEntryInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/textEntryInteraction/Widget',
    'tpl!taoQtiItem/qtiCreator/tpl/inlineInteraction/textEntryInteraction.placeholder'
], function($, _, TextEntryInteraction, TextEntryInteractionWidget, tpl){
    'use strict';

    var CreatorTextEntryInteraction = _.clone(TextEntryInteraction);

    CreatorTextEntryInteraction.template = tpl;

    CreatorTextEntryInteraction.render = function(interaction, options){

        //need to pass choice option form to the interaction widget because it will manage everything
        options = options || {};

        TextEntryInteractionWidget.build(
            interaction,
            $('.textEntryInteraction-placeholder[data-serial="' + interaction.serial + '"]'),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorTextEntryInteraction;
});
