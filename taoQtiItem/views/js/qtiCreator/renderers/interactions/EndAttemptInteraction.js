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
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/EndAttemptInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/endAttemptInteraction/Widget',
    'tpl!taoQtiItem/qtiCreator/tpl/inlineInteraction/endAttemptInteraction.placeholder'
], function($, _, EndAttemptInteraction, EndAttemptInteractionWidget, tpl){
    'use strict';

    var CreatorEndAttemptInteraction = _.clone(EndAttemptInteraction);

    CreatorEndAttemptInteraction.template = tpl;

    /**
     * Render the end attempt interaction widget
     *
     * @param {Object} interaction - the qti js object
     * @param {Object} options
     * @returns {Object}
     */
    CreatorEndAttemptInteraction.render = function(interaction, options){

        //need to pass choice option form to the interaction widget because it will manage everything
        options = options || {};

        //complete the option
        var interactionsConfig = this.getOption('interactions');
        if(interactionsConfig.endAttempt){
            options.config = interactionsConfig.endAttempt;
        }

        EndAttemptInteractionWidget.build(
            interaction,
            $('.endAttemptInteraction-placeholder[data-serial="' + interaction.serial + '"]'),
            this.getOption('interactionOptionForm'),
            this.getOption('responseOptionForm'),
            options
        );
    };

    return CreatorEndAttemptInteraction;
});
