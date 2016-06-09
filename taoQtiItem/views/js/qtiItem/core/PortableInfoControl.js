define([
    'lodash',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiItem/mixin/CustomElement',
    'taoQtiItem/qtiItem/mixin/NamespacedElement',
    'taoQtiItem/qtiItem/helper/rendererConfig'
], function(_, Element, CustomElement, NamespacedElement, rendererConfig){
    'use strict';

    var PortableInfoControl = Element.extend({
        qtiClass : 'infoControl',
        defaultNsName : 'pic',
        defaultNsUri : 'http://www.imsglobal.org/xsd/portableInfoControl',
        nsUriFragment : 'portableInfoControl',
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

            //stack of callback waiting to be ready
            this.readyStack = [];
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

        /**
         * Retrieve the state of the infoControl
         * The call will be delegated to the infoControl's renderer.
         *
         * @returns {Object} the state
         */
        getState : function getState(){
            var ret = null;
            var renderer = this.getRenderer();
            if(renderer && _.isFunction(renderer.getState)){
                ret = renderer.getState(this);
            }
            return ret;
        },

        /**
         * Set the state of the infoControl
         * The state will be set to the infoControl's renderer.
         *
         * @param {Object} state - the infoControl's state
         */
        setState : function setState(state){
            var renderer = this.getRenderer();
            if(renderer && _.isFunction(renderer.getState)){
                renderer.setState(this, state);
            }
        },

        toArray : function(){
            var arr = this._super();
            arr.markup = this.markup;
            arr.properties = this.properties;
            return arr;
        },


        /**
         * Execute a callback when the PIC is ready (ie. registered, loaded and rendererd)
         * @param {Function} cb - the function to execute once ready
         */
        onReady : function onReady(cb){

            this.readyStack.push(cb);

            //if we are ready this will pop the stack
            if(this.data('_ready') && this.data('pic')){
                this.triggerReady();
            }
        },

        /**
         * Define the PIC as ready and consume the waiting functions in the stack.
         */
        triggerReady : function triggerReady(){
            var self = this;
            _.forEach(this.readyStack, function(cb){
                cb.call(self, self.data('pic'));
            });

            //empty the stack
            this.readyStack = [];

            //mark the infoControl as ready
            this.data('_ready', true);
        }
    });

    //add portable element standard functions
    CustomElement.augment(PortableInfoControl);
    NamespacedElement.augment(PortableInfoControl);

    return PortableInfoControl;
});
