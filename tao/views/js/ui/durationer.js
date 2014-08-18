/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['jquery', 'lodash', 'i18n', 'core/pluginifier', 'handlebars', 'moment'], function($, _, __, Pluginifier, Handlebars, moment){
   'use strict';
   
   var ns = 'durationer';
   var dataNs = 'ui.' + ns;
   
   var defaults = {
       format : 'HH:mm:ss',
       separator : ':',
       wrapperClass : 'duration-ctrl-wrapper',
       ctrlClass : 'duration-ctrl',
       disableClass : 'disabled',
       title : {
            hours : __('hours'),
            minutes : __('minutes'),
            seconds: __('seconds')
       }
   };
   
   //the template used for each of the 3 part of the duration
   var fieldTmpl = Handlebars.compile(
        "<input type='text' id='{{id}}-{{type}}' data-duration-type='{{type}}' class='{{ctrlClass}}' value='{{value}}' title='{{title}}' />"
    );
  
   
   /** 
    * The Durationer component creates a widget to manage time duration using separate number inputs.
    * This plugin applies on an text input with the result of the widget sync with it.
    * Now only time is supported.
    * 
    * todo this plugin should support different widget like dropdowns... Now only the incrementer is implemented 
    * 
    * @exports ui/durationer
    */
   var Durationer = {
       
        /**
         * Initialize the plugin.
         * 
         * Called the jQuery way once registered by the Pluginifier. 
         * This plugin only applies on input text elements.
         * 
         * @see http://momentjs.com/docs/#/parsing/string-format/
         *  
         * @example $('selector').durationer({format : 'HH:mm:ss'});
         * @public
         * 
         * @constructor
         * @param {Object} [options] - the plugin options
         * @param {string} [format = 'HH:mm:ss'] - the format of the duration value got from
         * @returns {jQueryElement} for chaining
         */
        init : function(options){
            var self = Durationer;
            
            //get options using default
            options = _.defaults(options || {}, defaults);
           
            return this.each(function() {
                var $elt = $(this);
                
                if(!$elt.data(dataNs)){
                    //basic type checking
                    if(!$elt.is('input[type="text"]')){
                        $.error('The durationer plugin applies only on input element of type text');
                    } else {
                        options.id = $elt.attr('id') || $elt.attr('name') || 'durationer-' + new Date().getTime();


                        var duration = moment($elt.val(), options.format);

                        //hide the element
                        $elt.hide();

                        self._insertField($elt, options, duration.hours(), 'hours');
                        self._insertField($elt, options, duration.minutes(), 'minutes');
                        self._insertField($elt, options, duration.seconds(), 'seconds');

                        if(options.separator){
                            $elt.siblings('.' + options.wrapperClass + ':not(:last)')
                                .after('<span class="separator">:</span>');  
                        }
    
                        //keep a ref to the incrementer elements
                        options.$ctrls = $elt.siblings('.' + options.wrapperClass).children('input');

                        options.$ctrls.on('change', function(){
                            self._syncToField($elt);
                        });

                        $elt.on('change', function(e){
                           if(e.namespace !== ns){
                               self._syncFromField($elt);
                           } 
                        });

                        $elt.data(dataNs, options);
                        
                        /**
                         * The plugin have been created.
                         * @event Durationer#create.durationer
                         */
                        $elt.trigger('create.' + ns);
                    }
                }
            });
       },
       
       /**
        * Insert one of the duration control field, as an incrementer
        * @private
        * @param {jQueryElement} $elt - the plugin element
        * @param {Object} options - the plugin options (not yet set into the data space)
        * @param {string} value - the current field value
        * @param {string} type - which field to insert (hours, minutes or seconds)
        */
       _insertField : function($elt, options, value, type){
            var data = _.defaults({
                type : type,
                value : value,
                title : options.title[type]
            }, options);
            $(fieldTmpl(data))
                .insertBefore($elt)
                .val(value)
                .incrementer({
                    min: 0, 
                    max: (type === 'hours') ? 23 : 59,
                    incrementerWrapperClass : options.wrapperClass
                });
       },
       
       /**
        * Synchronize the value of the controls from the element
        * @private
        * @param {jQueryElement} $elt - the plugin element
        */
       _syncFromField : function($elt){
           var options = $elt.data(dataNs);
           var current = moment($elt.val(), options.format);
            
           options.$ctrls.each(function(){
               var $field = $(this);
               if(current[$field.data('duration-type')]){
                   $field.val(current[$field.data('duration-type')]());
               }
           });
       },
       
       /**
        * Synchronize the value of the controls to the element
        * @private
        * @param {jQueryElement} $elt - the plugin element
        */
       _syncToField : function($elt){
           var options = $elt.data(dataNs);
           var current = moment($elt.val(), options.format);
            
           options.$ctrls.each(function(){
               var $field = $(this);
               if(current[$field.data('duration-type')]){
                   current[$field.data('duration-type')]($field.val());
               }
           });
           
           $elt.val(current.format(options.format));
           
           $elt.trigger('update.' + ns)
                   .trigger('change');
       },
       
       /**
        * Destroy completely the plugin.
        * 
        * Called the jQuery way once registered by the Pluginifier.
        * @example $('selector').durationer('destroy');
        * @public
        */
       destroy : function(){
            this.each(function() {
                var $elt = $(this);
                var options = $elt.data(dataNs);
                
                $elt.siblings('.' + options.wrapperClass).remove();
                $elt.siblings('.separator').remove();
                $elt.removeData(dataNs);
                
                /**
                 * The plugin have been destroyed.
                 * @event Durationer#destroy.durationer
                 */
                $elt.trigger('destroy.' + ns);
            });
        }
   };
   
   //Register the durationer to behave as a jQuery plugin.
   Pluginifier.register(ns, Durationer);
   
   /**
    * The only exposed function is used to start listening on data-attr
    * 
    * @public
    * @example define(['ui/durationer'], function(durationer){ durationer($('rootContainer')); });
    * @param {jQueryElement} $container - the root context to listen in
    */
   return function listenDataAttr($container){

       $container.find('[data-duration]').each(function(){
           var $elt = $(this);
           var format = $elt.data('duration');
           var options = ($.trim(format).length > 0) ? {format : format} : {};
           $elt.durationer(options);
       });
    };
});

