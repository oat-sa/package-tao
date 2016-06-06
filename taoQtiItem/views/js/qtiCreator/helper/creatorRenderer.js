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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCreator/renderers/Renderer',
    'taoItems/assets/manager',
    'taoItems/assets/strategies',
    'helpers',
    'util/dom'
], function($, _, Renderer, assetManagerFactory, assetStrategies, helpers, dom){
    "use strict";

    //configure and instanciate once only:
    var _creatorRenderer = null;

    //list of configurable interactions
    //some interactions allow additional non-standard but sometimes useful configuration
    var _configurableInteractions = ['endAttempt'];

    /**
     * Extract interaction-specific configuration from the main one
     *
     * @param {object} config - the configuration object of the creatorRenderer
     * @returns {module.exports.properties|Function.properties|config.properties}
     */
    function _extractInteractionsConfig(config){
        var ret = {};
        if(config && config.properties){
            _.each(_configurableInteractions, function(interactionName){
                if(config.properties[interactionName]){
                    ret[interactionName] = config.properties[interactionName];
                }
            });
        }
        return ret;
    }

    /**
     * Get a preconfigured renderer singleton
     *
     * @param {Boolean} reset
     * @param {Object} config
     * @returns {Object} - a configured instance of creatorRenderer
     */
    var get = function(reset, config){
        var assetManager,
            $bodyEltForm;

        if(!_creatorRenderer || reset){

            $bodyEltForm = _creatorRenderer ? _creatorRenderer.getOption('bodyElementOptionForm') : null;
            if(reset ||
                !$bodyEltForm ||
                !$bodyEltForm.length ||
                !dom.contains($bodyEltForm)){

                _creatorRenderer = new Renderer({
                    //assetManager : assetManager,
                    lang : '',
                    uri : '',
                    shuffleChoices : false,
                    itemOptionForm : $('#item-editor-item-property-bar .panel'),
                    interactionOptionForm : $('#item-editor-interaction-property-bar .panel'),
                    choiceOptionForm : $('#item-editor-choice-property-bar .panel'),
                    responseOptionForm : $('#item-editor-response-property-bar .panel'),
                    bodyElementOptionForm : $('#item-editor-body-element-property-bar .panel'),
                    textOptionForm : $('#item-editor-text-property-bar .panel'),
                    modalFeedbackOptionForm : $('#item-editor-modal-feedback-property-bar .panel'),
                    mediaManager : {
                        appendContainer : '#mediaManager',
                        browseUrl : helpers._url('files', 'ItemContent', 'taoItems'),
                        uploadUrl : helpers._url('upload', 'ItemContent', 'taoItems'),
                        deleteUrl : helpers._url('delete', 'ItemContent', 'taoItems'),
                        downloadUrl : helpers._url('download', 'ItemContent', 'taoItems'),
                        fileExistsUrl : helpers._url('fileExists', 'ItemContent', 'taoItems'),
                        mediaSourcesUrl : config.properties.mediaSourcesUrl
                    },
                    interactions : _extractInteractionsConfig(config)
                });

                //update the resolver baseUrl
                _creatorRenderer.getAssetManager().setData({baseUrl : config.properties.baseUrl || '' });

            }
        }

        return _creatorRenderer;
    };


    return {
        get : function(reset, config){
            return get(reset, config);
        },
        setOption : function(name, value){
            return get().setOption(name, value);
        },
        setOptions : function(options){
            return get().setOptions(options);
        },
        load : function(qtiClasses, done){
            return get().load(function(){
                if(_.isFunction(done)){
                    done.apply(this, arguments);
                }
            }, qtiClasses);
        }
    };

});
