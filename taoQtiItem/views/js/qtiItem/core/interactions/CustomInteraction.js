define([
    'taoQtiItem/qtiItem/core/interactions/Interaction',
    'lodash',
    'taoQtiItem/qtiItem/helper/rendererConfig'
], function(Interaction, _, rendererConfig){

    var BlockInteraction = Interaction.extend({
        qtiClass : 'customInteraction',
        init : function(serial, attributes){
        
            this._super(serial, attributes);
            
            this.typeIdentifier = '';
            this.markup = '';
            this.properties = {};
            this.libraries = [];
        },
        is : function(qtiClass){
            return (qtiClass === 'customInteraction') || this._super(qtiClass);
        },
        render : function(){
            
            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer || this.getRenderer(),
                defaultData = {
                    markup : this.markup
                };
            
            return this._super(_.merge(defaultData, args.data), args.placeholder, args.subclass, renderer);
        },
        toArray : function(){
            var arr = this._super();
            arr.markup = this.markup;
            arr.properties = this.properties;
            return arr;
        }
    });
    return BlockInteraction;
});

