define([
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiItem/mixin/CustomElement',
    'taoQtiItem/qtiItem/helper/rendererConfig',
    'lodash'
], function(Element, CustomElement, rendererConfig, _){
    
    var PortableInfoControl = Element.extend({
        qtiClass : 'infoControl',
        defaultNsName : 'pic',
        defaultNsUri : 'http://www.imsglobal.org/xsd/portableInfoControl',
        defaultMarkupNsName : 'html5',
        defaultMarkupNsUri : 'html5',
        init : function(serial, attributes){

            this._super(serial, attributes);

            this.typeIdentifier = '';
            this.markup = '';
            this.properties = {};
            this.libraries = [];
            this.entryPoint = '';
            
            //note : if the uri is defined, it will be set the uri in the xml on xml serialization,
            //which may trigger xsd validation, which is troublesome for html5 (use xhtml5 maybe ?)
            this.markupNs = {};
        },
        is : function(qtiClass){
            return (qtiClass === 'infoControl') || this._super(qtiClass);
        },
        render : function(){

            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer || this.getRenderer(),
                defaultData = {
                    typeIdentifier : this.typeIdentifier,
                    markup : this.markup,
                    properties : this.properties,
                    libraries : this.libraries,
                    entryPoint : this.entryPoint,
                    ns : {
                        pic : this.getNamespace().name + ':'
                    }
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
    
    //add portable element standard functions
    CustomElement.augment(PortableInfoControl);
    
    return PortableInfoControl;
});