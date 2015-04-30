define(['taoQtiItem/qtiItem/core/interactions/BlockInteraction', 'lodash', 'taoQtiItem/qtiItem/helper/rendererConfig'], function(BlockInteraction, _, rendererConfig){
    
    var ExtendedTextInteraction = BlockInteraction.extend({
        qtiClass : 'extendedTextInteraction',
        render : function(){
            
            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer || this.getRenderer(),
                defaultData = {
                    'multiple' : false,
                    'maxStringLoop' : []
                },
                response = this.getResponseDeclaration();
        
            if(this.attr('maxStrings') && (response.attr('cardinality') === 'multiple' || response.attr('cardinality') === 'ordered')){
                defaultData.multiple = true;
                for(var i = 0; i < this.attr('maxStrings'); i++){
                    defaultData.maxStringLoop.push(i + '');//need to convert to string. The tpl engine fails otherwise
                }
            }

            return this._super(_.merge(defaultData, args.data), args.placeholder, args.subclass, renderer);
        }
    });
    
    return ExtendedTextInteraction;
});


