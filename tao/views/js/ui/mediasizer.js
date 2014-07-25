/**
 * @author Dieter Raber <dieter@taotesting.com>
 * @requires jquery
 * @requires core/pluginifier
 */
define([
    'jquery',
    'core/pluginifier',
    'tpl!ui/mediasizer/mediasizer',
    'nouislider',
    'tooltipster'
], function ($, Pluginifier, tpl) {
    'use strict';

    var ns = 'mediasizer';
    var dataNs = 'ui.' + ns;

    var defaults = {
        disableClass: 'disabled'
    };

    var supportedMedia = ['img'];


    function _round(value, decimals) {
        if (decimals === undefined) {
            decimals = 1;
        }
        var factor = 1;
        while (decimals--) {
            factor *= 10;
        }
        return Math.round(value * factor) / factor;

    }

    /**
     * The MediaSizer component, that helps you to show/hide an element
     * @exports ui/toggler
     */
    var MediaSizer = {


        /**
         * Creates object that contains all size related data of the medium (= image, video, etc.)
         *
         * @param $elt
         * @returns {{px: {natural: {width: number, height: number}, current: {width: number, height: number}}, '%': {natural: {width: number, height: number}, current: {width: number, height: null|number}}, ratio: {natural: number, current: number}, containerWidth: number}}
         * @private
         */
        _getSizeProps: function ($elt) {
            var options = $elt.data(dataNs),
                $medium = options.target,
                medium = $medium[0],
                containerWidth = options.parentSelector ? $medium.parents(options.parentSelector).innerWidth() : $medium.parent().innerWidth();

            return {
                px: {
                    natural: {
                        width: medium.naturalWidth,
                        height: medium.naturalHeight
                    },
                    current: {
                        width: medium.width,
                        height: medium.height
                    }
                },
                '%': {
                    natural: {
                        width: 100,
                        height: 100
                    },
                    current: {
                        width: medium.width * 100 / containerWidth,
                        height: null // height does not work on % - this is just in case you have to loop or something
                    }
                },
                ratio: {
                    natural: medium.naturalWidth / medium.naturalHeight,
                    current: medium.width / medium.height
                },
                containerWidth: containerWidth,
                sliders: {
                    '%': {
                        max: 100,
                        start: medium.width * 100 / containerWidth
                    },
                    px: {
                        max: Math.max(containerWidth, medium.naturalWidth),
                        start: medium.width
                    }
                }
            };

        },


        /**
         * Toggle width/height synchronization
         *
         * @param $elt
         * @private
         */
        _initLink: function ($elt) {
            var options = $elt.data(dataNs),
                $mediaSizer = $elt.find('.media-sizer');
            $elt.find('.media-sizer-link').on('click', function () {
                $mediaSizer.toggleClass('media-sizer-synced');
                options.toBeSynced = $mediaSizer.hasClass('media-sizer-synced');
            });
        },


        /**
         * Blocks are the two different parts of the form (either width|height or size)
         *
         * @param $elt
         * @returns {{}}
         * @private
         */
        _initBlocks: function ($elt) {
            var _blocks = {};

            _(['px', '%']).forEach(function (unit) {
                _blocks[unit] = $elt.find('.media-sizer-' + (unit === 'px' ? 'pixel' : 'percent'));
                _blocks[unit].prop('unit', unit);
                _blocks[unit].find('input').data('unit', unit).after($('<span>', {
                    'class': 'unit-indicator',
                    text: unit
                }));
            });

            $elt.find('.media-mode-switch').on('click', function () {
                if (this.checked) {
                    _blocks['px'].hide();
                    _blocks['%'].show();
                }
                else {
                    _blocks['%'].hide();
                    _blocks['px'].show();
                }
            });

            return _blocks;
        },


        /**
         * Initialize the two sliders, one based on pixels the other on percentage
         *
         * @param $elt
         * @returns {{}}
         * @private
         */
        _initSliders: function ($elt) {
            var options = $elt.data(dataNs),
                unit,
                _sliders = {};

            _(options.$blocks).forOwn(function ($block, unit) {
                _sliders[unit] = $block.find('.media-sizer-slider');
                _sliders[unit].prop('unit', unit);
                _sliders[unit].noUiSlider({
                    start: options.sizeProps.sliders[unit].start,
                    range: {
                        'min': 0,
                        'max': options.sizeProps.sliders[unit].max
                    }
                })
                    .on('slide', function () {
                        var $slider = $(this),
                            unit = $slider.prop('unit'),
                            factor,
                            otherFactor,
                            value = $(this).val(),
                            otherValue,
                            otherUnit;


                        if (unit === 'px') {
                            factor = 0;
                            otherFactor = 1;
                            otherUnit = '%';
                            otherValue = value * 100 / options.sizeProps.containerWidth
                        }
                        else {
                            factor = 1;
                            otherFactor = 0;
                            otherUnit = 'px';
                            otherValue = value * options.sizeProps.containerWidth / 100
                        }
                        options.$fields[unit].width.val(_round(value, factor)).trigger('slidechange');

                        // synchronize slider and fields of other unit
                        options.$sliders[otherUnit].val(otherValue);
                        options.$fields[otherUnit].width.val(_round(otherValue, otherFactor)).trigger('slidechange');
                    })
            });

            return _sliders;
        },

        /**
         * Synchronize all parameters
         *
         * @param $elt
         * @param $field
         * @param eventType
         * @private
         */
        _sync: function ($elt, $field, eventType) {
            var options = $elt.data(dataNs),
                unit = $field.prop('unit'),
                dimension = $field.prop('dimension'),
                value = parseFloat($field.val()),
                otherDimension = dimension === 'width' ? 'height' : 'width',
                otherUnit = unit === 'px' ? '%' : 'px',
                otherField = options.$fields[unit][otherDimension],
                otherValue = parseFloat(otherField.val()),
                slider = options.$sliders[unit],
                ratio,
                factor;

            // invalid entries
            if (isNaN(value)) {
                return;
            }

            // recalculate ratio
            ratio = options.sizeProps.ratio.natural;
            if (options.allowCustomRatio && unit === 'px') {
                if (dimension === 'width') {
                    options.sizeProps.ratio.current = value / otherValue;
                }
                else {
                    options.sizeProps.ratio.current = otherValue / value;
                }
                ratio = options.sizeProps.ratio.current;
            }


            console.log({
                unit: unit,
                otherUnit: otherUnit,
                value: value,
                otherValue: otherValue,
                ratio: ratio
            })
            return

            // set slider value
            if (dimension === 'width' && eventType !== 'slidechange') {
                slider.val(value);
            }

            if (unit === 'px') {
                factor = 0;
                otherUnit = '%';
            }
            else {
                factor = 1;
                otherUnit = 'px';
            }


            options.sizeProps[unit].current[dimension] = value;
            if (options.toBeSynced) {
                options.sizeProps[unit].current[otherDimension] = value / ratio;
                options.$fields[unit][otherDimension].val(_round((value / ratio), factor));
            }
            options.sizeProps[otherUnit].current.width = value * 100 / options.sizeProps.containerWidth;
            options.$fields[otherUnit].width.val(value * 100 / options.sizeProps.containerWidth);

        },


        /**
         * Initialize the fields
         *
         * @param $elt
         * @returns {{}}
         * @private
         */
        _initFields: function ($elt) {
            var options = $elt.data(dataNs),
                dimensions = ['width', 'height'],
                field, _fields = {},
                factor,
                self = this;

            _(options.$blocks).forOwn(function ($block, unit) {
                _fields[unit] = {};
                factor = unit === 'px' ? 0 : 1;
                options.$blocks[unit].find('input').each(function () {
                    _(dimensions).forEach(function (dim) {
                        field = options.$blocks[unit].find('[name="' + dim + '"]');
                        // there is no 'height' field for % - $('<input>') is a dummy to avoid checking if the field exists all the time
                        _fields[unit][dim] = field.length ? field : $('<input>');
                        _fields[unit][dim].prop({
                            unit: unit,
                            dimension: dim
                        });
                        _fields[unit][dim].val(_round(options.sizeProps[unit].current[dim], factor));
                        _fields[unit][dim].on('keypress blur slidechange', function (e) {
                            self._sync($elt, $(this), e.type);
                        });
                    });
                });
            });

            return _fields;
        },


        /**
         * Initialize the plugin.
         *
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').mediaSizer({target : $('target') });
         * @public
         *
         * @constructor
         * @returns {*}
         */
        init: function (options) {

            //get options using default
            options = $.extend(true, {}, defaults, options);

            var self = MediaSizer;

            return this.each(function () {
                var $elt = $(this),
                    $target = options.target,
                    type = $target[0].nodeName.toLowerCase();

                if (!_.contains(supportedMedia, type)) {
                    throw new Error('MediaSizer::init() Unsupported element type ' + type);
                }

                if (!$elt.data(dataNs)) {

                    $elt.html(tpl());

                    //add data to the element
                    $elt.data(dataNs, options);

                    options.sizeProps = self._getSizeProps($elt);
                    options.originalSizeProps = _.cloneDeep(options.sizeProps);

                    // options.parentSelector = '[class*="col-"]';

                    options.toBeSynced = $elt.hasClass('media-sizer-synced');
                    options.allowCustomRatio = options.allowCustomRatio || false;

                    options.$blocks = self._initBlocks($elt);
                    options.$fields = self._initFields($elt);
                    options.$sliders = self._initSliders($elt);


                    self._initLink($elt);


                    /**
                     * The plugin have been created.
                     * @event MediaSizer#create.toggler
                     */
                    $elt.trigger('create.' + ns);
                }
            });
        },


        /**
         * Destroy the plugin completely.
         * Called the jQuery way once registered by the Pluginifier.
         *
         * @example $('selector').toggler('destroy');
         * @public
         */
        destroy: function () {
            this.each(function () {
                var $elt = $(this);
                var options = $elt.data(dataNs);


                /**
                 * The plugin have been destroyed.
                 * @event MediaSizer#destroy.toggler
                 */
                $elt.trigger('destroy.' + ns);
            });
        }
    };

    //Register the toggler to behave as a jQuery plugin.
    Pluginifier.register(ns, MediaSizer);

});