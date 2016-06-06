/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @requires jquery
 * @requires lodash
 * @requires core/pluginifier
 * @requires core/dataattrhandler
 */
define(['jquery', 'lodash', 'core/pluginifier', 'core/dataattrhandler'], function($, _, Pluginifier, DataAttrHandler){
   'use strict';
   
   var ns = 'disabler';
   var dataNs = 'ui.' + ns;
   
   var defaults = {
       bindEvent : 'click',
       disabledClass : 'disabled'
   };
   
   /** 
    * The Disabler component, that helps you to disable/enable elements.
    * @exports ui/disabler
    */
   var Disabler = {
       
       /**
         * Initialize the plugin.
         * 
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').disabler({target : $('target')});
         * @public
         * 
         * @constructor
         * @param {Object} options - the plugin options
         * @param {jQueryElement} options.target - the element to enable/disable
         * @param {string|boolean} [options.bindEvent = 'click'] - the event that trigger the disabling
         * @fires Disabler#create.disabler
         * @returns {jQueryElement} for chaining
         */
        init : function(options){
            options = $.extend(true, {}, defaults, options);
           
            return this.each(function() {
                var $elt = $(this);
                
                if(!$elt.data(dataNs)){
                    //add data to the element
                    $elt.data(dataNs, options);

                     //bind an event to trigger the addition
                    if(options.bindEvent !== false){
                        $elt.on(options.bindEvent, function(e){
                            Disabler._toogle($elt);
                            e.preventDefault();
                         });
                    }

                    /**
                     * The plugin have been created.
                     * @event Disabler#create.disabler
                     */
                    $elt.trigger('create.' + ns);
                }
            });
       },
       
       /**
        * Enable the target.
        * 
        * Called the jQuery way once registered by the Pluginifier.
        * @example $('selector').disabled('enable');
        * @public
        * 
        * @returns {jQueryElement} for chaining
        */
       enable : function(){
           this.each(function() {
                Disabler._enable($(this));
           });
       },
         
       /**
        * Internal enabling mechanism.
        * 
        * @private
        * @param {jQueryElement} $elt - plugin's element 
        * @fires Disabler#enable.disabler
        */
       _enable : function($elt){
           var options = $elt.data(dataNs);
           var $target = options.target;
           $target.removeClass(options.disabledClass)
                .find(':input').prop('disabled', false);
        
           /**
            * The target has been enabled
            * @event Disabler#enable.disabler
            * @param {jQueryElement} $target - the enabled target
            */
           $elt.trigger('enable.' + ns, [$target]);
           $target.find(':input').andSelf().trigger('enable');
       },
               
       /**
        * Disable the target.
        * 
        * Called the jQuery way once registered by the Pluginifier.
        * @example $('selector').disabled('disable');
        * @public
        * 
        * @returns {jQueryElement} for chaining
        */
       disable : function(){
           this.each(function() {
                Disabler._disable($(this));
           });
       },
         
       /**
        * Internal disabling mechanism.
        * 
        * @private
        * @param {jQueryElement} $elt - plugin's element 
        * @fires Disabler#disable.disabler
        */
       _disable : function($elt){
            var options = $elt.data(dataNs);
            var $target = options.target;
            $target.addClass(options.disabledClass)
                 .find(':input').prop('disabled', true);
         
            /**
            * The target has been disabled
            * @event Disabler#disable.disabler
            * @param {jQueryElement} $target - the enabled target
            */
            $elt.trigger('disable.' + ns, [$target]);
            $target.find(':input').andSelf().trigger('disable');
       },
               
       /**
        * Enable/disable.
        * 
        * Called the jQuery way once registered by the Pluginifier.
        * @example $('selector').disabler('toggle');
        * @public
        * 
        * @returns {jQueryElement} for chaining
        */
       toggle : function(){
           this.each(function() {
                Disabler._toggle($(this));
           });
       },
       
        /**mechnism
        * Internal enable/disable mechanism.
        * 
        * @private
        * @param {jQueryElement} $elt - plugin's element 
        */
       _toggle: function($elt){
            var options = $elt.data(dataNs);
            if( $elt.is(':radio,:checkbox') ){
                $elt.prop('checked') ?  this._disable($elt) : this._enable($elt);
            } else {
                options.target.hasClass(options.disabledClass) ?  this._enable($elt) : this._disable($elt);
            }
       },
               
       /**
        * Destroy completely the plugin.
        * 
        * Called the jQuery way once registered by the Pluginifier.
        * @example $('selector').disabler('destroy');
        * @public
        * @fires Disabler#destroy.disabler
        */
       destroy : function(){
            this.each(function() {
                var $elt = $(this);
                var options = $elt.data(dataNs);
                if(options.bindEvent !== false){
                    $elt.off(options.bindEvent);
                }
                
                /**
                 * The plugin have been destroyed.
                 * @event Disabler#destroy.disabler
                 */
                $elt.trigger('destroy.' + ns);
            });
        }
   };
   
   //Register the toggler to behave as a jQuery plugin.
   Pluginifier.register(ns, Disabler);
   
    /**
    * The only exposed function is used to start listening on data-attr
    * 
    * @public
    * @example define(['ui/disabler'], function(disabler){ disabler($('rootContainer')); });
    * @param {jQueryElement} $container - the root context to listen in
    */
   return function listenDataAttr($container){
       
        new DataAttrHandler('disable', {
            container: $container,
            listenerEvent: 'click',
            namespace: dataNs
        }).init(function($elt, $target) {
            $elt.disabler({
                target: $target,
                bindEvent: false
            });
        }).trigger(function($elt) {
            $elt.disabler('toggle');
        });
    };
});

