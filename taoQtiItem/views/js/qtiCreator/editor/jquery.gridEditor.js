define([
    'jquery',
    'taoQtiItem/qtiCreator/editor/gridEditor/helper',
    'taoQtiItem/qtiCreator/editor/gridEditor/content',
    'taoQtiItem/qtiCreator/editor/gridEditor/draggable',
    'taoQtiItem/qtiCreator/editor/gridEditor/resizable'
], function($, helper, contentHelper, draggable, resizable) {

    'use strict';

    $.fn.gridEditor = function(options) {

        var opts = {};
        var method = '';
        var args = [];
        var ret = undefined;
        if (typeof options === 'object') {
            opts = $.extend({}, $.fn.gridEditor.defaults, options);
        } else if (options === undefined) {
            opts = $.extend({}, $.fn.gridEditor.defaults);//use default
        } else if (typeof options === 'string') {
            if (typeof methods[options] === 'function') {
                method = options;
                args = Array.prototype.slice.call(arguments, 1);
            }
        }

        this.each(function() {
            var $this = $(this);
            if (method) {
                if (isCreated($this)) {
                    ret = methods[method].apply($(this), args);
                } else {
                    $.error('call of method of gridEditor while it has not been initialized');
                }
            } else if (!isCreated($this) && typeof opts === 'object') {
                create($this, opts);
            }
        });

        if (ret === undefined) {
            return this;
        } else {
            return ret;
        }
    };

    $.fn.gridEditor.defaults = {
        'elementClass': 'itemBody'
    };

    var methods = {
        addInsertables: function($elts, options) {
            var $grid = $(this);
            $elts.each(function() {
                draggable.createInsertable($(this), $grid, options);
            });
        },
        createMovables: function($elts) {
            var $grid = $(this);
            $elts.each(function() {
                draggable.createMovable($(this), $grid);
            });
        },
        destroy: function() {
            destroy($(this));
        },
        getContent: function() {
            return getContent($(this));
        },
        setContent: function(content) {
            if (content) {
                setContent($(this), content);
            }
        },
        resizable: function() {
            resizable.create($(this));
        }
    };

    function isCreated($elt) {
        return (typeof $elt.data('qti-grid-options') === 'object');
    }

    function create($elt, options) {

        $elt.data('qti-grid-options', options);

        //initialize grid count
        $elt.find('.grid-row').each(function() {
            var totalUnits = 0;
            $(this).children('[class*=" col-"], [class^="col-"]').each(function() {
                totalUnits += helper.setUnitsFromClass($(this));
            });
//            $(this).attr('data-units', parseInt(totalUnits));//useless??
        });
    }

    function destroy($elt) {
        contentHelper.destroyGridWidgets($elt);
    }

    function getContent($el) {
        return contentHelper.getContent($el);
    }

});