define([
    'jquery',
    'lodash'
], function($, _){
    'use strict';

    var itemStylesheetHandler = (function(){

        var attach = function(stylesheets) {
            var head = $('head'), link;

             $('body').addClass('tao-scope');

             // relative links with cache buster
            _(stylesheets).forEach(function(stylesheet){
                link = (function() {
                    var _link = $(stylesheet.render()),
                        _href = _link.attr('href'),
                        _sep  = _href.indexOf('?') > -1 ? '&' : '?';

                    if(_href.indexOf('/') === 0) {
                        _href = _href.slice(1);
                    }
                    _link.attr('href', _href + _sep + (new Date().getTime()).toString());
                    return _link;
                }());
                // this bit seems to get IE to co-operate
                setTimeout(function() {
                    head.append(link);
                }, 500)
            });

        };

        return {
            attach: attach
        }

    }());

    return itemStylesheetHandler;
});