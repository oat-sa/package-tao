define([
    'taoQtiItem/qtiItem/core/Element',
    'lodash',
    'taoQtiItem/qtiItem/helper/rendererConfig',
    'taoQtiItem/qtiItem/helper/util'
], function(Element, _, rendererConfig, util){

    var Img = Element.extend({
        qtiClass : 'img',
        render : function(){

            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer || this.getRenderer(),
                baseUrl = renderer.getOption('baseUrl') || '',
                src = this.attr('src') || '',
                defaultData = {
                    attributes : {
                        src : util.fullpath(src, baseUrl)
                    }
                };
                
            return this._super(_.merge(defaultData, args.data), args.placeholder, args.subclass, renderer);
        },
        isEmpty : function(){
            return (!this.attr('src'));
        }
    });

    return Img;
});
