define([
    'jquery',
    'taoQtiItem/qtiCreator/editor/styleEditor/styleEditor'
], function ($, styleEditor) {
    'use strict'

    var fontSizeChanger = function () {
        var fontSizeChanger = $('#item-editor-font-size-changer'),
            target = fontSizeChanger.data('target'),
            headSelector = target + ' .item-title',
            bodySelector = target + ' .qti-itemBody',
            headFontSize = parseInt($(headSelector).css('font-size'), 10),
            bodyFontSize = parseInt($(bodySelector).css('font-size'), 10),
            headBodyDiff = headFontSize - bodyFontSize,
            resetButton =  fontSizeChanger.parents('.reset-group').find('[data-role="font-size-reset"]'),
            input = $('#item-editor-font-size-text');
        
        var resizeFont = function() {
            var headFontSize = bodyFontSize + headBodyDiff;
            styleEditor.apply(headSelector, 'font-size', headFontSize.toString() + 'px');
            styleEditor.apply(bodySelector, 'font-size', bodyFontSize.toString() + 'px');
        };

        fontSizeChanger.find('a').on('click', function(e) {
            e.preventDefault();
            if($(this).data('action') === 'reduce') {
                if(bodyFontSize <= 10) {
                    return;
                }
                bodyFontSize--;
            }
            else {
                bodyFontSize++;
            }
            resizeFont();
            input.val(bodyFontSize);
            $(this).parent().blur();
        });

        input.on('keydown', function(e) {
            var c = e.keyCode;
            return (_.contains([8, 37, 39, 46], c)
                || (c >= 48 && c <= 57)
                || (c >= 96 && c <= 105));
        });

        input.on('blur', function() {
            bodyFontSize = parseInt(this.value, 10);
            resizeFont();
        });

        input.on('keydown', function(e) {
            var c = e.keyCode;
            if(c === 13) {
                input.trigger('blur');
            }
        });

        resetButton.on('click', function () {
            input.val('');
            styleEditor.apply(headSelector, 'font-size');
            styleEditor.apply(bodySelector, 'font-size');
        });

        // style loaded from style sheet
        $(document).on('customcssloaded.styleeditor', function(e, style) {
            if(style[bodySelector] && style[bodySelector]['font-size']) {
                input.val(parseInt(style[bodySelector]['font-size'], 10));
                input.trigger('blur');
            }
            else {
                input.val(parseInt($(bodySelector).css('font-size'), 10));
            }
        });
    };

    return fontSizeChanger;
});

