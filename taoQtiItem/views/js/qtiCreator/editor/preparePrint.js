define([
    'jquery'
], function ($) {
    'use strict';

    /**
     * Prepare item for printing.
     *
     * @author <a href="mailto:dieter@taotesting.com">Dieter Raber</a>
     */
    var preparePrint = function () {

        function initHideOnPrint(elem) {
            elem.siblings().each(function () {
                $(this).addClass('item-no-print');
            });
            return elem.parent();
        }

        var parent = initHideOnPrint($('#item-editor-scope').parent());
        while (parent.length && parent.get(0).nodeName.toLowerCase() !== 'body') {
            parent = initHideOnPrint(parent);
        }

        $('#item-editor-toolbar, .item-editor-sidebar').addClass('item-no-print');
    };

    $('#print-trigger').on('click', function () {
        window.print();
    });

    return preparePrint;
});


