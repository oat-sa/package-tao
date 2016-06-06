define([
  'jquery',
], function($){
  'use strict'

  // if no scope is given, messages will be attached to the body
  var messageScope = $(document.body);

  // the actual messenger class
  var messageManager = function(message, options) {

    var setup = $.extend({}, {
      type: 'error',
      delayToClose: 2000,
      scope: messageScope
    }, options),

    collectGarbage = function() {
      var oldMsg = setup.scope.find('.tao-msg');
      if(oldMsg.length){
        oldMsg.remove();
      }
    };

    this.run = function(message) {
      collectGarbage();
      var msg = $('<div>', {
        class: 'tao-msg ' + setup.type,
        text:   message
      }),
      icon = $('<span>', {
        class: 'icon-' + setup.type
      }),
      closer = $('<span>', {
        class: 'icon-remove closer'
      }).on('click', function() {
        msg.remove();
      });
      msg.prepend(icon).append(closer);
      setup.scope.append(msg);

      msg.fadeIn();

      if(setup.delayToClose > -1) {
        window.setTimeout(function(){
          msg.slideUp(
            'slow',
            collectGarbage()
          )
        }, setup.delayToClose)
      }
    }
  };

  var message = function(message, options) {
    var mm = new messageManager(message, options);
    mm.run();
  }
  return message;
});


