/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @requires jquery
 * @requires core/pluginifier
 * @requires core/dataattrhandler
 */
define(['jquery', 'lodash', 'core/pluginifier', 'core/dataattrhandler'], function($, _, Pluginifier, DataAttrHandler){
   'use strict';
   
   var ns = 'btngrouper';
   var dataNs = 'ui.' + ns;
   
   var defaults = {
       bindEvent    : 'click',
       activeClass  : 'active',
       disableClass : 'disabled',
       innerElt     : 'li',
       action       : 'toggle'
   };
   
   //todo add other behavior : on/off, multi
   var availableActions = ['toggle', 'switch'];
   
   /** 
    * The BtnGrouper component, hepls you to manage a group of buttons
    * @exports ui/btngrouper
    */
   var BtnGrouper = {
       
        /**
         * Initialize the plugin.
         * 
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').btngrouper({action : 'toggle' });
         * @public
         * 
         * @constructor
         * @param {Object} options - the plugin options
         * @param {string|boolean} [options.bindEvent = 'click'] - the event that trigger the close
         * @param {String} [options.action = 'toggle'] - the action type to be executed
         * @param {string} [options.activeClass = 'active'] - the css class to apply when an element of the button is active
         * @param {string} [options.innerElt = 'a'] - the element that compose the group
         * @fires BtnGrouper#create.btngrouper
         * @returns {jQueryElement} for chaining
         */
        init : function(options){
            
            //get options using default
            options = _.defaults(options || {}, defaults);
            
            if(!_.contains(availableActions, options.action)){
                return $.error('Action ' + options.action + ' not supported');
            }
           
            return this.each(function() {
                var $elt = $(this);
                
                if(!$elt.data(dataNs)){
                
                    //add data to the element
                    $elt.data(dataNs, options);
                    
                    if(options.action === 'toggle'){
                        //at the begining, one inner elt only should have the active class
                        var $activeElt = $elt.find(options.innerElt + '.' + options.activeClass);
                        if($activeElt.length === 0){
                            $elt.find(options.innerElt + ':first').addClass(options.activeClass);
                        } else if ($activeElt.length > 1) {
                            $elt.find(options.innerElt+ '.' + options.activeClass).not(':first').removeClass(options.activeClass);
                        }
                    }

                    //bind an event to trigger the action
                    if(options.bindEvent !== false){
                        //the event is bound to the 
                        $elt.on(options.bindEvent, options.innerElt, function(e){
                            e.preventDefault();
                            //execute the private method that corresponds to tha action
                            var action = '_' + options.action;
                            if(typeof BtnGrouper[action] === 'function'){
                                BtnGrouper[action]($elt, $(this));
                            }
                         });
                    }

                    /**
                     * The plugin have been created.
                     * @event BtnGrouper#create.btngrouper
                     */
                    $elt.trigger('create.' + ns);
                }
            });
       },
       
       /**
        * Toggle the button state.
        * 
        * Called the jQuery way once registered by the Pluginifier.
        * @example $('selector').btngrouper('toggle');
        * @param {jQueryElement} $elt - plugin's element 
        * @fires BtnGrouper#toggle.btngrouper
        */
       _toggle: function($elt){
            var options = $elt.data(dataNs);

            $elt.find(options.innerElt).toggleClass(options.activeClass);
        
           /**
            * The target has been toggled. 
            * @event BtnGrouper#toggle.btngrouper
            */
            $elt.trigger('toggle.' + ns)
                    .trigger('change');
       },
       
       /**
        * On/Off a button in the group .
        * 
        * @example $('selector').btngrouper('toggle');
        * @param {jQueryElement} $elt - plugin's element 
        * @param {jQueryElement} $target - the inner element to switch
        * @fires BtnGrouper#toggle.btngrouper
        */
       _switch: function($elt, $target){
            var options = $elt.data(dataNs);

            $target.toggleClass(options.activeClass);
        
           /**
            * The target has been toggled. 
            * @event BtnGrouper#toggle.btngrouper
            */
            $elt.trigger('switch.' + ns)
                    .trigger('change');
       },
       
       /**
        * Get the active(s) value(s)
        * @returns {string|array} the text content of the active buttons
        */
       value : function(){
           var value = [];
           this.each(function(){
               var $elt = $(this);
               var options = $elt.data(dataNs);
               var eltValue = $elt.find(options.innerElt).filter('.' + options.activeClass).text();
               if($.isArray(eltValue)){
                   value.concat(eltValue);
               } else {
                   value.push(eltValue);
               }
           });
           return (value.length === 1) ? value[0]: value;
       },
               
       /**
        * Destroy completely the plugin.
        * 
        * Called the jQuery way once registered by the Pluginifier.
        * @example $('selector').btngrouper('destroy');
        * @public
        */
       destroy : function(){
            this.each(function() {
                var $elt = $(this);
                var options = $elt.data(dataNs);
                if(options.bindEvent !== false){
                    $elt.off(options.bindEvent, options.innerElt);
                }
                $elt.removeData(dataNs);
                
                /**
                 * The plugin have been destroyed.
                 * @event BtnGrouper#destroy.btngrouper
                 */
                $elt.trigger('destroy.' + ns);
            });
        }
   };
   
    //Register the btngrouper to behave as a jQuery plugin.
    Pluginifier.register(ns, BtnGrouper, {
        expose : ['toggle', 'switch']
    });
   
   /**
    * The only exposed function is used to start listening on data-attr
    * 
    * @public
    * @example define(['ui/btngrouper'], function(btngrouper){ btngrouper($('rootContainer')); });
    * @param {jQueryElement} $container - the root context to listen in
    */
   return function listenDataAttr($container){
       
        new DataAttrHandler('button-group', {
            container: $container,
            inner : 'li',
            bubbled : true,
            listenerEvent: 'click',
            namespace: dataNs,
            useTarget: false
        }).init(function($elt, $target) {
            $elt.on('create.' + ns, function(e){
                if(e.namespace === ns){
                    $elt.btngrouper($elt.data('button-group'), $target);
                }
            });
            $elt.btngrouper({
                action : $elt.data('button-group')
            });
        });
    };
});

