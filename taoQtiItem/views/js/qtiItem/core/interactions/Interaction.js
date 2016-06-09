define([
    'taoQtiItem/qtiItem/core/Element', 
    'lodash', 
    'taoQtiItem/qtiItem/helper/rendererConfig',
    'taoQtiItem/qtiItem/helper/util'
], function(Element, _, rendererConfig, util){
    'use strict';

    var QtiInteraction = Element.extend({
        init : function(serial, attributes){
            this._super(serial, attributes);
            this.choices = {};
        },
        is : function(qtiClass){
            return (qtiClass === 'interaction') || this._super(qtiClass);
        },
        addChoice : function(choice){
            choice.setRelatedItem(this.getRelatedItem() || null);
            this.choices[choice.getSerial()] = choice;
            return this;
        },
        getChoices : function(){
            var choices = {};
            for(var i in this.choices){//prevent passing the whole array by ref
                choices[i] = this.choices[i];
            }
            return choices;
        },
        getChoice : function(serial){
            var ret = null;
            if(this.choices[serial]){
                ret = this.choices[serial];
            }
            return ret;
        },
        getChoiceByIdentifier : function(identifier){
            for(var i in this.choices){
                if(this.choices[i].id() === identifier){
                    return this.choices[i];
                }
            }
            return null;
        },
        getComposingElements : function(){
            var elts = this._super();
            //recursive to choices:
            for(var serial in this.choices){
                if(Element.isA(this.choices[serial], 'choice')){
                    elts[serial] = this.choices[serial];
                    elts = _.extend(elts, this.choices[serial].getComposingElements());
                }
            }
            return elts;
        },
        find : function(serial){
            var found = this._super(serial);
            if(!found){
                found = util.findInCollection(this, 'choices', serial);
            }
            return found;
        },
        getResponseDeclaration : function(){
            var response = null;
            var responseId = this.attr('responseIdentifier');
            if(responseId){
                var item = this.getRelatedItem();
                if(item){
                    response = item.getResponseDeclaration(responseId);
                }else{
                    throw 'cannot get response of an interaction out of its item context';
                }
            }
            return response;
        },
        /**
         * Render the interaction to the view.
         * The optional argument "subClass" allows distinguishing customInteraction: e.g. customInteraction.matrix, customInteraction.likertScale ...
         */
        render : function(){

            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer || this.getRenderer(),
                defaultData = {
                    '_type' : this.qtiClass.replace(/([A-Z])/g, function($1){
                        return "_" + $1.toLowerCase();
                    }),
                    'choices' : [],
                    'choiceShuffle' : true
                };

            if(!renderer){
                throw 'no renderer found for the interaction ' + this.qtiClass;
            }
            
            var choices = (this.attr('shuffle') && renderer.getOption('shuffleChoices')) ? renderer.getShuffledChoices(this) : this.getChoices();
            var interactionData = {'interaction' : {'serial' : this.serial, 'attributes' : this.attributes}};
            var _this = this;
            _.each(choices, function(choice){
                if(Element.isA(choice, 'choice')){
                    try{
                        var renderedChoice = choice.render(_.clone(interactionData, true), null, choice.qtiClass + '.' + _this.qtiClass, renderer); //use interaction type as choice subclass
                        defaultData.choices.push(renderedChoice);
                    }catch(e){
                        //leave choices empty in case of error
                    }
                }
            });
            
            var tplName = args.subclass ? this.qtiClass + '.' + args.subclass : this.qtiClass;
            
            return this._super(_.merge(defaultData, args.data), args.placeholder, tplName, renderer);
        },
        postRender : function(data, altClassName, renderer){
            var self = this;
            renderer = renderer || this.getRenderer();

            return _(this.getChoices())
                .filter(function(elt){
                    return Element.isA(elt, 'choice');
                })
                .map(function(choice){
                    return choice.postRender({}, choice.qtiClass + '.' + self.qtiClass, renderer);
                })
                .value()
                .concat(this._super(data, altClassName, renderer));
        },
        setResponse : function(values){
            var ret = null;
            var renderer = this.getRenderer();
            if(renderer){
                ret = renderer.setResponse(this, values);
            }else{
                throw 'no renderer found for the interaction ' + this.qtiClass;
            }
            return ret;
        },
        getResponse : function(){
            var ret = null;
            var renderer = this.getRenderer();
            if(renderer){
                ret = renderer.getResponse(this);
            }else{
                throw 'no renderer found for the interaction ' + this.qtiClass;
            }
            return ret;
        },
        resetResponse : function(){
            var ret = null;
            var renderer = this.getRenderer();
            if(renderer){
                ret = renderer.resetResponse(this);
            }else{
                throw 'no renderer found for the interaction ' + this.qtiClass;
            }
            return ret;
        },

        /**
         * Retrieve the state of the interaction. 
         * The state is provided by the interaction's renderer.
         * 
         * @returns {Object} the interaction's state
         * @throws {Error} if no renderer is found 
         */
        getState : function(){
            var ret = null;
            var renderer = this.getRenderer();
            if(renderer){
                if(_.isFunction(renderer.getState)){
                    ret = renderer.getState(this);
                }
            }else{
                throw 'no renderer found for the interaction ' + this.qtiClass;
            }
            return ret;
        },

        /**
         * Retrieve the state of the interaction. 
         * The state will be given to the interaction's renderer.
         * 
         * @param {Object} state - the interaction's state
         * @throws {Error} if no renderer is found 
         */
        setState : function(state){
            var renderer = this.getRenderer();
            if(renderer){
                if(_.isFunction(renderer.setState)){
                    renderer.setState(this, state);
                }
            }else{
                throw 'no renderer found for the interaction ' + this.qtiClass;
            }
        },

        /**
         * Clean up an interaction rendering.
         * Ask the renderer to run destroy if exists.
         * 
         * @throws {Error} if no renderer is found 
         */
        clear : function(){
            var renderer = this.getRenderer();
            if(renderer){
                if(_.isFunction(renderer.destroy)){
                    renderer.destroy(this);
                }
            }else{
                throw 'no renderer found for the interaction ' + this.qtiClass;
            }
        },

        toArray : function(){
            var arr = this._super();
            arr.choices = {};
            for(var serial in this.choices){
                if(Element.isA(this.choices[serial], 'choice')){
                    arr.choices[serial] = this.choices[serial].toArray();
                }
            }
            return arr;
        }
    });
    return QtiInteraction;
});
