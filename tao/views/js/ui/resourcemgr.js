define([
    'jquery',
    'lodash', 
    'core/pluginifier', 
    'core/dataattrhandler', 
    'ui/modal',
    'ui/resourcemgr/fileBrowser',
    'ui/resourcemgr/filePreview',
    'ui/resourcemgr/fileSelector',
    'tpl!ui/resourcemgr/tpl/layout'
], function($, _, Pluginifier, DataAttrHandler, modal, fileBrowser, filePreview, fileSelector, layout){

    'use strict';
   
   var ns = 'resourcemgr';
   var dataNs = 'ui.' + ns;
   
   var defaults = {
        mediaSources    : [{'root' : 'local', 'path' : '/'}],
        open            : true,
        appendContainer : '.tao-scope:first',
        title           : ''
   };
   
   /** 
    * The ResourceMgr component helps you to browse and select external resources.
    * @exports ui/resourcemgr
    */
   var resourceMgr = {

        /**
         * Initialize the plugin.
         * 
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').resourcemgr({
         *
         *  });
         * 
         * @constructor
         * @param {Object} options - the plugin options
         * @param {Sring|Boolean} [options.bindEvent = 'click'] - the event that trigger the toggling
         * @param {String} options.url - the URL of the service used to retrieve the resources.
         * @fires ResourceMgr#create.resourcemgr
         * @returns {jQueryElement} for chaining
         */
        init : function(options){
            var self = resourceMgr;
            
            //get options using default
            options = _.defaults(options, defaults);
           
            return this.each(function() {
                var $elt = $(this);
                var $target; 

                if(!$elt.data(dataNs)){

                    //add data to the element
                    $elt.data(dataNs, options);

                    //auto bind events configured in options
                    _.functions(options).forEach(function(eventName){
                        $elt.on(eventName + '.' + ns, function(){

                            options[eventName].apply($elt, arguments);
                        });
                    });
                  
                    $target = options.$target || self._createTarget($elt);
            
                    $target.modal({
                        startClosed: true,
                        minWidth : 900
                    });

                    //rethrow some events
                    $target.on('select.' + ns, function(e, files){
                        self._close($elt);
                        $elt.trigger(e, [files]);
                    });
                    $target.on('closed.modal', function(){
                        $elt.trigger('close.' + ns);
                    });
                    //initialize the components
                    var $fileBrowser    = $('.file-browser .file-browser-wrapper', $target);
                    if(options.mediaSourcesUrl){
                        $.getJSON(options.mediaSourcesUrl)
                            .done(function(data){
                                var mediaSources = data || defaults.mediaSources;
                                for(var i = 0; i < mediaSources.length; i++){
                                    options.root = mediaSources[i].root;
                                    options.path = mediaSources[i].path;
                                    $fileBrowser.append('<div class="'+options.root+'"><ul class="folders"></ul></div>');
                                    fileBrowser(options);
                                }

                            })
                            .fail(function(){
                                for(var i = 0; i < defaults.mediaSources.length; i++){
                                    options.root = defaults.mediaSources[i].root;
                                    options.path = defaults.mediaSources[i].path;
                                    $fileBrowser.append('<div class="'+options.root+'"><ul class="folders"></ul></div>');
                                    fileBrowser(options);
                                }
                            });
                    }
                    else if(options.path && options.root){
                        $fileBrowser.append('<div class="'+options.root+'"><ul class="folders"></ul></div>');
                        fileBrowser(options);
                    }

                    $fileBrowser.find('li.root:last').addClass('active');
                    fileSelector(options);
                    filePreview(options);

                    /**
                     * The plugin have been created.
                     * @event ResourceMgr#create.resourcemgr
                     */
                    $elt.trigger('create.' + ns, [$target[0]]);
                            
                    if(options.open){
                        self._open($elt);
                    }
                } else {
                    options = $elt.data(dataNs);
                    if(options.open){
                        self._open($elt);
                    }
                }
            });
       },
      
       _createTarget : function($elt){
            var options = $elt.data(dataNs);
            if(options){
                //create an identifier to the target content
                options.targetId = 'resourcemgr-' + $(document).find('.resourcemgr').length;
                
                //generate
                options.$target  = $(layout({
                    title   :   options.title || ''
                }));
                
                options.$target.attr('id', options.targetId)
                    .css('display', 'none')
                    .appendTo(options.appendContainer);             
 
                $elt.data(dataNs, options);
            }
            return options.$target;
       },

       _open : function($elt){
            var options = $elt.data(dataNs);
            if(options && options.$target){
                options.$target.modal('open');
                
                /**
                 * Open the resource manager.
                 * @event ResourceMgr#open.resourcemgr
                 */
                $elt.trigger('open.' + ns);
            }
       }, 
               
       _close : function($elt){
            var options = $elt.data(dataNs);
            if(options && options.$target){
                options.$target.modal('close');
            }
       }, 
       /**
        * Destroy completely the plugin.
        * 
        * Called the jQuery way once registered by the Pluginifier.
        * @example $('selector').resourcemgr('destroy');
        * @public
        */
       destroy : function(){
            this.each(function() {
                var $elt = $(this);
                var options = $elt.data(dataNs);
                $elt.data(dataNs, null);
                if(options.bindEvent !== undefined && options.bindEvent !== false){
                    $elt.off(options.bindEvent);
                }
                if(options.targetId){
                    $('#' + options.targetId).remove();
                } 

                $(window).off('resize.resourcemgr');
   
                /**
                 * The plugin have been destroyed.
                 * @event ResourceMgr#destroy.resourcemgr
                 */
                $elt.trigger('destroy.' + ns);
            });
        }
   };
   
   //Register the resourcemgr to behave as a jQuery plugin.
   Pluginifier.register(ns, resourceMgr);
});

