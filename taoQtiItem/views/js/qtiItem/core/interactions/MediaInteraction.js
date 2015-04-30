define([
    'lodash',
    'taoQtiItem/qtiItem/core/interactions/ObjectInteraction',
    'taoQtiItem/qtiItem/helper/rendererConfig'
], function(_, ObjectInteraction, rendererConfig){
    var MediaInteraction = ObjectInteraction.extend({
        qtiClass : 'mediaInteraction',
        render : function(){

            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer || this.getRenderer(),
                defaultData = {
                    'media' : this.object.render({}, null, '', renderer)
                };

            return this._super(_.merge(defaultData, args.data), args.placeholder, args.subclass, renderer);

        }
    });
    return MediaInteraction;
});
