define([
    'jquery',
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/editor/styleEditor/styleEditor',
    'taoQtiItem/qtiCreator/helper/popup',
    'lib/farbtastic/farbtastic'
], function ($, _, __, styleEditor, popup) {
    'use strict';

    // based on http://stackoverflow.com/a/14238466
    // this conversion is required to communicate with farbtastic
    function rgbToHex(color) {

        function toHexPair(inp) {
            return ('0' + parseInt(inp, 10).toString(16)).slice(-2);
        }

        // undefined can happen when no color is defined for a particular element
        // isString on top of that should cover all sorts of weird input
        if(!_.isString(color)) {
            return color;
        }

        var rgbArr = /rgb\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)/i.exec(color);

        // color is not rgb
        if(!_.isArray(rgbArr) || rgbArr.length !== 4) {
            return color;
        }

        return '#' + toHexPair(rgbArr[1]) + toHexPair(rgbArr[2]) + toHexPair(rgbArr[3]);
    }

    var colorSelector = function () {
        var colorPicker = $('.item-editor-color-picker'),
            widget = colorPicker.find('.color-picker'),
            widgetBox = colorPicker.find('.color-picker-container'),
            titleElement =  colorPicker.find('#color-picker-title'),
            input = colorPicker.find('#color-picker-input'),
            resetButtons = colorPicker.find('.reset-button'),
            colorTriggers = colorPicker.find('.color-trigger'),
            colorTriggerLabels = colorPicker.find('label'),
            currentProperty = 'color',
            widgetObj,
            $doc = $(document);



        /**
         * Widget title
         *
         * @param property
         * @param trigger
         */
        var setTitle = function (property, trigger) {
            titleElement.text(trigger.parent().find('label').text());
        };

        /**
         * Trigger button background
         */
        var setTriggerColor = function() {
            colorTriggers.each(function () {
                var $trigger = $(this),
                    target   = $trigger.data('target'),
                    $target  = $(target),
                    style    = styleEditor.getStyle() || {},
                    value;
                // elements have a color from usage of style editor
                if(style[target] && style[target][$trigger.data('value')]) {
                    value = style[target][$trigger.data('value')];
                    $trigger.css('background-color', value);
                    $trigger.attr('title', rgbToHex(value));
                }
                // elements have a default color from tao css
                else if($target.css($trigger.data('value'))) {
                    value = $target.css($trigger.data('value'));
                    $trigger.css('background-color', value);
                    $trigger.attr('title', rgbToHex(value));
                }
                // elements have no color at all
                else {
                    $trigger.css('background-color', '');
                    $trigger.attr('title', __('No value set'));
                }

            });
        };

        widgetObj = $.farbtastic(widget).linkTo(input);

        // event received from modified farbtastic
        widget.on('colorchange.farbtastic', function (e, color) {
            styleEditor.apply(widget.prop('target'), currentProperty, color);
            setTriggerColor();
        });


        // open color picker
        setTriggerColor();
        colorTriggers.add(colorTriggerLabels).on('click', function () {
            var $tmpTrigger = $(this),
                $trigger = (this.nodeName.toLowerCase() === 'label' ? $tmpTrigger.parent().find('.color-trigger') : $tmpTrigger),
                value = $trigger.css('background-color');

            widget.prop('target', $trigger.data('target'));
            widgetBox.hide();
            currentProperty = $trigger.data('value');
            setTitle(currentProperty, $trigger);
            widgetObj.setColor(rgbToHex($trigger.css('background-color')));
            widgetBox.show();
        });

        // close color picker, when clicking somewhere outside or on the x
        $doc.on('mouseup', function(e) {
            if($(e.target).hasClass('closer')) {
                widgetBox.hide();
                return false;
            }

            if (!widgetBox.is(e.target) && widgetBox.has(e.target).length === 0) {
                widgetBox.hide();
                return false;
            }
        });

        // close color picker on escape
        $doc.on('keyup', function(e){
            if (e.keyCode === 27) {
                widgetBox.hide();
                return false;
            }
        });


        // reset to default
        resetButtons.on('click', function () {
            var $this = $(this),
                $colorTrigger = $this.parent().find('.color-trigger'),
                target = $colorTrigger.data('target'),
                value = $colorTrigger.data('value');
            styleEditor.apply(target, value);
            setTriggerColor();
        });


        $doc.on('customcssloaded.styleeditor', setTriggerColor);
    };

    return colorSelector;
});
