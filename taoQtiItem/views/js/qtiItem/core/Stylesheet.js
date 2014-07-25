define(['taoQtiItem/qtiItem/core/Element', 'lodash', 'taoQtiItem/qtiItem/helper/rendererConfig'], function(Element, _, rendererConfig){
    
    var Stylesheet = Element.extend({
        qtiClass : 'stylesheet',
        render : function(){
            
            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer||this.getRenderer(),
                defaultData = {},
                baseUrl = renderer.getOption('baseUrl')||'',
                href = this.attr('href');
            
            if(!href.match(/^http/i)){
                defaultData.attributes = {href : baseUrl + href};
            }
            
            return this._super(_.merge(defaultData, args.data), args.placeholder, args.subclass, renderer);
        }
    });
    
    return Stylesheet;
});