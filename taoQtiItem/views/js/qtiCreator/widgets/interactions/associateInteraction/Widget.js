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
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/associateInteraction/states/states',
    'taoQtiItem/qtiCommonRenderer/helpers/sizeAdapter'
], function (Widget, states, sizeAdapter) {

    'use strict';

    var AssociateInteractionWidget = Widget.clone();

    AssociateInteractionWidget.initCreator = function () {

        this.registerStates(states);

        Widget.initCreator.call(this);

        sizeAdapter.adaptSize(this);

    };

    return AssociateInteractionWidget;
});
