define(['taoQtiItem/qtiItem/core/interactions/ObjectInteraction', 'lodash', 'taoQtiItem/qtiItem/helper/rendererConfig'], function(QtiObjectInteraction, _, rendererConfig){
    var QtiGraphicInteraction = QtiObjectInteraction.extend({
        render : function(){
            
            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer || this.getRenderer(),
                defaultData = {
                    'backgroundImage' : this.object.getAttributes(),
                    'object' : this.object.render(renderer)
                };
                
            return this._super(_.merge(defaultData, args.data), args.placeholder, args.subclass, renderer);
        }
    });

    return QtiGraphicInteraction;
});

