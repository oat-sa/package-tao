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
'lodash',
'taoQtiItem/qtiCommonRenderer/renderers/interactions/ExtendedTextInteraction',
'taoQtiItem/qtiCreator/widgets/interactions/extendedTextInteraction/Widget'
], function(_, ExtendedTextInteraction,ExtendedTextInteractionWidget){
    'use strict';

    var CreatorExtendedTextInteraction = _.clone(ExtendedTextInteraction);

    CreatorExtendedTextInteraction.render = function(interaction, options) {

        ExtendedTextInteraction.render(interaction);

        ExtendedTextInteractionWidget.build(
            interaction,
            ExtendedTextInteraction.getContainer(interaction),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorExtendedTextInteraction;
});
