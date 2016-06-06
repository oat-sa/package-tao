define([
    'taoQtiItem/qtiItem/core/interactions/Interaction',
    'taoQtiItem/qtiItem/core/interactions/Prompt',
    'lodash',
    'taoQtiItem/qtiItem/helper/rendererConfig'
], function(Interaction, Prompt, _, rendererConfig){
    'use strict';

    var BlockInteraction = Interaction.extend({
        init : function(serial, attributes){
            this._super(serial, attributes);
            this.prompt = new Prompt('');
        },
        is : function(qtiClass){
            return (qtiClass === 'blockInteraction') || this._super(qtiClass);
        },
        getComposingElements : function(){
            var elts = this._super();
            elts = _.extend(elts, this.prompt.getComposingElements());
            elts[this.prompt.getSerial()] = this.prompt;
            return elts;
        },
        find : function(serial){
            return this._super(serial) || this.prompt.find(serial);
        },
        render : function(){

            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer || this.getRenderer(),
                defaultData = {
                    'prompt' : this.prompt.render(renderer)
                };

            return this._super(_.merge(defaultData, args.data), args.placeholder, args.subclass, renderer);
        },
        postRender : function(data, altClassName, renderer){
            renderer = renderer || this.getRenderer();
            return []
                    .concat(this.prompt.postRender({}, '', renderer))
                    .concat(this._super(data, altClassName, renderer));
        },
        toArray : function(){
            var arr = this._super();
            arr.prompt = this.prompt.toArray();
            return arr;
        }
    });
    return BlockInteraction;
});

