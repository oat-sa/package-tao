define(['jquery', 'lodash'], function($, _){
    "use strict";
    var _eventName = 'arrowenter.gridEdit.gridDragDrop';

    //build arrow for inter-column insertion:
    var _buildArrows = function _buildArrows($col, options){

        var defaults = {
            insertZoneWidth : 20,
            marginWidth : 0
        };

        options = _.extend(defaults, options);

        //create inter-column insertion helper
        var $insertRight = $('<div>', {'class' : 'grid-edit-insert-box'})
            .css({
                top : 0,
                width: options.insertZoneWidth,
                right : -(options.marginWidth + (options.insertZoneWidth - options.marginWidth) / 2),
                height : $col.parent('.grid-row').height() - parseFloat($col.css('margin-bottom'))
            })
            .append($('<div>', {'class' : 'grid-edit-insert-square'}))
            .append($('<div>', {'class' : 'grid-edit-insert-triangle'}))
            .append($('<div>', {'class' : 'grid-edit-insert-line'}))
            .on('mouseenter', '.grid-edit-insert-triangle, .grid-edit-insert-square', function(){
                $col.trigger(_eventName, ['right']);
                _removeArrows($col);
            });

        var $insertLeft = $insertRight
            .clone()
            .css({'left' : -(options.marginWidth + 5), 'right' : 'auto'})
            .on('mouseenter', '.grid-edit-insert-triangle, .grid-edit-insert-square', function(){
                $col.trigger(_eventName, ['left']);
                _removeArrows($col);
            });

        $col.append($insertRight).append($insertLeft);

        _arrowFall($insertRight, function(){
            $col.trigger(_eventName, ['right']);
            _removeArrows($col);
        });

        _arrowFall($insertLeft, function(){
            $col.trigger(_eventName, ['left']);
            _removeArrows($col);
        });

    };

    //remove arrows:
    var _removeArrows = function _removeArrows($col){
        $col.find('.grid-edit-insert-box').remove();
    };

    //animate arrow fall:
    var _arrowFall = function _arrowFall($insert, callback){

        var interval = null;
        $insert.on('mouseenter', function(e){

            var relY = e.pageY - $(this).offset().top
                - parseInt($(this).find('.grid-edit-insert-triangle').outerHeight())
                - parseInt($(this).find('.grid-edit-insert-square').outerHeight());

            var $arrow = $(this).find('.grid-edit-insert-triangle, .grid-edit-insert-square');

            var t = 0;
            interval = setInterval(function(){
                var top = 0.5 * 9.81 * Math.pow(t, 2) / 100;
                $arrow.css('top', top);

                if(top > relY){
                    clearInterval(interval);
                    callback();
                }

                t++;
            }, 10);
        }).on('mouseleave', function(){
            $(this).find('.grid-edit-insert-triangle, .grid-edit-insert-square').css('top', 0);
            clearInterval(interval);
        });
    };

    return {
        create : _buildArrows
    };
});
