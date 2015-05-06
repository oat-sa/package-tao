/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @requires jquery
 * @requires core/pluginifier
 * @requires core/dataattrhandler
 */
define(['jquery', 'core/pluginifier', 'core/dataattrhandler'], function($, Pluginifier, DataAttrHandler){
   'use strict';
   
   var ns = 'toggler';
   var dataNs = 'ui.' + ns;
   
   var defaults = {
       disableClass : 'disabled',
       bindEvent   : 'click',
       openedClass : 'opened',
       closedClass : 'closed'
   };
   
   /** 
    * The Toggler component, that helps you to show/hide an element
    * @exports ui/toggler
    */
   var Toggler = {

        /**
         * Initialize the plugin.
         * 
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').toggler({target : $('target') });
         * @public
         * 
         * @constructor
         * @param {Object} options - the plugin options
         * @param {jQueryElement} options.target - the element to be toggled
         * @param {string|boolean} [options.bindEvent = 'click'] - the event that triggers the toggling
         * @param {string} [options.openedClass = 'opened'] - the css added to element (not the target) for the opened state
         * @param {string} [options.closedClass = 'closed'] - the css added to element (not the target) for the closed state
         * @param {string} [options.hideText] - the text to replace the toggler with when the element is toggled (ie. Show -> Hide)
         * @fires Toggler#create.toggler
         * @returns {jQueryElement} for chaining
         */
        init : function(options){
            
            //get options using default
            options = $.extend(true, {}, defaults, options);

            return this.each(function() {
                var $elt = $(this);
                var $target = options.target;
                var openedClass = options.openedClass;
                var closedClass = options.closedClass;
               
                if(!$elt.data(dataNs)){

                    if(options.hideText){
                        options.showText = $elt.text();
                    }
                    
                    //add data to the element
                    $elt.data(dataNs, options);

                    //add the default class if not set
                    if(!$elt.hasClass(closedClass) && !$elt.hasClass(openedClass)){
                        $elt.addClass($target.css('display') === 'none' ? closedClass : openedClass);
                    }

                    //keep in sync with changes made by another toggler
                    $target.on('toggle.' + ns, function(e, $toggler){
                         e.stopPropagation();
                         if(!$toggler.is($elt)){
                            if($target.css('display') === 'none'){
                                $elt.addClass(closedClass)
                                    .removeClass(openedClass);
                            } else {
                                $elt.removeClass(closedClass)
                                    .addClass(openedClass);
                            }

                        }
                    });

                    //bind an event to trigger the toggling
                    if(options.bindEvent !== false){
                        $elt.on(options.bindEvent, function(e){
                            e.preventDefault();
                            Toggler._toggle($(this));
                         });
                    }

                    /**
                     * The plugin have been created.
                     * @event Toggler#create.toggler
                     */
                    $elt.trigger('create.' + ns);
                }
            });
       },
       
       /**
        * Toggle the target.
        * 
        * Called the jQuery way once registered by the Pluginifier.
        * @example $('selector').toggler('toggle');
        * @param {jQueryElement} $elt - plugin's element 
        * @fires Toggler#toggle.toggler
        * @fires Toggler#open.toggler
        * @fires Toggler#close.toggler
        */
       _toggle: function($elt){
            var options = $elt.data(dataNs);
            var $target = options.target;

            var triggerEvents = function triggerEvents(){ 

                /**
                * The target has been toggled. 
                * Trigger 2 events : toggle and open or close.
                * @event Toggler#toggle.toggler
                * @event Toggler#open.toggler
                * @event Toggler#close.toggler
                */
                $elt.trigger('toggle.' + ns, [$target])
                    .trigger(action + '.' + ns, [$target]);

                //trigger also on the target in case of multiple toggling
                $target.trigger('toggle.' + ns, [$elt]);
            };

           var action;
           if( $elt.is(':radio,:checkbox') ){
                action =  $elt.prop('checked') ?  'open' : 'close';
            } else {
                action =  $elt.hasClass(options.closedClass) ?  'open' : 'close';
                $elt.toggleClass(options.closedClass)
                    .toggleClass(options.openedClass);
            }
            
            if(action === 'open'){
                $target.fadeIn(200, triggerEvents);
                if(options.hideText){
                    $elt.text(options.hideText);
                }
            } else {
                $target.fadeOut(300, triggerEvents);
                if(options.showText){
                    $elt.text(options.showText);
                }
            }
       },
               
       /**
        * Destroy completely the plugin.
        * 
        * Called the jQuery way once registered by the Pluginifier.
        * @example $('selector').toggler('destroy');
        * @public
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
                 * @event Toggler#destroy.toggler
                 */
                $elt.trigger('destroy.' + ns);
            });
        }
   };
   
    //Register the toggler to behave as a jQuery plugin.
    Pluginifier.register(ns, Toggler, {
        expose : ['toggle']
    });
   
   /**
    * The only exposed function is used to start listening on data-attr
    * 
    * @public
    * @example define(['ui/toggler'], function(toggler){ toggler($('rootContainer')); });
    * @param {jQueryElement} $container - the root context to listen in
    */
   return function listenDataAttr($container){
       
        new DataAttrHandler('toggle', {
            container: $container,
            listenerEvent: 'click',
            bubbled: true,
            namespace: dataNs
        }).init(function($elt, $target) {
            var opts = {
                target: $target,
                bindEvent: false
            };
            if($elt.data('hide-text')){
                opts.hideText = $elt.data('hide-text');
            }
            $elt.toggler(opts);
        }).trigger(function($elt) {
            $elt.toggler('toggle');
        });
    };
});

