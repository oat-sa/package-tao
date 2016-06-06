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
 * Portable Info Control Common Renderer
 */
define([
    'core/promise',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/infoControl',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'taoQtiItem/qtiCommonRenderer/helpers/PortableElement',
    'qtiInfoControlContext',
    'taoQtiItem/qtiItem/helper/util'
], function(Promise, tpl, containerHelper, PortableElement, qtiInfoControlContext, util){
    'use strict';

    /**
     * Get the PIC instance associated to the infoControl object
     * If none exists, create a new one based on the PIC typeIdentifier
     *
     * @param {Object} infoControl - the js object representing the infoControl
     * @returns {Object} PIC instance
     */
    var _getPic = function(infoControl){

        var typeIdentifier,
            pic = infoControl.data('pic') || undefined;

        if(!pic){

            typeIdentifier = infoControl.typeIdentifier;
            pic = qtiInfoControlContext.createPciInstance(typeIdentifier);

            if(pic){

                //binds the PIC instance to TAO infoControl object and vice versa
                infoControl.data('pic', pic);
                pic._taoInfoControl = infoControl;

            }else{
                throw 'no custom infoControl hook found for the type ' + typeIdentifier;
            }
        }

        return pic;
    };

    /**
     * Execute javascript codes to bring the infoControl to life.
     * At this point, the html markup must already be ready in the document.
     *
     * It is done in 5 steps :
     * 1. ensure the context is configured correctly
     * 2. require all required libs
     * 3. create a pic instance based on the infoControl model
     * 4. initialize the rendering
     * 5. restore full state if applicable
     *
     * @param {Object} infoControl
     * @param {Object} [options]
     * @param {Object} [options.runtimeLocations] - to set manually the runtime path of PIC
     */
    var render = function(infoControl, options){
        var self = this;
        options = options || {};

        return new Promise(function(resolve, reject){

            var runtimeLocation;
            var state              = {}; //@todo pass state and response to renderer here:
            var localRequireConfig = { paths : {} };
            var id                 = infoControl.attr('id');
            var typeIdentifier     = infoControl.typeIdentifier;
            var runtimeLocations   = options.runtimeLocations ? options.runtimeLocations : self.getOption('runtimeLocations');
            var config             = infoControl.properties;
            var $dom               = containerHelper.get(infoControl).children();
            var entryPoint         = infoControl.entryPoint.replace(/\.js$/, '');   //ensure the entry point is in AMD

            //update config
            if(runtimeLocations && runtimeLocations[typeIdentifier]){
                runtimeLocation = runtimeLocations[typeIdentifier];
            } else{
                //use the asset strategy named "portableElementLocation"
                runtimeLocation = self.getAssetManager().resolveBy('portableElementLocation', typeIdentifier);
            }
            if(runtimeLocation){
                localRequireConfig.paths[typeIdentifier] = runtimeLocation;
                require.config(localRequireConfig);
            }

            //load the entry point
            require([entryPoint], function(){

                var pic = _getPic(infoControl);
                if(pic && $dom.length){
                    //call pci initialize() to render the pci
                    pic.initialize(id, $dom[0], config);
                    //restore context (state + response)
                    pic.setSerializedState(state);
                    return resolve();
                }
                return reject('Unable to initiliaze infoControl : ' + id);
            }, reject);
        });
    };

    /**
     * Reverse operation performed by render()
     * After this function is executed, only the inital naked markup remains
     * Event listeners are removed and the state and the response are reset
     *
     * @param {Object} infoControl
     */
    var destroy = function destroy(infoControl){
        _getPic(infoControl).destroy();
    };

    /**
     * Restore the state of the infoControl from the serializedState.
     *
     * @param {Object} infoControl - the element instance
     * @param {Object} state - the state to set
     */
    var setState = function setState(infoControl, state){
        _getPic(infoControl).setSerializedState(state);
    };

    /**
     * Get the current state of the infoControl as a string.
     * It enables saving the state for later usage.
     *
     * @param {Object} infoControl - the element instance
     * @returns {Object} the state
     */
    var getState = function getState(infoControl){
        return _getPic(infoControl).getSerializedState();
    };

    return {
        qtiClass : 'infoControl',
        template : tpl,
        getData : function(infoControl, data){

            //remove ns + fix media file path
            var markup = data.markup;
            markup = util.removeMarkupNamespaces(markup);
            markup = PortableElement.fixMarkupMediaSources(markup, this);
            data.markup = markup;
            return data;
        },
        render : render,
        getContainer : containerHelper.get,
        destroy : destroy,
        getState : getState,
        setState : setState
    };
});
