define([
    'lodash',
    'taoQtiItem/qtiItem/core/interactions/Interaction',
    'taoQtiItem/qtiItem/mixin/CustomElement',
    'taoQtiItem/qtiItem/mixin/NamespacedElement',
    'taoQtiItem/qtiItem/helper/rendererConfig'
], function(_, Interaction, CustomElement, NamespacedElement, rendererConfig){

    var CustomInteraction = Interaction.extend({
        qtiClass : 'customInteraction',
        defaultNsName : 'pci',
        defaultNsUri : 'http://www.imsglobal.org/xsd/portableCustomInteraction',
        nsUriFragment : 'portableCustomInteraction',
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

            this.pciReadyCallbacks = [];
        },
        is : function(qtiClass){
            return (qtiClass === 'customInteraction') || this._super(qtiClass);
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
                        pci : this.getNamespace().name + ':'
                    }
                };

            return this._super(_.merge(defaultData, args.data), args.placeholder, args.subclass, renderer);
        },
        toArray : function(){
            var arr = this._super();
            arr.markup = this.markup;
            arr.properties = this.properties;
            return arr;
        },
        getMarkupNamespace : function(){

            if(this.markupNs && this.markupNs.name && this.markupNs.uri){
                return _.clone(this.markupNs);
            }else{
                var relatedItem = this.getRelatedItem();
                if(relatedItem){
                    //set the default one:
                    relatedItem.namespaces[this.defaultMarkupNsName] = this.defaultMarkupNsUri;
                    return {
                        name : this.defaultMarkupNsName,
                        uri : this.defaultMarkupNsUri
                    };
                }
            }

            return {};
        },
        setMarkupNamespace : function(name, uri){
            this.markupNs = {
                name : name,
                uri : uri
            };
        },
        onPciReady : function(callback){

            this.pciReadyCallbacks.push(callback);

            if(this.data('pci')){
                //if pci is already ready, call it immediately
                this.triggerPciReady();
            }
        },
        triggerPciReady : function(){

            var _this = this,
                pci = this.data('pci');

            if(pci){
                _.each(this.pciReadyCallbacks, function(fn){
                    fn.call(_this, pci);
                });
                
                //empty the stack of ready callbacks
                this.pciReadyCallbacks = [];
                
                //mark the interaction as ready
                this.data('pciReady', true);
                
            }else{
                throw 'cannot trigger pci ready when no pci is actually attached to the interaction';
            }

        },
        onPci : function(event, callback){
            this.onPciReady(function(pci){
                if(_.isFunction(pci.on)){
                    pci.on(event, callback);
                }else{
                    throw 'the pci does not implement on() function';
                }
            });
            return this;
        },
        offPci : function(event){
            this.onPciReady(function(pci){
                if(_.isFunction(pci.off)){
                    pci.off(event);
                }else{
                    throw 'the pci does not implement off() function';
                }
            });
            return this;
        },
        triggerPci : function(event, args){
            this.onPciReady(function(pci){
                if(_.isFunction(pci.off)){
                    pci.trigger(event, args);
                }else{
                    throw 'the pci does not implement off() function';
                }
            });
            return this;
        }
    });
    
    //add portable element standard functions
    CustomElement.augment(CustomInteraction);
    NamespacedElement.augment(CustomInteraction);
    
    return CustomInteraction;
});

