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
 *
 * @author dieter <dieter@taotesting.com>
 * @requires jquery
 * @requires lodash
 * @requires core/pluginifier
 * @requires util/strPad
 * @requires util/capitalize
 */
define([
    'jquery',
    'lodash',
    'core/pluginifier',
    'util/strPad',
    'util/capitalize'
], function ($, _, Pluginifier, strPad, capitalize) {
    'use strict';

    var ns = 'liststyler';

    var currStyle = '';

    var defaults = {
        selected: null
    };

    /**
     * list styles - at the time of writing this is the list of cross browser compatible
     * styles.
     * @see https://developer.mozilla.org/en-US/docs/Web/CSS/list-style-type
     */
    var listStyles = {
        none:'',
        disc:   '\u25cf',
        circle: '\u25cb',
        square: '\u25fd',
        decimal: '1',
        'decimal-leading-zero': '01',
        'lower-alpha': 'a',
        'upper-alpha': 'A',
        'lower-roman': 'i',
        'upper-roman': 'I',
        'lower-greek': '\u03b1',
        'armenian': '\u0531',
        'georgian': '\u10d0'
    };


    /**
     * Populate selectBox with options
     *
     * @param selectBox
     * @param selectedStyle
     */
    function populate(selectBox, selectedStyle) {
        _.forOwn(listStyles, function(symbol, style) {
            selectBox.options.add(new Option(capitalize(style.replace(/-/g, ' ')), style, false, style === selectedStyle));
        });
    }

    /**
     * Prepare select2 formatting
     *
     * @param state
     * @returns {*}
     */
    function formatState (state) {
        var symbol = listStyles[state.id];
        return $('<span/>',{ text: state.text, 'data-symbol': symbol });
    }


    /**
     * Hint: to get a proper two-column design of the select box you should a fixed font
     *
     * @type {{init: init}}
     */
    var ListStyler = {


        /**
         * Initialize the plugin.
         *
         * Called the jQuery way once registered by the Pluginifier.

         * @example $('selector').liststyler();
         * @public
         *
         * @constructor
         * @param options
         * @returns {*}
         */
        init: function (options) {

            return this.each(function () {
                var $elt = $(this);

                //get options using default
                options = $.extend(true, {}, defaults, options);

                populate(this, options.selected);

                currStyle = options.selected;

                $elt.on('change', function() {
                    $elt.trigger('stylechange.' + ns, { newStyle: this.value, oldStyle: currStyle });
                    currStyle = this.value;
                });

                $elt.select2({
                    formatResult: formatState,
                    width: 'element',
                    minimumResultsForSearch: Infinity
                });

                /**
                 * The plugin has been created
                 * @event ListStyler#create.toggler
                 */
                $elt.trigger('create.' + ns);
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

                /**
                 * The plugin have been destroyed.
                 * @event ListStyler#destroy.toggler
                 */
                $elt.trigger('destroy.' + ns);
            });
        }
    };

    //Register the toggler to behave as a jQuery plugin.
    Pluginifier.register(ns, ListStyler);
});
