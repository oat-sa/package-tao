define([
    'taoQtiItem/qtiItem/core/Element',
    'lodash',
    'taoQtiItem/qtiItem/helper/rendererConfig'
], function(Element, _, rendererConfig){

    var Img = Element.extend({
        qtiClass : 'img',
        render : function(){

            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer || this.getRenderer(),
                defaultData = {};

            defaultData.attributes = {
                src : renderer.resolveUrl(this.attr('src'))
            };

            return this._super(_.merge(defaultData, args.data), args.placeholder, args.subclass, renderer);
        },
        isEmpty : function(){
            return (!this.attr('src'));
        }
    });

    return Img;
});
