/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

/**
 * Work with 2D transformations on any container
 *
 * @author dieter <dieter@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'lib/unmatrix/unmatrix'
], function ($, _, _unmatrix) {
    'use strict';

    var ns = 'transformer';

    var vendorPrefixes = ['webkit', 'ms'];

    /**
     * Figure out the vendor prefix, if any
     */
    var prefix = (function () {
        var i = vendorPrefixes.length,
            style = window.getComputedStyle(document.body, null);

        if (style.getPropertyValue('transform')) {
            return '';
        }
        while (i--) {
            if (style[vendorPrefixes[i] + 'Transform'] !== undefined) {
                return '-' + vendorPrefixes[i] + '-';
            }
        }
    }());


    /**
     * Get the transformation of an element
     *
     * @param elem
     * @returns {{matrix: string, obj: obj }}
     */
    var _getTransformation = function (elem) {
        var _style = window.getComputedStyle(elem, null),
            matrix = _style.getPropertyValue('transform') ||
                _style.getPropertyValue('-webkit-transform') ||
                _style.getPropertyValue('-ms-transform'),
            obj = _unmatrix(matrix);

        return { matrix: matrix, obj: obj };
    };


    /**
     * Get the transformation origin of an element
     *
     * @param elem
     * @returns {string}
     * @private
     */
    var _getTransformOrigin = function(elem) {
        var _style = window.getComputedStyle(elem, null);
        return _style.getPropertyValue('transform-origin') ||
            _style.getPropertyValue('-webkit-transform-origin') ||
            _style.getPropertyValue('-ms-transform-origin');
    };


    /**
     * Normalize property keys to the same format unmatrix uses
     *
     * @param transforms
     * @returns {*}
     * @private
     */
    function _normalizeTransforms(transforms) {
        var xy = ['translate', 'scale'],
            i = xy.length;

        while (i--) {
            if (transforms[xy[i]]) {
                if (_.isArray(transforms[xy[i]]) && transforms[xy[i]].length === 2) {
                    transforms[xy[i] + 'X'] = transforms[xy[i]][0];
                    transforms[xy[i] + 'Y'] = transforms[xy[i]][1];
                }
                else {
                    transforms[xy[i] + 'X'] = transforms[xy[i]];
                    transforms[xy[i] + 'Y'] = transforms[xy[i]];
                }
                delete transforms[xy[i]];
            }
        }

        return transforms;
    }


    /**
     * Transform the container with the given configuration
     *
     * @param $elem
     * @param {Object} transforms
     * @param {Number|Array} [transforms.translate] 20|[20,30], assumes px
     * @param {Number} [transforms.translateX] dto.
     * @param {Number} [transforms.translateY] dto.
     * @param {Number} [transforms.rotate] 20, assumes deg
     * @param {Number} [transforms.skew] 20 dto.
     * @param {Number|Array} [transforms.scale] 2|[2,3], assumes 'times original size'
     * @param {Number} [transforms.scaleX] dto.
     * @param {Number} [transforms.scaleY] dto.
     */
    function _transform($elem, transforms) {
        var cssObj = {},
            defaults = _unmatrix('none'),
            classNames = [],
            oriTrans;

        transforms = _normalizeTransforms(transforms);

        // memorize old transformation
        if (!$elem.data('oriTrans')) {
            oriTrans =  _getTransformation($elem[0]);
            oriTrans.origin = _getTransformOrigin($elem[0]);
            $elem.data('oriTrans', oriTrans);
        }

        cssObj[prefix + 'transform'] = '';

        // generate the style
        _.forIn(transforms, function (value, key) {

            // ignore values that aren't numeric
            if (_.isNaN(value)) {
                return true;
            }
            value = parseFloat(value);

            // apply original transformation if applicable
            if ($elem.data('oriTrans').obj[key] !== defaults[key]) {
                if (key.indexOf('scale') > -1) {
                    value *= $elem.data('oriTrans').obj[key];
                }
                else {
                    value += $elem.data('oriTrans').obj[key];
                }
            }

            if (undefined !== defaults[key] && value !== defaults[key]) {
                if (key.indexOf('translate') > -1) {
                    value += 'px';
                }
                else if (key === 'rotate' || key.indexOf('skew') > -1) {
                    value += 'deg';
                }
                cssObj[prefix + 'transform'] += key + '(' + value + ') ';
                classNames.push('transform-' + key.replace(/(X|Y)$/i, ''));
            }
        });

        cssObj[prefix + 'transform'] = $.trim(cssObj[prefix + 'transform']);

        $elem.css(cssObj);
        $elem.removeClass('transform-translate transform-rotate transform-skew transform-scale');
        $elem.addClass(_.unique(classNames).join(' '));

        $elem.trigger('transform.' + ns, transforms);
    }


    /**
     * @exports
     */
    return {

        /**
         * Translate
         *
         * @param $elem
         * @param {Number} valueX
         * @param {Number} [valueY], defaults to valueX
         */
        translate: function ($elem, valueX, valueY) {
            valueY = valueY || valueX;
            _transform($elem, { translateX: valueX, translateY: valueY });
        },

        /**
         * TranslateX
         *
         * @param $elem
         * @param value
         */
        translateX: function ($elem, value) {
            _transform($elem, { translateX: value });
        },

        /**
         * TranslateY
         *
         * @param $elem
         * @param value
         */
        translateY: function ($elem, value) {
            _transform($elem, { translateY: value });
        },

        /**
         * Rotate
         *
         * @param $elem
         * @param value
         */
        rotate: function ($elem, value) {
            _transform($elem, { rotate: value });
        },

        /**
         * Skew
         *
         * @param $elem
         * @param value
         */
        skew: function ($elem, value) {
            _transform($elem, { skew: value });
        },

        /**
         * Scale
         *
         * @param $elem
         * @param {Number} valueX
         * @param {Number} [valueY], defaults to valueX
         */
        scale: function ($elem, valueX, valueY) {
            valueY = valueY || valueX;
            _transform($elem, { scaleX: valueX, scaleY: valueY });
        },

        /**
         * ScaleX
         *
         * @param $elem
         * @param value
         */
        scaleX: function ($elem, value) {
            _transform($elem, { scaleX: value });
        },

        /**
         * ScaleY
         *
         * @param $elem
         * @param value
         */
        scaleY: function ($elem, value) {
            _transform($elem, { scaleY: value });
        },

        /**
         * Remove all transformations added by this code
         *
         * @param $elem
         * @param value
         */
        reset: function ($elem) {
            var cssObj = {};

            // when called on a container that has never been transformed
            if (!$elem.data('oriTrans')) {
                return;
            }

            cssObj[prefix + 'transform'] = $elem.data('oriTrans').matrix;
            cssObj[prefix + 'transform-origin'] = $elem.data('oriTrans').origin;
            $elem.css(cssObj);
            $elem.removeClass('transform-translate transform-rotate transform-skew transform-scale');
            $elem.trigger('reset.' + ns, $elem.data('oriTrans'));
        },

        /**
         * Get current transformation. Though _getTransformation() expects a DOM element
         * jQuery elements are also accepted to keep the same format the other functions have.
         *
         * @param {DomElement|jQueryElement} elem
         * @returns {{matrix: string, obj: obj}}
         */
        getTransformation: function (elem) {
            if (elem instanceof $) {
                elem = elem[0];
            }
            return _getTransformation(elem);
        },

        /**
         * Set the transformation origin to another value
         *
         * @param $elem
         * @param value
         * @private
         */
        setTransformOrigin: function($elem, value) {
            var cssObj = {};
            cssObj[prefix + 'transform-origin'] = value;
            $elem.css(cssObj);
        }
    };
});
