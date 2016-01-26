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
 * A factory to create a QTI renderer.
 *
 * @author Sam Sipasseuth <sam@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'jquery',
    'handlebars',
    'core/promise',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiItem/helper/interactionHelper',
    'ui/themeLoader'
], function(_, $, Handlebars, Promise, Element, interactionHelper, themeLoader){
    'use strict';

    var _isValidRenderer = function(renderer){

        var valid = true;

        if(typeof (renderer) !== 'object'){
            return false;
        }

        var classCorrect = false;
        if(renderer.qtiClass){
            if(_.indexOf(_renderableClasses, renderer.qtiClass) >= 0){
                classCorrect = true;
            }else{
                var pos = renderer.qtiClass.indexOf('.');
                if(pos > 0){
                    var qtiClass = renderer.qtiClass.slice(0, pos);
                    var subClass = renderer.qtiClass.slice(pos + 1);
                    if(_renderableSubclasses[qtiClass] && _.indexOf(_renderableSubclasses[qtiClass], subClass) >= 0){
                        classCorrect = true;
                    }
                }
            }
        }
        if(!classCorrect){
            valid = false;
            throw new Error('invalid qti class name in renderer declaration : ' + renderer.qtiClass);
        }

        if(!renderer.template){
            valid = false;
            throw new Error('missing template in renderer declaration : ' + renderer.qtiClass);
        }

        return valid;
    };

    var _renderableClasses = [
        '_container',
        'assessmentItem',
        'stylesheet',
        'responseDeclaration',
        'outcomeDeclaration',
        'responseProcessing',
        '_simpleFeedbackRule',
        'img',
        'math',
        'object',
        'modalFeedback',
        'rubricBlock',
        'associateInteraction',
        'choiceInteraction',
        'extendedTextInteraction',
        'gapMatchInteraction',
        'graphicAssociateInteraction',
        'graphicGapMatchInteraction',
        'graphicOrderInteraction',
        'hotspotInteraction',
        'hottextInteraction',
        'inlineChoiceInteraction',
        'matchInteraction',
        'mediaInteraction',
        'orderInteraction',
        'selectPointInteraction',
        'sliderInteraction',
        'textEntryInteraction',
        'uploadInteraction',
        'endAttemptInteraction',
        'customInteraction',
        'prompt',
        'associableHotspot',
        'gap',
        'gapImg',
        'gapText',
        'hotspotChoice',
        'hottext',
        'inlineChoice',
        'simpleAssociableChoice',
        'simpleChoice',
        'infoControl',
        'include'
    ];

    /**
     * The list of qti element dependencies. It is used internally to load dependent qti classes
     */
    var _dependencies = {
        assessmentItem : ['stylesheet', '_container', 'prompt', 'modalFeedback'],
        rubricBlock : ['_container'],
        associateInteraction : ['simpleAssociableChoice'],
        choiceInteraction : ['simpleChoice'],
        gapMatchInteraction : ['gap', 'gapText'],
        graphicAssociateInteraction : ['associableHotspot'],
        graphicGapMatchInteraction : ['associableHotspot', 'gapImg'],
        graphicOrderInteraction : ['hotspotChoice'],
        hotspotInteraction : ['hotspotChoice'],
        hottextInteraction : ['hottext'],
        inlineChoiceInteraction : ['inlineChoice'],
        matchInteraction : ['simpleAssociableChoice'],
        orderInteraction : ['simpleChoice']
    };

    /**
     * The list of supported qti subclasses.
     */
    var _renderableSubclasses = {
        'simpleAssociableChoice' : ['associateInteraction', 'matchInteraction'],
        'simpleChoice' : ['choiceInteraction', 'orderInteraction']
    };

    /**
     * List of the default properties for the item
     */
    var _defaults = {
        shuffleChoices : true
    };

    /**
     * Get the location of the document, useful to define a baseUrl for the required context
     * @returns {String}
     */
    function getDocumentBaseUrl(){
        return window.location.protocol + '//' + window.location.host + window.location.pathname.replace(/([^\/]*)$/, '');
    }

    /**
     * The built Renderer class
     * @constructor
     * @param {Object} [options] - the renderer options
     * @param {AssetManager} [options.assetManager] - The renderer needs an AssetManager to resolve URLs (see {@link taoItems/assets/manager})
     * @param {Boolean} [options.shuffleChoices = true] - Does the renderer take care of the shuffling
     * @param {Object} [options.decorators] - to set up rendering decorator
     * @param {preRenderDecorator} [options.decorators.before] - to set up a pre decorator
     * @param {postRenderDecorator} [options.decorators.after] - to set up a post decorator
     */
    var Renderer = function(options){

        /**
         * Store the registered renderer location
         */
        var _locations = {};

        /**
         * Store loaded renderers
         */
        var _renderers = {};

        options = _.defaults(options || {}, _defaults);

        this.isRenderer = true;

        this.name = '';

        //store shuffled choice here
        this.shuffledChoices = [];

        /**
         * Get the actual renderer of the give qti class or subclass:
         * e.g. simplceChoice, simpleChoice.choiceInteraction, simpleChoice.orderInteraction
         */
        var _getClassRenderer = function(qtiClass){
            var ret = null;
            if(_renderers[qtiClass]){
                ret = _renderers[qtiClass];
            }else{
                var pos = qtiClass.indexOf('.');
                if(pos > 0){
                    qtiClass = qtiClass.slice(0, pos);
                    if(_renderers[qtiClass]){
                        ret = _renderers[qtiClass];
                    }
                }
            }
            return ret;
        };

        /**
         * Set the renderer options
         * @param {String} key - the name of the option
         * @param {*} value - the option vallue
         * @returns {Renderer} for chaining
         */
        this.setOption = function(key, value){
            if(typeof (key) === 'string'){
                options[key] = value;
            }
            return this;
        };

        /**
         * Set the renderer options
         * @param {Object} opts - the options
         * @returns {Renderer} for chaining
         */
        this.setOptions = function(opts){
            options = _.extend(options, opts);
            return this;
        };

        /**
         * Get the renderer option
         * @param {String} key - the name of the option
         * @returns {*|null} the option vallue
         */
        this.getOption = function(key){
            if(typeof (key) === 'string' && options[key]){
                return options[key];
            }
            return null;
        };

        /**
         * Get the bound assetManager
         * @returns {AssetManager} the assetManager
         */
        this.getAssetManager = function getAssetManager(){
            return options.assetManager;
        };

        /**
         * Get the bound theme loader
         * @returns {Object} the themeLoader
         */
        this.getThemeLoader = function getThemeLoader(){
            return this.themeLoader;
        };


        /**
         * Renders the template
         * @param {Object} element - the QTI model element
         * @param {Object} data - the data to give to the template
         * @param {String} [qtiSubclass] - to get the render of the element subclass (when element's qtiClass is abstract)
         * @returns {String} the template results
         * @throws {Error} if the renderer is not set or has no template bound
         */
        this.renderTpl = function(element, data, qtiSubclass){

            var res;
            var ret        = '';
            var tplFound   = false;
            var qtiClass   = qtiSubclass || element.qtiClass;
            var renderer   = _getClassRenderer(qtiClass);
            var decorators = this.getOption('decorators');

            if(!renderer || !_.isFunction(renderer.template)){
                throw new Error('no renderer template loaded under the class name : ' + qtiClass);
            }

            //pre rendering decoration
            if(_.isObject(decorators) && _.isFunction(decorators.before)){

                /**
                 * @callback preRenderDecoractor
                 * @param {Object} element - the QTI model element
                 * @param {String} [qtiSubclass] - to get the render of the element subclass (when element's qtiClass is abstract)
                 * @returns {String} the decorator result
                 */
                res = decorators.before(element, qtiSubclass);
                if(_.isString(res)){
                    ret += res;
                }
            }

            //render the template
            ret += renderer.template(data);


            //post rendering decoration
            if(_.isObject(decorators) && _.isFunction(decorators.after)){

                /**
                 * @callback postRenderDecoractor
                 * @param {Object} element - the QTI model element
                 * @param {String} [qtiSubclass] - to get the render of the element subclass (when element's qtiClass is abstract)
                 * @returns {String} the decorator result
                 */
                res = decorators.after(element, qtiSubclass);
                if(_.isString(res)){
                    ret += res;
                }
            }
            return ret;
        };

        this.getData = function(element, data, qtiSubclass){

            var ret = data,
                qtiClass = qtiSubclass || element.qtiClass,
                renderer = _getClassRenderer(qtiClass);

            if(renderer){
                if(typeof (renderer.getData) === 'function'){
                    ret = renderer.getData.call(this, element, data);
                }
            }

            return ret;
        };

        this.renderDirect = function(tpl, data){
            return Handlebars.compile(tpl)(data);
        };

        this.getContainer = function(qtiElement, $scope, qtiSubclass){

            var ret = null,
                qtiClass = qtiSubclass || qtiElement.qtiClass,
                renderer = _getClassRenderer(qtiClass);

            if(renderer){
                ret = renderer.getContainer(qtiElement, $scope);
            }else{
                throw 'no renderer found for the class : ' + qtiElement.qtiClass;
            }
            return ret;
        };

        this.postRender = function(qtiElement, data, qtiSubclass){

            var qtiClass = qtiSubclass || qtiElement.qtiClass;
            var renderer = _getClassRenderer(qtiClass);

            if(renderer && typeof (renderer.render) === 'function') {
                return renderer.render.call(this, qtiElement, data);
            }
        };

        this.setResponse = function(qtiInteraction, response, qtiSubclass){

            var ret = false,
                qtiClass = qtiSubclass || qtiInteraction.qtiClass,
                renderer = _getClassRenderer(qtiClass);

            if(renderer){
                if(typeof (renderer.setResponse) === 'function'){
                    ret = renderer.setResponse.call(this, qtiInteraction, response);
                    var $container = renderer.getContainer.call(this, qtiInteraction);
                    if($container instanceof $ && $container.length){
                        $container.trigger('responseSet', [qtiInteraction, response]);
                    }
                }
            }else{
                throw 'no renderer registered under the name : ' + qtiClass;
            }
            return ret;
        };

        this.getResponse = function(qtiInteraction, qtiSubclass){

            var ret = false,
                qtiClass = qtiSubclass || qtiInteraction.qtiClass,
                renderer = _getClassRenderer(qtiClass);

            if(renderer){
                if(typeof (renderer.getResponse) === 'function'){
                    ret = renderer.getResponse.call(this, qtiInteraction);
                }
            }else{
                throw 'no renderer registered under the name : ' + qtiClass;
            }
            return ret;
        };

        this.resetResponse = function(qtiInteraction, qtiSubclass){

            var ret = false,
                qtiClass = qtiSubclass || qtiInteraction.qtiClass,
                renderer = _getClassRenderer(qtiClass);

            if(renderer){
                if(typeof (renderer.resetResponse) === 'function'){
                    ret = renderer.resetResponse.call(this, qtiInteraction);
                }
            }else{
                throw 'no renderer registered under the name : ' + qtiClass;
            }
            return ret;
        };

        /**
         * Retrieve the state of the interaction.
         * If the renderer has no state management, it falls back to the response management.
         *
         * @param {Object} qtiInteraction - the interaction
         * @param {String} [qtiSubClass] - (not sure of the type and how it is used - Sam ? )
         * @returns {Object} the interaction's state
         *
         * @throws {Error} if no renderer is registered
         */
        this.getState = function(qtiInteraction, qtiSubclass){

            var ret = false;
            var qtiClass = qtiSubclass || qtiInteraction.qtiClass;
            var renderer = _getClassRenderer(qtiClass);

            if(renderer){
                if(_.isFunction(renderer.getState)){
                    ret = renderer.getState.call(this, qtiInteraction);
                } else {
                    ret = renderer.getResponse.call(this, qtiInteraction);
                }
            }else{
                throw 'no renderer registered under the name : ' + qtiClass;
            }
            return ret;
        };

        /**
         * Retrieve the state of the interaction.
         * If the renderer has no state management, it falls back to the response management.
         *
         * @param {Object} qtiInteraction - the interaction
         * @param {Object} state - the interaction's state
         * @param {String} [qtiSubClass] - (not sure of the type and how it is used - Sam ? )
         *
         * @throws {Error} if no renderer is found
         */
        this.setState =  function(qtiInteraction, state, qtiSubclass){

            var qtiClass = qtiSubclass || qtiInteraction.qtiClass;
            var renderer = _getClassRenderer(qtiClass);

            if(renderer){
                if(_.isFunction(renderer.setState)){
                    renderer.setState.call(this, qtiInteraction, state);
                } else {
                    renderer.resetResponse.call(this, qtiInteraction);
                    renderer.setResponse.call(this, qtiInteraction, state);
                }
            }else{
                throw 'no renderer registered under the name : ' + qtiClass;
            }
        };

        /**
         * Calls the renderer destroy.
         * Ask the renderer to run destroy if exists.
         *
         * @throws {Error} if no renderer is found
         */
        this.destroy = function(qtiInteraction, qtiSubclass){

            var ret = false,
                qtiClass = qtiSubclass || qtiInteraction.qtiClass,
                renderer = _getClassRenderer(qtiClass);

            if(renderer){
                if(_.isFunction(renderer.destroy)){
                    ret = renderer.destroy.call(this, qtiInteraction);
                }
            }else{
                throw 'no renderer registered under the name : ' + qtiClass;
            }
            return ret;
        };

        this.getLoadedRenderers = function(){
            return _renderers;
        };

        this.register = function(renderersLocations){
            _.extend(_locations, renderersLocations);
        };

        this.load = function(callback, requiredClasses){
           var self = this;
            var required = [];

            if(options.themes){

                //resolve themes paths
                options.themes.base = this.resolveUrl(options.themes.base);
                _.forEach(options.themes.available, function(theme, index){
                    options.themes.available[index].path = self.resolveUrl(theme.path);
                });
                this.themeLoader = themeLoader(options.themes).load();
            }

            if(requiredClasses){
                if(_.isArray(requiredClasses)){

                    //exclude abstract classes
                    requiredClasses = _.intersection(requiredClasses, _renderableClasses);

                    //add dependencies
                    _.each(requiredClasses, function(reqClass){
                        var deps = _dependencies[reqClass];
                        if(deps){
                            requiredClasses = _.union(requiredClasses, deps);
                        }
                    });

                    _.forEach(requiredClasses, function(qtiClass){

                        if(_renderableSubclasses[qtiClass]){
                            var requiredSubClasses = _.intersection(requiredClasses, _renderableSubclasses[qtiClass]);
                            _.each(requiredSubClasses, function(subclass){
                                if(_locations[qtiClass + '.' + subclass]){
                                    required.push(_locations[qtiClass + '.' + subclass]);
                                }else if(_locations[qtiClass]){
                                    required.push(_locations[qtiClass]);
                                }else{
                                    throw new Error(self.name + ' : missing qti class location declaration: ' + qtiClass + ', subclass: ' + subclass);
                                }
                            });
                        } else {
                            if(_locations[qtiClass] === false){
                                _renderers[qtiClass] = false;//mark this class as not renderable
                            }else if(_locations[qtiClass]){
                                required.push(_locations[qtiClass]);
                            }else{
                                throw new Error(self.name + ' : missing qti class location declaration: ' + qtiClass);
                            }
                        }
                    });
                } else {
                    throw new Error('invalid argument type: expected array for arg "requireClasses"');
                }

            }else{
                required = _.values(_locations);
            }

            var _this = this;
            require(required, function(){

                _.each(arguments, function(clazz){
                    if(_isValidRenderer(clazz)){
                        _renderers[clazz.qtiClass] = clazz;
                    }
                });

                if(typeof (callback) === 'function'){
                    callback.call(_this, _renderers);
                }
            });

            return this;
        };

        /**
         * Unload the renderer
         */
        this.unload = function unload(){
            if(this.themeLoader){
                themeLoader(options.themes).unload();
            }
            return this;
        };

        /**
         * Define the shuffling manually
         *
         * @param {Object} interaction - the interaction
         * @param {Array} choices - the shuffled choices
         * @param {String} identificationType -
         */
        this.setShuffledChoices = function(interaction, choices, identificationType){
            if(Element.isA(interaction, 'interaction')){
                this.shuffledChoices[interaction.getSerial()] = _.pluck(interactionHelper.findChoices(interaction, choices, identificationType), 'serial');
            }
        };

        /**
         * Get the choices shuffled for this interaction.
         *
         * @param {Object} interaction - the interaction
         * @param {Boolean} reshuffle - by default choices are shuffled only once and store, but if true you can force shuffling again
         * @param {String} returnedType - the choice type
         * @returns {Array} the choices
         */
        this.getShuffledChoices = function(interaction, reshuffle, returnedType){
            var choices = [];
            var shuffled = [];
            var serial, i;

            if(Element.isA(interaction, 'interaction')){
                serial = interaction.getSerial();

                //1st time, we shuffle (or asked to force shuffling)
                if(!this.shuffledChoices[serial] || reshuffle){
                    if(Element.isA(interaction, 'matchInteraction')){
                        this.shuffledChoices[serial] = [];
                        for(i = 0; i < 2; i++){
                            choices[i] = interactionHelper.shuffleChoices(interaction.getChoices(i));
                            this.shuffledChoices[serial][i] = _.pluck(choices[i], 'serial');
                        }
                    } else {
                        choices = interactionHelper.shuffleChoices(interaction.getChoices());
                        this.shuffledChoices[serial] = _.pluck(choices, 'serial');
                    }

                //otherwise get the choices from the serials
                } else {
                    if(Element.isA(interaction, 'matchInteraction')){
                        _.forEach(choices, function(choice, index){
                            if(index < 2){
                                _.forEach(this.shuffledChoices[serial][i], function(choiceSerial){
                                    choice.push(interaction.getChoice(choiceSerial));
                                });
                            }
                        });
                    } else {
                        _.forEach(this.shuffledChoices[serial], function(choiceSerial){
                            choices.push(interaction.getChoice(choiceSerial));
                        });
                    }
                }

                //do some type convertion
                if(returnedType === 'serial' || returnedType === 'identifier'){
                    return interactionHelper.convertChoices(choices, returnedType);
                }

                //pass value only, not ref
                return _.clone(choices);
            }

            return [];
        };

        this.getRenderers = function(){
            return _renderers;
        };

        this.getLocations = function(){
            return _locations;
        };

        /**
         * Resolve URLs using the defined assetManager's strategies
         * @param {String} url - the URL to resolve
         * @returns {String} the resolved URL
         */
        this.resolveUrl = function resolveUrl(url){
            if(!options.assetManager){
                return url;
            }
            if(typeof url === 'string' && url.length > 0){
                return options.assetManager.resolve(url);
            }
        };

        /**
         * @deprecated in favor of resolveUrl
         */
        this.getAbsoluteUrl = function(relUrl){

            //let until method is removed
            console.warn('DEPRECATED used getAbsoluteUrl with ', arguments);

            //allow relative url output only if explicitely said so
            if(this.getOption('userRelativeUrl')){
                return relUrl.replace(/^\.?\//, '');
            }

            if(/^http(s)?:\/\//i.test(relUrl) || /^data:[^\/]+\/[^;]+(;charset=[\w]+)?;base64,/.test(relUrl)){
                //already absolute or base64 encoded
                return relUrl;
            }else{

                var absUrl = '';
                var runtimeLocations = this.getOption('runtimeLocations');

                if(runtimeLocations && _.size(runtimeLocations)){
                    _.forIn(runtimeLocations, function(runtimeLocation, typeIdentifier){
                        if(relUrl.indexOf(typeIdentifier) === 0){
                            absUrl = relUrl.replace(typeIdentifier, runtimeLocation);
                            return false;//break
                        }
                    });
                }

                if(absUrl){
                    return absUrl;
                }else{
                    var baseUrl = this.getOption('baseUrl') || getDocumentBaseUrl();
                    return baseUrl + relUrl;
                }

            }
        };
    };

    /**
     * Expose the renderer's factory
     * @exports taoQtiItem/qtiRunner/core/Renderer
     */
    return {

        /**
         * Creates a new Renderer by extending the Renderer's prototype
         * @param {Object} renderersLocations -
         * @param {String} [name] - the new renderer name
         * @param {Object} [defaultOptions] - the renderer options
         */
        build : function(renderersLocations, name, defaultOptions){
            var NewRenderer = function(){
                var options = _.isPlainObject(arguments[0]) ? arguments[0] : {};

                Renderer.apply(this);

                this.register(renderersLocations);
                this.name = name || '';
                this.setOptions(_.defaults(options, defaultOptions || {}));
            };
            NewRenderer.prototype = Renderer.prototype;
            return NewRenderer;
        }
    };
});
