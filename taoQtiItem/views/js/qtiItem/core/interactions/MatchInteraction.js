define([
    'taoQtiItem/qtiItem/core/interactions/BlockInteraction',
    'taoQtiItem/qtiItem/core/choices/SimpleAssociableChoice',
    'lodash',
    'taoQtiItem/qtiItem/helper/rendererConfig',
    'taoQtiItem/qtiItem/helper/util'
], function(BlockInteraction, SimpleAssociableChoice, _, rendererConfig, util){
    'use strict';

    var MatchInteraction = BlockInteraction.extend({
        qtiClass : 'matchInteraction',
        init : function(serial, attributes){
            this._super(serial, attributes);
            this.choices = [{}, {}];
        },
        addChoice : function(choice, matchSet){
            matchSet = parseInt(matchSet);
            if(this.choices[matchSet]){
                choice.setRelatedItem(this.getRelatedItem() || null);
                this.choices[matchSet][choice.getSerial()] = choice;
            }
        },
        getChoices : function(matchSet){
            matchSet = parseInt(matchSet);
            if(this.choices[matchSet]){
                return _.clone(this.choices[matchSet]);
            }else{
                return _.clone(this.choices);
            }
        },
        getChoice : function(serial){
            return this.choices[0][serial] || this.choices[1][serial] || null;
        },
        getComposingElements : function(){

            var elts = this._super();
            //recursive to both match sets:
            for(var i = 0; i < 2; i++){
                var matchSet = this.getChoices(i);
                for(var serial in matchSet){
                    if(matchSet[serial] instanceof SimpleAssociableChoice){
                        elts[serial] = matchSet[serial];
                        elts = _.extend(elts, matchSet[serial].getComposingElements());
                    }
                }
            }

            return elts;
        },
        find : function(serial){
            var found = this._super(serial);
            if(!found){
                found = util.findInCollection(this, ['choices.0', 'choices.1'], serial);
            }
            return found;
        },
        render : function(){

            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer || this.getRenderer(),
                choices,
                defaultData = {
                'matchSet1' : [],
                'matchSet2' : []
            };

            var interactionData = {'interaction' : {'serial' : this.serial, 'attributes' : this.attributes}};

            if(!renderer){
                throw 'no renderer found for the interaction ' + this.qtiClass;
            }

            if(this.attr('shuffle') && renderer.getOption('shuffleChoices')){
                choices = renderer.getShuffledChoices(this);
            }else{
                choices = this.getChoices();
            }

            for(var i = 0; i < 2; i++){
                var matchSet = choices[i];
                for(var serial in matchSet){
                    if(matchSet[serial] instanceof SimpleAssociableChoice){
                        defaultData['matchSet' + (i + 1)].push(matchSet[serial].render(_.clone(interactionData, true), null, 'simpleAssociableChoice.matchInteraction', renderer));
                    }
                }
            }
            
            return this._super(_.merge(defaultData, args.data), args.placeholder, args.subclass, renderer);
        },
        postRender : function(data, altClassName, renderer){
            renderer = renderer || this.getRenderer();
            return _(this.getChoices())
                .map(function(choices){
                    return _(choices)
                        .filter(function(choice){
                            return choice instanceof SimpleAssociableChoice;
                        })
                        .map(function(choice){
                            return choice.postRender({}, 'simpleAssociableChoice.matchInteraction', renderer);
                        })
                        .value();
                })
                .flatten(true)
                .value()
                .concat(this._super(data, altClassName, renderer));
        },
        toArray : function(){
            var arr = this._super();
            arr.choices = {0 : {}, 1 : {}};
            for(var i = 0; i < 2; i++){
                var matchSet = this.getChoices(i);
                for(var serial in matchSet){
                    if(matchSet[serial] instanceof SimpleAssociableChoice){
                        arr.choices[i][serial] = matchSet[serial].toArray();
                    }
                }
            }
            return arr;
        }
    });

    return MatchInteraction;
});


