/**
 * This is an extension of http://refreshless.com/nouislider/ which means all documentation
 * on $.noUiSlider applies to this one too.
 *
 * On top of that it adds responsive markers with or without labels to the slider.
 * Arguments for the extension can either be added to the configuration object of $.noUiSlider
 * or be passed with data attributes.
 *
 * options.stepPosition || slider.data('step-position')
 *      where to place the markers, valid values are
 *      - before (above or left depending on orientation)
 *      - after (below or right depending on orientation)
 * -when using the data - attribute no value is also accepted
 *      stepPosition defaults to 'after'
 *      Essentially it adds a homonymous CSS class to the marker container.
 *
 * options.labels || slider.data('step-labels')
 *      whether you want text on the markers or not, valid values are
 *      - true
 *      - '%smm', a super primitive sprintf() that results in '10mm' or whatever text you use
 *      - in the case of data-attribute no value is also accepted
 *
 * Examples:
 * <div class="slider" data-step-position="before" data-step-labels></div>
 * <div class="slider" data-step-labels></div>
 * <div class="slider" data-step-labels="%s%"></div>
 *
 * @requires $.noUiSlider
 */
define(['jquery', 'lodash', 'nouislider'], function($, _) {

    $.fn.labeledSlider = function(options) {

        options = options || {};
        this.each(function() {
            var slider = $(this);

            if (!options.step || !options.range) {
                slider.noUiSlider(options);
                return false;
            }

            var stepPosition = options.stepPosition || slider.data('step-position') || '';
            delete(options.stepPosition);

            var labels = options.labels || slider.data('step-labels');
            delete(options.labels);

            slider.noUiSlider(options);

            // does it have labels?
            var hasLabels = (function() {
                return _.isString(labels)
            }());

            // continue if no markers are required
            if(!hasLabels) {
                return true;
            }

            // where should the the markers be placed?
            var markerPosition = (function(stepPosition, labels) {

                // 1. position not set but indicated by labels
                // default to after
                if (!stepPosition && _.isString(labels)) {
                    stepPosition = 'after';
                }

                // 2. position given but no value
                // default to after
                if (stepPosition === '') {
                    stepPosition = 'after';
                }

                // test validity of value, must be either before|after
                if (_.contains(['before', 'after'], stepPosition)) {
                    return stepPosition;
                }
                throw ('Invalid value ' + stepPosition + ' for options.stepPosition');
            }(stepPosition, labels));

            if (!markerPosition) {
                return false;
            }

            var sizeFn = (function() {
                var fn = {
                    horizontal: {
                        key: 'width',
                        outer: 'outerWidth'
                    },
                    vertical: {
                        key: 'height',
                        outer: 'outerHeight'
                    }
                };
                return fn[(options.orientation || 'horizontal')];
            }());

            var excessSize = 0;

            var wrapper, spans;

            var markers = (function(format) {
                format = format || '%s';

                var max = options.range.max + options.step,
                    markerSize = 100 / (((options.range.max - options.range.min) / options.step) + 1).toString() + '%',
                    i,
                    unitProps = {},
                    boxProps = {};

                boxProps['class'] = 'step-marker clearfix ' + markerPosition;

                // horizontal
                if (sizeFn['key'] === 'width') {
                    excessSize = ((slider[sizeFn['outer']]() * 100 / slider[sizeFn['key']]()) + (options.step * 100 / (options.range.max - options.range.min)) - 100);
                    boxProps.width = (100 + excessSize).toString() + '%';
                }
                // vertical
                else {
                    excessSize = (slider[sizeFn['outer']]() / options.step) * 2;
                    boxProps.height = slider.outerHeight() + excessSize + 'px';
                }

                var markers = $('<div/>', boxProps);

                for (i = options.range.min; i < max; i += options.step) {
                    unitProps.html = (hasLabels ? format.replace('%s', i.toString()) : '');
                    unitProps[sizeFn['key']] = markerSize;
                    markers.append($('<span>', unitProps));
                }
                return markers;
            }(labels));


            // horizontal
            if (sizeFn['key'] === 'width') {
                slider.append(markers);
                markers.css({
                    left: (excessSize / -2).toString() + '%'
                });
            }
            // vertical
            else {
                wrapper = (function(){
                    slider.wrap('<div class="noUi-vertical-wrapper">');
                    return slider.parent();
                }());
                spans = markers.find('span');
                wrapper.height(slider.outerHeight()).append(markers);
                markers.css({
                    top: (excessSize / -2)
                });
                spans.css('line-height', spans.height() + 'px');
            }

        });

        return this;
    };

});
