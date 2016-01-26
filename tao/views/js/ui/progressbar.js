/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @requires jquery
 * @requires lodash
 * @requires core/pluginifier
 */
define(['jquery', 'lodash', 'core/pluginifier'],
function($, _, Pluginifier){
   'use strict';

   var ns = 'progressbar';
   var dataNs = 'ui.' + ns;


   var defaults = {
       disableClass : 'disabled',
       style : 'info',
       value : 0,
       showProgress: false
   };

   /**
    * The Progressbar component.
    * @exports ui/progressbar
    */
   var progressBar = {

        /**
         * Initialize the plugin.
         *
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').progressbar({ value : 15 });
         *
         * @constructor
         * @param {Object} [options] - the plugin options
         * @param {Number} [options.value] - the progress value in %
         * @param {String} [options.style = 'info'] - the progress bar style in info, success, warning, error
         *
         * @fires progressBar#create.progressbar
         * @returns {jQueryElement} for chaining
         */
        init : function(options){
            options = _.defaults(options || {}, defaults);

            return this.each(function() {
                var $elt = $(this);
                var $pgElt, percent;

                if(!$elt.data(dataNs)){

                    options.value = parseInt(options.value, 10);

                    //add data to the element
                    $elt.data(dataNs, options);

                    percent = options.value + '%';

                    $pgElt = $('<span></span>')
                                .width(percent)
                                .attr('title', percent);


                    if(options.showProgress){
                        $pgElt.text(percent);
                    }

                    $elt.addClass('progressbar')
                        .addClass(options.style)
                        .empty()
                        .append($pgElt);

                    /**
                     * The plugin have been created.
                     * @event progressBar#create.progressbar
                     */
                    $elt.trigger('create.' + ns);
                }
            });
       },

       /**
        * Trigger the progress value
        *
        * Called the jQuery way once registered by the Pluginifier.
        *
        * @example $('selector').progressbar('update', 50);
        *
        * @param {jQueryElement} $elt - plugin's element
        * @param {Number} value - the new value
        *
        * @fires progressBar#update.progressbar
        */
       _update : function($elt, value){
           var options = $elt.data(dataNs);
           var $pgElt, percent;

           value = parseInt(value, 10);
           if(value >= 0 && value <= 100){
               percent = value + '%';
               $pgElt = $elt.children('span');

               $pgElt.width( value + '%')
                     .attr('title', percent);

               if(options.showProgress){
                    $pgElt.text(percent);
               }

               options.value = value;
               $elt.data(dataNs, options);

                /**
                 * The progress value has been updated
                 * @event progressBar#create.progressbar
                 */
                $elt.trigger('update.' + ns, value);
            }
       },

       /**
        * Get/Set the value
        *
        * Called the jQuery way once registered by the Pluginifier.
        *
        * @example var value = $('selector').progressbar('value');
        *
        * @param {jQueryElement} $elt - plugin's element
        * @param {Number} [value] - the new value in setter mode only
        * @returns {Number} the value in getter mode
        */
       _value : function($elt, value){
           var options = $elt.data(dataNs);
           if(typeof value !== 'undefined'){
                return progressBar._update($elt, value);
           }

           return options.value;
       },

       /**
        * Destroy completely the plugin.
        *
        * Called the jQuery way once registered by the Pluginifier.
        * @example $('selector').progressbar('destroy');
        *
        * @fires progressBar#destroy.progressbar
        */
       destroy : function(){
            return this.each(function() {
                var $elt = $(this);
                var options = $elt.data(dataNs);
                if(options){

                    $elt.removeClass('progressbar')
                        .empty()
                        .removeData(dataNs);

                    /**
                     * The plugin have been destroyed.
                     * @event progressBar#destroy.progressbar
                     */
                    $elt.trigger('destroy.' + ns);
                }
            });
        }
   };

   //Register the toggler to behave as a jQuery plugin.
   Pluginifier.register(ns, progressBar, {
        expose : ['update', 'value']
   });
});

