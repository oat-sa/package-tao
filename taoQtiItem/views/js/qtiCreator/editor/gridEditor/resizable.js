define([
    'lodash',
    'jquery',
    'taoQtiItem/qtiCreator/editor/gridEditor/config',
    'taoQtiItem/qtiCreator/editor/gridEditor/helper',
    'taoQtiItem/qtiCreator/helper/qtiElements',
    'jqueryui'
], function(_, $, config, helper, qtiElements){
    "use strict";
    var _syncHandleHeight = function($row){
        var h = $row.height() - parseFloat($row.children('[class^="col-"], [class*=" col-"]').css('margin-bottom'));
        $row.find('.grid-edit-resizable-zone').height(h);
    };

    var _createResizables = function createResizables($el){

        var marginWidth = parseFloat($el.find('[class^="col-"]:last, [class*=" col-"]:last').css('margin-left')),
            activeWidth = 20;



        $el.find('[class^="col-"], [class*=" col-"]').each(function(){

            var $col = $(this);

            //@todo this should be more generic
            //see draggable etc for more references
            if($col.parent().hasClass('fixed-grid-row')) {
                return true;
            }

            if($col.children('.grid-edit-resizable-zone').length){
                //already created, next !
                return true;
            }

            var $nextCol = $col.next(),
                $row = $col.parent('.grid-row'),
                offset = $col.offset(),
                max = 12,
                min = qtiElements.is($col.data('qti-class'), 'interaction') ? config.min.interaction : config.min.text,
                nextMin = qtiElements.is($nextCol.data('qti-class'), 'interaction') ? config.min.interaction : config.min.text,
                unitWidth = $row.width() / max;

            var activeHeight = $row.height() - parseFloat($col.css('margin-bottom'));
            var $activeZone = $('<div>', {'class' : 'grid-edit-resizable-zone grid-edit-resizable-zone-active'}).css({top : 0, right : -(marginWidth + (activeWidth - marginWidth) / 2), width : activeWidth, height : activeHeight});
            var $handle = $('<span>', {'class' : 'grid-edit-resizable-handle'});
            $activeZone.append($handle);
            $col.append($activeZone);

            var _syncOutlineHeight = function(){
                var h = $row.height() - parseFloat($col.css('margin-bottom'));
                $col.find('.grid-edit-resizable-outline').height(h);
                $activeZone.height(h);
            };

            $activeZone.draggable({
                containment : $nextCol.length ? [
                    offset.left + min * unitWidth - marginWidth * 2 + 10,
                    offset.top,
                    offset.left + $col.outerWidth() + marginWidth + $nextCol.outerWidth() - nextMin * unitWidth - activeWidth / 2 - 10,
                    offset.top + $col.height()
                ] : [
                    offset.left + min * unitWidth - marginWidth - activeWidth / 2 - 10,
                    offset.top,
                    $row.offset().left + $row.outerWidth() - marginWidth - activeWidth / 2 - 12,
                    offset.top + $col.height()
                ],
                axis : 'x',
                cursor : 'col-resize',
                start : function(){

                    $col.trigger('resizestart.gridEdit');

                    var $overlay = $('<div>', {'class' : 'grid-edit-resizable-outline'});
                    if($nextCol.length){
                        $overlay.width(parseFloat($col.outerWidth()) + marginWidth + parseFloat($nextCol.outerWidth()));
                    }else{
                        $overlay.css({'width' : '100%', 'border-right-width' : 0});
                    }
                    //store in memory for quick access during resize:
                    $(this).data('overlay', $overlay);
                    $col.append($overlay);
                    $handle.addClass('grid-edit-resizable-active');
                    $el.find('.grid-edit-resizable-zone-active').removeClass('grid-edit-resizable-zone-active');
                    _syncOutlineHeight();

                },
                drag : _.throttle(function(){

                    var width = ($(this).offset().left + activeWidth / 2) - offset.left,
                        units = helper.getColUnits($col),
                        nextUnits = $nextCol.length ? helper.getColUnits($nextCol) : 0;

                    if(!$nextCol.length){
                        //need to resize the outline element:
                        $col.find('.grid-edit-resizable-outline').width($handle.offset().left - offset.left);
                    }

                    if(width + marginWidth * 0 < (units - 1) * unitWidth){//need to compensate for the width of the active zone
                        
                        units--;
                        _setColUnits($col, units);

                        if($nextCol.length){
                            nextUnits++;
                            _setColUnits($nextCol, nextUnits);
                        }

                        _syncOutlineHeight();
                        
                        $col.trigger('resize.gridEdit');
                        
                    }else if(width + marginWidth + 20 > (units + 1) * unitWidth){//need to compensate for the width of the active zone
                        
                        units++;
                        _setColUnits($col, units);

                        if($nextCol.length){
                            nextUnits--;
                            _setColUnits($nextCol, nextUnits);
                        }

                        _syncOutlineHeight();
                        
                        $col.trigger('resize.gridEdit');
                    }

                }, config.throttle.resize),
                stop : function(){

                    $col.find('.grid-edit-resizable-outline').remove();

                    _deleteResizables($el);
                    _createResizables($el);

                    $col.trigger('resizestop.gridEdit');
                }
            }).css('position', 'absolute');

        });

        $el.off('.gridEdit.resizable').on('dragoverstart.gridEdit.resizable', function(){

            _deleteResizables($el);

        }).on('dragoverstop.gridEdit.resizable', function(){

            _createResizables($el);

        }).on('contentChange.gridEdit.resizable', '.grid-row', function(){

            _deleteResizables($el);
            _createResizables($el);
        });

    };

    var _deleteResizables = function _deleteResizables($el){
        
        $el.find('.grid-edit-resizable-zone').remove();
    };

    var _setColUnits = function _setColUnits($elt, newUnits){
        
        if($elt.attr('class').match(/col-([\d]+)/)){
            
            var oldUnits = $elt.attr('data-units');
            var $parentRow = $elt.parent('.grid-row');
            var totalUnits = $parentRow.attr('data-units');
            $parentRow.attr('data-units', totalUnits - oldUnits + newUnits);//update parent
            $elt.attr('data-units', newUnits);//update element
            $elt.removeClass('col-' + oldUnits).addClass('col-' + newUnits);
            
        }else{
            
            throw $.error('the element is not a grid column');
        }
    };

    return {
        create : function($element){
            
            _createResizables($element);
            
            $(window).off('resize.qtiEdit.resizable').on('resize.qtiEdit.resizable', function(){
                
                _deleteResizables($element);
                _createResizables($element);
            });
        },
        destroy : function($element, preserveGlobalEvents){
            
            _deleteResizables($element);
            
            if(!preserveGlobalEvents){
                
                $(window).off('resize.qtiEdit.resizable');
            }
        },
        syncHandleHeight : function($row){
        
            if($row.hasClass('grid-row')){
                _syncHandleHeight($row);
            }else{
                $row.find('.grid-row').each(function(){
                    _syncHandleHeight($(this));
                });
            }
        }

    };
});