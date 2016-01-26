define(['taoQtiItem/qtiItem/core/Element', 'lodash', 'taoQtiItem/qtiItem/helper/rendererConfig'], function(Element, _, rendererConfig){

    var Stylesheet = Element.extend({
        qtiClass : 'stylesheet',
        render : function(){

            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer || this.getRenderer(),
                defaultData = {};

            defaultData.attributes = {href : renderer.resolveUrl(this.attr('href'))};

            return this._super(_.merge(defaultData, args.data), args.placeholder, args.subclass, renderer);
        }
    });

    return Stylesheet;
});
