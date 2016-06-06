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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 *
 */
//@todo : move this to the ../helper directory
define(['lodash', 'class', 'taoQtiItem/qtiItem/core/qtiClasses', 'taoQtiItem/qtiItem/core/Element'], function(_, Class, qtiClasses, Element){

    var Loader = Class.extend({
        init : function(item, classesLocation){

            this.qti = {}; //loaded qti classes are store here
            this.classesLocation = {};

            this.item = item || null;//starts either from scratch or with an existing item object
            this.setClassesLocation(classesLocation || qtiClasses);//load default location for qti classes model
        },
        setClassesLocation : function(qtiClasses){
            _.extend(this.classesLocation, qtiClasses);
            return this;
        },
        getRequiredClasses : function(data){
            var ret = [];
            for(var i in data){
                if(i === 'qtiClass' && data[i] !== '_container' && i !== 'relatedItem'){//although a _container is a concrete class in TAO, it is not defined in QTI standard
                    ret.push(data[i]);
                }else if(typeof(data[i]) === 'object' && i !== 'responseRules'){    //responseRules should'nt be part of the parsing
                    ret = _.union(ret, this.getRequiredClasses(data[i]));
                }
            }
            return ret;
        },
        loadRequiredClasses : function(data, callback, reload){

            var requiredClasses = this.getRequiredClasses(data, reload), required = [];

            for(var i in requiredClasses){
                var requiredClass = requiredClasses[i];
                if(this.classesLocation[requiredClass]){
                    required.push(this.classesLocation[requiredClass]);
                }else{
                    throw new Error('missing qti class location declaration : ' + requiredClass);
                }
            }

            var _this = this;
            require(required, function(){
                _.each(arguments, function(QtiClass){
                    _this.qti[QtiClass.prototype.qtiClass] = QtiClass;
                });
                callback.call(_this, _this.qti);
            });
        },
        getLoadedClasses : function(){
            return _.keys(this.qti);
        },
        loadItemData : function(data, callback){

            var _this = this;
            _this.loadRequiredClasses(data, function(Qti){
                var i;
                if(typeof(data) === 'object' && data.qtiClass === 'assessmentItem'){

                    //unload an item from it's serial (in case of a reload)
                    if(data.serial){
                        Element.unsetElement(data.serial);
                    }

                    _this.item = new Qti.assessmentItem(data.serial, data.attributes || {});
                    _this.loadContainer(_this.item.getBody(), data.body);

                    for(i in data.outcomes){
                        var outcome = _this.buildOutcome(data.outcomes[i]);
                        if(outcome){
                            _this.item.addOutcomeDeclaration(outcome);
                        }
                    }
                    for(i in data.feedbacks){
                        var feedback = _this.buildElement(data.feedbacks[i]);
                        if(feedback){
                            _this.item.addModalFeedback(feedback);
                        }
                    }
                    for(i in data.stylesheets){
                        var stylesheet = _this.buildElement(data.stylesheets[i]);
                        if(stylesheet){
                            _this.item.addStylesheet(stylesheet);
                        }
                    }
                    
                    //important : build responses after all modal feedbacks and outcomes has been loaded, because the simple feedback rules need to reference them
                    for(i in data.responses){
                        var response = _this.buildResponse(data.responses[i]);
                        if(response){
                            _this.item.addResponseDeclaration(response);

                            var feedbackRules = data.responses[i].feedbackRules;
                            if(feedbackRules){
                                _.forIn(feedbackRules, function(fbData, serial){
                                    response.feedbackRules[serial] = _this.buildSimpleFeedbackRule(fbData, response);
                                });
                            }
                        }
                    }
                    
                    if(data.responseProcessing){
                        _this.item.setResponseProcessing(_this.buildResponseProcessing(data.responseProcessing));
                    }
                    _this.item.setNamespaces(data.namespaces);
                    _this.item.setSchemaLocations(data.schemaLocations);
                    _this.item.setApipAccessibility(data.apipAccessibility);
                }

                if(typeof(callback) === 'function'){
                    callback.call(_this, _this.item);
                }
            });
        },
        loadAndBuildElement : function(data, callback){

            var _this = this;

            _this.loadRequiredClasses(data, function(Qti){

                var element = _this.buildElement(data);

                if(typeof(callback) === 'function'){
                    callback.call(_this, element);
                }
            });
        },
        loadElement : function(element, data, callback){

            var _this = this;
            this.loadRequiredClasses(data, function(){
                _this.loadElementData(element, data);
                if(typeof(callback) === 'function'){
                    callback.call(_this, element);
                }
            });
        },
        /**
         * Load ALL given elements into existing loaded item
         *
         * @todo to be renamed to loadItemElements
         * @param {object} data
         * @param {function} callback
         * @returns {undefined}
         */
        loadElements : function(data, callback){

            var _this = this;

            if(_this.item){

                this.loadRequiredClasses(data, function(){

                    var allElements = _this.item.getComposingElements();

                    for(var i in data){
                        var elementData = data[i];
                        if(elementData && elementData.qtiClass && elementData.serial){
                            //find and update element
                            if(allElements[elementData.serial]){
                                _this.loadElementData(allElements[elementData.serial], elementData);
                            }
                        }
                    }

                    if(typeof(callback) === 'function'){
                        callback.call(_this, _this.item);
                    }
                });
            }else{
                throw 'QtiLoader : cannot load elements in empty item';
            }

        },
        buildResponse : function(data){

            var response = this.buildElement(data);

            response.template = data.howMatch || null;
            response.defaultValue = data.defaultValue || null;
            response.correctResponse = data.correctResponses || null;

            if(_.size(data.mapping)){
                response.mapEntries = data.mapping;
            }else if(_.size(data.areaMapping)){
                response.mapEntries = data.areaMapping;
            }else{
                response.mapEntries = {};
            }

            response.mappingAttributes = data.mappingAttributes || {};

            return response;
        },
        buildSimpleFeedbackRule : function(data, response){

            var feedbackRule = this.buildElement(data);
            
            feedbackRule.setCondition(response, data.condition, data.comparedValue || null);
            
//            feedbackRule.comparedOutcome = this.item.responses[data.comparedOutcome] || null;
            feedbackRule.feedbackOutcome = this.item.outcomes[data.feedbackOutcome] || null;
            feedbackRule.feedbackThen = this.item.modalFeedbacks[data.feedbackThen] || null;
            feedbackRule.feedbackElse = this.item.modalFeedbacks[data.feedbackElse] || null;
            
            //associate the compared outcome to the feedbacks if applicable
            var response = feedbackRule.comparedOutcome;
            if(feedbackRule.feedbackThen){
                feedbackRule.feedbackThen.data('relatedResponse', response);
            }
            if(feedbackRule.feedbackElse){
                feedbackRule.feedbackElse.data('relatedResponse', response);
            }
            
            return feedbackRule;
        },
        buildOutcome : function(data){
            var outcome = this.buildElement(data);
            outcome.defaultValue = data.defaultValue || null;
            return outcome;
        },
        buildResponseProcessing : function(data){
            var rp = this.buildElement(data);
            if(data && data.processingType){
                if(data.processingType === 'custom'){
                    rp.xml = data.data;
                    rp.processingType = 'custom';
                }else{
                    rp.processingType = 'templateDriven';
                }
            }
            return rp;
        },
        loadContainer : function(bodyObject, bodyData){
            if(!Element.isA(bodyObject, '_container')){
                throw 'bodyObject must be a QTI Container';
            }

            if(bodyData && typeof bodyData.body === 'string' && (typeof bodyData.elements === 'array' || typeof bodyData.elements === 'object')){
                for(var serial in bodyData.elements){
                    var eltData = bodyData.elements[serial];
                    //check if class is loaded:
                    var element = this.buildElement(eltData);
                    if(element){
                        bodyObject.setElement(element, bodyData.body);
                    }
                }
                bodyObject.body(bodyData.body);
            }else{
                throw 'wrong bodydata format';
            }
        },
        buildElement : function(elementData){
            var elt = null;
            if(elementData && elementData.qtiClass && elementData.serial){
                var className = elementData.qtiClass;
                if(this.qti[className]){
                    elt = new this.qti[className](elementData.serial);
                    this.loadElementData(elt, elementData);
                }else{
                    throw 'the qti element class does not exist: ' + className;
                }
            }else{
                throw 'wrong elementData format';
            }
            return elt;
        },
        loadElementData : function(element, data){

            //merge attributes when loading element data
            var attributes = _.defaults(data.attributes || {}, element.attributes || {});
            element.setAttributes(attributes);

            if(element.body && data.body){
                if(element.bdy){
                    this.loadContainer(element.getBody(), data.body);
                }
            }

            if(element.object && data.object){
                if(element.object){
                    this.loadObjectData(element.object, data.object);
                }
            }

            if(Element.isA(element, 'interaction')){
                this.loadInteractionData(element, data);
            }else if(Element.isA(element, 'choice')){
                this.loadChoiceData(element, data);
            }else if(Element.isA(element, 'math')){
                this.loadMathData(element, data);
            }else if(Element.isA(element, 'infoControl')){
                this.loadPicData(element, data);
            }

            return element;
        },
        loadInteractionData : function(interaction, data){
            if(Element.isA(interaction, 'blockInteraction')){
                if(data.prompt){
                    this.loadContainer(interaction.prompt.getBody(), data.prompt);
                }
            }

            this.buildInteractionChoices(interaction, data);

            if(Element.isA(interaction, 'customInteraction')){
                this.loadPciData(interaction, data);
            }
        },
        buildInteractionChoices : function(interaction, data){

            //note: Qti.ContainerInteraction (Qti.GapMatchInteraction and Qti.HottextInteraction) has already been parsed by builtElement(interacionData);
            if(data.choices){
                if(Element.isA(interaction, 'matchInteraction')){
                    for(var set = 0; set < 2; set++){
                        if(!data.choices[set]){
                            throw 'missing match set #' + set;
                        }
                        var matchSet = data.choices[set];
                        for(var serial in matchSet){
                            var choice = this.buildElement(matchSet[serial]);
                            if(choice){
                                interaction.addChoice(choice, set);
                            }
                        }
                    }
                }else{
                    for(var serial in data.choices){
                        var choice = this.buildElement(data.choices[serial]);
                        if(choice){
                            interaction.addChoice(choice);
                        }
                    }
                }

                if(Element.isA(interaction, 'graphicGapMatchInteraction')){
                    if(data.gapImgs){
                        for(var serial in data.gapImgs){
                            var gapImg = this.buildElement(data.gapImgs[serial]);
                            if(gapImg){
                                interaction.addGapImg(gapImg);
                            }
                        }
                    }
                }

            }

        },
        loadChoiceData : function(choice, data){
            if(Element.isA(choice, 'textVariableChoice')){
                choice.val(data.text);
            }else if(Element.isA(choice, 'gapImg')){
                //has already been taken care of in buildElement()
            }else if(Element.isA(choice, 'containerChoice')){
                //has already been taken care of in buildElement()
            }
        },
        loadObjectData : function(object, data){
            object.setAttributes(data.attributes);
            //@todo: manage object like a container
            if(data._alt){
                if(data._alt.qtiClass === 'object'){
                    object._alt = Loader.buildElement(data._alt);
                }else{
                    object._alt = data._alt;
                }
            }
        },
        loadMathData : function(math, data){
            math.ns = data.ns || {};
            math.setMathML(data.mathML || '');
            _.forIn(data.annotations || {}, function(value, encoding){
                math.setAnnotation(encoding, value);
            });
        },
        loadPciData : function(pci, data){
            loadPortableCustomElementData(pci, data);
        },
        loadPicData : function(pic, data){
            loadPortableCustomElementData(pic, data);
        }
    });

    function loadPortableCustomElementData(portableElement, data){
        portableElement.typeIdentifier = data.typeIdentifier;
        portableElement.markup = data.markup;
        portableElement.entryPoint = data.entryPoint;
        portableElement.properties = data.properties;
        portableElement.libraries = data.libraries;
    }

    return Loader;
});
