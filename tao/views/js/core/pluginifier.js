/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @requires jquery
 * @requires lodash
 */
define(['jquery', 'lodash'], function($, _){
   'use strict';

   /**
    * Abstract plugin used to provide common behavior to the plugins
    */
   var basePlugin = {

        /**
         * Set options of the plugin
         *
         * @example $('selector').pluginName('options', { key: value });
         * @param  {String} dataNs - the data namespace
         * @param  {String} ns - the event namespace
         * @param  {Object} options - the options to set
         */
        options : function(dataNs, ns, options){
            return this.each(function(){
                var $elt = $(this);
                var currentOptions = $elt.data(dataNs);
                if(currentOptions){
                    $elt.data(dataNs, _.merge(currentOptions, options));
                }
            });
        },

       /**
        * Disable the component. 
        * 
        * It can be called prior to the plugin initilization.
        * 
        * Called the jQuery way once registered by the Pluginifier.
        * @example $('selector').pluginName('disable');
        * @param  {String} dataNs - the data namespace
        * @param  {String} ns - the event namespace
        * @fires  basePlugin#disable.ns
        */
       disable : function(dataNs, ns){
            return this.each(function(){
                var $elt = $(this);
                var options = $elt.data(dataNs);
                if(options){
                    $elt.addClass(options.disableClass || 'disabled')
                        .trigger('disable.'+ ns);
                }
            });
       },

       /**  
        * Enable the component. 
        * 
        * Called the jQuery way once registered by the Pluginifier.
        * @example $('selector').pluginName('enable');
        * @param  {String} dataNs - the data namespace
        * @param  {String} ns - the event namespace
        * @fires  basePlugin#enable.ns
        */
       enable : function(dataNs, ns){
            return this.each(function(){
                var $elt = $(this);
                var options = $elt.data(dataNs);
                if(options){
                    $elt.removeClass(options.disableClass || 'disabled')
                       .trigger('enable.'+ ns);
                }
            });
       },
    };
   
   /** 
    * Helps you to create a jQuery plugin, the Cards way
    * @exports core/pluginifer
    */
    var Pluginifier = {
        
        /**
         * Regsiter a new jQuery plugin, the Cards way
         * @param {string} pluginName - the name of the plugin to regsiter. ie $('selector').pluginName();
         * @param {Object} plugin - the plugin as a plain object 
         * @param {Function} plugin.init - the entry point of the plugin is always an init method
         * @param {Object} [config] - plugin configuration 
         * @param {String} [config.ns = pluginName] - plugin namespace (used for events and data-attr)
         * @param {String} [config.dataNs = ui.pluginName] - plugin namespace (used for events and data-attr)
         * @param {Array<String>} [config.expose] - list of methods to expose
         */
        register : function(pluginName, plugin, config){
            config      = config  || {};
            var ns      = config.ns || pluginName.toLowerCase();
            var dataNs  = config.dataNs || 'ui.' + ns;
            var expose  = config.expose || [];

            //checks
            if(_.isFunction($.fn[pluginName])){
                return $.error('A plugin named ' + pluginName + ' is already registered');
            }
            if(!_.isPlainObject(plugin) || !_.isFunction(plugin.init)){
                return $.error('The object to register as a jQuery plugin must be a plain object with an `init` method.');
            }
            
            //configure and augments the plugin
            _.assign(plugin, _.transform(basePlugin, function(result, prop, key){
                if(_.isFunction(prop)){
                    result[key] = _.partial(basePlugin[key], dataNs, ns);
                }
            }));
            
            //set up public methods to wrap privates the jquery way
            _.forEach(expose, function(toExposeName){
                var privateMethod = toExposeName; 
                var publicMethod  = toExposeName; 
                if(!/^_/.test(expose)){
                    privateMethod = '_' + privateMethod;
                } else {
                    publicMethod = publicMethod.replace(/^_/, '');
                }
        
                //do not override if exists
                if(_.isFunction(plugin[privateMethod]) && !_.isFunction(plugin[publicMethod])){
                    plugin[publicMethod] = function(){
                        var returnValue;
                        var args = Array.prototype.slice.call(arguments, 0);
                        this.each(function(){
                            //call plugin._method($element, [remainingArgs...]);
                            returnValue = plugin[privateMethod].apply(plugin, [$(this)].concat(args));
                        });
                        return returnValue || this;
                    };
                }
            });

            // map $('selector').pluginName() to plugin.init
            // map $('selector').pluginName('method', params) to plugin.method(params) to plugin._method($elt, params);
            // disable direct call to private (starting with _) methods
            $.fn[pluginName] = function(method){
                if(plugin[method]){
                     if(/^_/.test(method)){
                         $.error( 'Trying to call a private method `' + method + '`' );
                     } else {
                         return plugin[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
                     }
                } else if ( typeof method === 'object' || ! method) {
                     return plugin.init.apply( this, arguments );
                } 
                $.error( 'Method ' + method + ' does not exist on plugin' );
            };
        }
    };
   
    return Pluginifier;
});

