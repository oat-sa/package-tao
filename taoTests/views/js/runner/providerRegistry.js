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
 * Enable to register providers to a target.
 *
 * TODO generalize (core)
 *
 * @author Sam <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['lodash'], function(_){
    'use strict';

    /**
     * Transfor the target into a a provider registry
     * It adds two methods registerProvider() and getProvider();
     *
     * @todo useful to move it to tao/core ?
     * @param {Object} target
     * @param {Function} [validator] - a function to validate the provider to be registered
     * @returns {target} the target itself
     */
    function providerRegistry(target, validator){

        var _providers = {};
        target = target || {};


        /**
        * Register an <i>provider</i> into the provider registry.
        * The provider provides the behavior required by the target object.
        *
        * @param {String} name - the provider name will be used to select the provider while instantiating the target object
        *
        * @param {Object} provider - the Item Runtime Provider as a plain object. The target object forwards, encapsulates and delegates calls to the provider.
        * @param {InitItemProvider} provider.init - the provider initializes the target object from it's config
        *
        * @throws TypeError when a wrong provider is given or an empty name.
        */
        function registerProvider(name, provider){

            var valid = true;

            //type checking
            if(!_.isString(name) || name.length <= 0){
                throw new TypeError('It is required to give a name to your provider.');
            }
            if(!_.isPlainObject(provider) || (!_.isFunction(provider.init))){
                throw new TypeError('A provider is an object that contains at least an init function.');
            }
            valid = validator && _.isFunction(validator) ? validator(provider) : valid;

            if(valid){
                _providers[name] = provider;
            }
        }

        /**
         * Get a registered provider by its name
         * @param {String} providerName
         * @returns {Object} provider
         */
        function getProvider(providerName){

            var provider;

            //check a provider is available
            if(!_providers || _.size(_providers) === 0){
                throw new Error('No provider regitered');
            }

            if(_.isString(providerName) && providerName.length > 0){
                provider = _providers[providerName];
            } else if(_.size(_providers) === 1) {

                //if there is only one provider, then we take this one
                providerName = _.keys(_providers)[0];
                provider = _providers[providerName];
            }

            //now we should have a provider
            if(!provider){
                throw new Error('No candidate found for the provider');
            }

            return provider;
        }

        /**
         * Clear the registered providers
         */
        function clearProviders(){
            _providers = {};
        }

        target.registerProvider = registerProvider;
        target.getProvider      = getProvider;
        target.clearProviders   = clearProviders;

        return target;
    }

    return providerRegistry;
});
