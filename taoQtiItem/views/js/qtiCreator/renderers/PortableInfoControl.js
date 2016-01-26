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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 *
 */

/**
 * Portable Info Control Creator Renderer
 */
define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/PortableInfoControl',
    'taoQtiItem/qtiCreator/editor/infoControlRegistry',
    'taoQtiItem/qtiCreator/helper/commonRenderer'
], function(_, Renderer, icRegistry, commonRenderer){
    'use strict';

    //clone the common renderer
    var CreatorPortableInfoControl = _.clone(Renderer);

    /**
     * override the render method
     */
    CreatorPortableInfoControl.render = function render(infoControl, options){

        var pciCreator = icRegistry.getCreator(infoControl.typeIdentifier);
        var renderOptions = {
            runtimeLocations : {}
        };
        //set the runtime location manually
        renderOptions.runtimeLocations[infoControl.typeIdentifier] = icRegistry.getBaseUrl(infoControl.typeIdentifier);

        //initial rendering:
        Renderer.render.call(commonRenderer.get(), infoControl, renderOptions);

        pciCreator.getWidget().build(
            infoControl,
            Renderer.getContainer(infoControl),
            this.getOption('bodyElementOptionForm'),
            options
        );
    };

    return CreatorPortableInfoControl;
});
