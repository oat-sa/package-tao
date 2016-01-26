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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Map',
    'taoQtiItem/qtiCreator/widgets/interactions/choiceInteraction/ResponseWidget',
    'lodash'
], function(stateFactory, Map, responseWidget, _){

    'use strict';

    var ChoiceInteractionStateMap = stateFactory.create(Map, function(){

        var _widget = this.widget,
            interaction = _widget.element,
            response = interaction.getResponseDeclaration();

        //init response widget in responseMapping mode:
        responseWidget.create(_widget, true);

        //finally, apply defined correct response and response mapping:
        responseWidget.setResponse(interaction, _.values(response.getCorrect()));

    }, function(){

        responseWidget.destroy(this.widget);

    });

    return ChoiceInteractionStateMap;
});