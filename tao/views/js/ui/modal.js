define(['jquery', 'core/pluginifier', 'core/dataattrhandler'], function($, Pluginifier, DataAttrHandler){
    'use strict';
    
    /**
     * jQuery modal is an easy to use plugin 
     * which allows you to create modal windows
     * @example $('#modal-window').modal();
     * 
     * @require jquery >= 1.7.0 [http://jquery.com/]
     */

    var pluginName = 'modal';
    var dataNs = 'ui.' + pluginName;
    
    var defaults = {
        modalCloseClass  : 'modal-close',
        modalOverlayClass: 'modal-bg',
        startClosed: false,
        disableClosing: false,
        width: 'responsive',
        minWidth : false,
        vCenter : true
    };


    var Modal = {
       /**
        * Initialize the modal dialog
        * @param {Object} [options] - plugin options
        * @param {String} [options.modalClose = 'modal-close'] - the css class for the modal closer
        * @param {String} [options.modalOverlay = 'modal-bg'] - the css class for the modal overlay element
        * @param {Boolean} [options .disableClosing = false] - to disable the default closers
        * @param {String|Number|Boolean}  [options.width = 'responsive'] - the width behavior, responsive or a fixed value, or default if false
        * @returns {jQueryElement} for chaining
        */
       init: function(options){
          
          //extend the options using defaults
          options = $.extend(true, {}, defaults, options);
          
          return $(this).each(function() {
            var $modal = $(this);
            
            options.modalOverlay = '__modal-bg-' + ($modal.attr('id') || new Date().getTime()); 
            
            //add data to the element
            $modal.data(dataNs, options);
            
            //Initialize the overlay for the modal dialog
            if ($('#'+options.modalOverlay).length === 0) {
               $('<div/>').attr({'id':options.modalOverlay, 'class': options.modalOverlayClass}).insertAfter($modal);
            }
            
            //Initialize the close button for the modal dialog
            if ($('.'+options.modalCloseClass, $modal).length === 0 && !options.disableClosing) {
               $('<div class="' + options.modalCloseClass + '"><span class="icon-close"></span></div>').appendTo($modal);
            }
            
            if(!options.startClosed){
                Modal._open($modal);
            }
            
            /**
             * The plugin have been created.
             * @event Modal#create.modal
             */
            $modal.trigger('create.' + pluginName);
          });
       },

       /**
        * Bind events
        * @param {jQuery object} $element
        * @returns {undefined}
        */
       _bindEvents: function($element){
          var options = $element.data(dataNs);
         
          if(options.width === 'responsive'){ 
              $(window).on('resize.'+pluginName, function(e){
                 e.preventDefault();
                 Modal._resize($element);
              });
          }

          if(!options.disableClosing){
                $('.'+options.modalCloseClass, $element).on('click.'+pluginName, function(e){
                    e.preventDefault();
                    Modal._close($element);
                });

                $('#'+options.modalOverlay).on('click.'+pluginName, function(e){
                    e.preventDefault();
                    Modal._close($element);
                });

                $(document).on('keydown.'+pluginName, function(e) {
                    if(e.keyCode===27){
                        e.preventDefault();
                        Modal._close($element);
                    }
                });
          }
       },

       /**
        * Unbind events
        * @param {jQuery object} $element
        * @returns {undefined}
        */
       _unBindEvents: function($element){
          var options = $element.data(dataNs);
          
          if(options.width === 'responsive'){ 
            $(window).off('resize.'+pluginName);
          }

          $element.off('click.'+pluginName);
          
          if(!options.disableClosing){
              $('.'+options.modalCloseClass, $element).off('click.'+pluginName);
              $('#'+options.modalOverlay).off('click.'+pluginName);
              $(document).off('keydown.'+pluginName);
          }
       },

       /**
        * Open the modal dialog
        * @param {jQuery object} $element
        * @returns {jQuery object}
        */
       _open: function($element){
          var modalHeight = $element.outerHeight(),
              windowHeight = $(window).height(),
              options = $element.data(dataNs);
      
          if (typeof options !== 'undefined'){
       
            //Calculate the top offset
            var topOffset = (options.vCenter || modalHeight > windowHeight) ? 40: (windowHeight-modalHeight)/2;

            Modal._resize($element);

            $element.css({
                'top': '-'+modalHeight+'px',
                'display' : 'block'
            });

            $('#'+options.modalOverlay).fadeIn(300);

            $element.animate({'opacity': '1', 'top':topOffset+'px'});
            $element.addClass('opened');
            Modal._bindEvents($element);

           /**
            * The target has been closed/removed. 
            * @event Modal#closed.modal
            */
            $element.trigger('opened.'+ pluginName);
          }
       },

       /**
        * Close the modal dialog
        * @param {jQuery object} $element
        * @returns {undefined}
        */
       _close: function($element){
           var options = $element.data(dataNs);
       
           Modal._unBindEvents($element);
           
           $('#'+options.modalOverlay).fadeOut(300);
           $element.animate({'opacity': '0', 'top':'-1000px'}, 500, function(){
                $element.css('display', 'none');
           });
           $element.removeClass('opened');
           
           /**
            * The target has been closed/removed. 
            * @event Modal#closed.modal
            */
           $element.trigger('closed.'+ pluginName);
       },
       
       /**
        * Resize the modal window
        * @param {jQuery object} $element
        * @returns {undefined}
        */
       _resize: function($element){
           var options = $element.data(dataNs);
           var windowWidth = parseInt($(window).width(), 10);

           var modalWidth = options.width === 'responsive' ? windowWidth * 0.7 : parseInt(options.width, 10);
           if(options.minWidth && modalWidth < options.minWidth){
                modalWidth = options.minWidth;
           }
           $element.css({
                'width': modalWidth + 'px'
           });
       }
    };


    //Register the modal to behave as a jQuery plugin.
    Pluginifier.register(pluginName, Modal, {
        expose : ['open', 'close']
    });
   
   /**
    * The only exposed function is used to start listening on data-attr
    * 
    * @public
    * @example define(['ui/modal'], function(modal){ modal($('rootContainer')); });
    * @param {jQueryElement} $container - the root context to listen in
    */
   return function listenDataAttr($container){
        new DataAttrHandler('modal', {
            container: $container,
            listenerEvent: 'click',
            namespace: dataNs
        }).init(function($elt, $target) {
            $target.modal();
        });
    };

});
