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
 * Copyright (c) 2014 (original work) Open Assessment Technlogies SA (under the project TAO-PRODUCT);
 *
 */

/**
 * @author Sam Sipasseuth <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'context',
    'core/promise',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/customInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiCommonRenderer/helpers/PortableElement',
    'qtiCustomInteractionContext',
    'taoQtiItem/qtiItem/helper/util'
], function(_, context, Promise, tpl, containerHelper, PortableElement, qtiCustomInteractionContext, util){
    'use strict';

    /**
     * Get the PCI instance associated to the interaction object
     * If none exists, create a new one based on the PCI typeIdentifier
     *
     * @param {Object} interaction - the js object representing the interaction
     * @returns {Object} PCI instance
     */
    var _getPci = function(interaction){

        var pciTypeIdentifier,
            pci = interaction.data('pci') || undefined;

        if(!pci){

            pciTypeIdentifier = interaction.typeIdentifier;
            pci = qtiCustomInteractionContext.createPciInstance(pciTypeIdentifier);

            if(pci){

                //binds the PCI instance to TAO interaction object and vice versa
                interaction.data('pci', pci);
                pci._taoCustomInteraction = interaction;

            }else{
                throw 'no custom interaction hook found for the type ' + pciTypeIdentifier;
            }
        }

        return pci;
    };

    /**
     * Execute javascript codes to bring the interaction to life.
     * At this point, the html markup must already be ready in the document.
     *
     * It is done in 5 steps :
     * 1. configure the paths
     * 2. require all required libs
     * 3. create a pci instance based on the interaction model
     * 4. initialize the rendering
     * 5. restore full state if applicable (state and/or response)
     *
     * @param {Object} interaction
     */
    var render = function(interaction, options){
        var self = this;

        options = options || {};
        return new Promise(function(resolve, reject){
            var runtimeLocation;
            var state              = {}; //@todo pass state and response to renderer here:
            var localRequireConfig = { paths : {} };
            var response           = { base : null};
            var id                 = interaction.attr('responseIdentifier');
            var typeIdentifier     = interaction.typeIdentifier;
            var runtimeLocations   = options.runtimeLocations ? options.runtimeLocations : self.getOption('runtimeLocations');
            var config             = _.clone(interaction.properties); //pass a clone instead
            var $dom               = containerHelper.get(interaction).children();
            var entryPoint         = interaction.entryPoint.replace(/\.js$/, '');   //ensure it's an AMD module

            //update config with runtime paths
            if(runtimeLocations && runtimeLocations[typeIdentifier]){
                runtimeLocation = runtimeLocations[typeIdentifier];
            } else{
                runtimeLocation = self.getAssetManager().resolveBy('portableElementLocation', typeIdentifier);
            }
            if(runtimeLocation){
                localRequireConfig.paths[typeIdentifier] = runtimeLocation;
                require.config(localRequireConfig);
            }

            //load the entrypoint
            require([entryPoint], function(){

                var pci = _getPci(interaction);
                if(pci){
                    //call pci initialize() to render the pci
                    pci.initialize(id, $dom[0], config);
                    //restore context (state + response)
                    pci.setSerializedState(state);
                    pci.setResponse(response);

                    //forward internal PCI event responseChange
                    interaction.onPci('responseChange', function(){
                        containerHelper.triggerResponseChangeEvent(interaction);
                    });

                    return resolve();
                }

                return reject('Unable to initiliaze pci : ' + id);

            }, reject);
        });
    };

    /**
     * Programmatically set the response following the json schema described in
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
     *
     * @param {Object} interaction
     * @param {Object} response
     */
    var setResponse = function(interaction, response){
        _getPci(interaction).setResponse(response);
    };

    /**
     * Get the response in the json format described in
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
     *
     * @param {Object} interaction
     * @returns {Object}
     */
    var getResponse = function(interaction){
        return _getPci(interaction).getResponse();
    };

    /**
     * Remove the current response set in the interaction
     * The state may not be restored at this point.
     *
     * @param {Object} interaction
     */
    var resetResponse = function(interaction){
        _getPci(interaction).resetResponse();
    };

    /**
     * Reverse operation performed by render()
     * After this function is executed, only the inital naked markup remains
     * Event listeners are removed and the state and the response are reset
     *
     * @param {Object} interaction
     */
    var destroy = function(interaction){
        _getPci(interaction).destroy();
    };

    /**
     * Restore the state of the interaction from the serializedState.
     *
     * @param {Object} interaction
     * @param {Object} serializedState - json format
     */
    var setSerializedState = function(interaction, serializedState){
        _getPci(interaction).setSerializedState(serializedState);
    };

    /**
     * Get the current state of the interaction as a string.
     * It enables saving the state for later usage.
     *
     * @param {Object} interaction
     * @returns {Object} json format
     */
    var getSerializedState = function(interaction){
        return _getPci(interaction).getSerializedState();
    };

    return {
        qtiClass : 'customInteraction',
        template : tpl,
        getData : function(customInteraction, data){

            //remove ns + fix media file path
            var markup = data.markup;
            markup = util.removeMarkupNamespaces(markup);
            markup = PortableElement.fixMarkupMediaSources(markup, this);
            data.markup = markup;

            return data;
        },
        render : render,
        getContainer : containerHelper.get,
        setResponse : setResponse,
        getResponse : getResponse,
        resetResponse : resetResponse,
        destroy : destroy,
        getSerializedState : getSerializedState,
        setSerializedState : setSerializedState
    };
});
