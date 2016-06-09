/**
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 */
/**
 * Provides a keystrokes simulator. This module relies on jquery.simulate, provided by the jQuery Foundation
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define([
    'jquery',
    'lib/simulator/jquery.simulate'
], function($) {
    'use strict';

    $.fn.extend({
        /**
         * Inserts a text at the cursor position inside a textbox.
         * Code from: http://stackoverflow.com/questions/946534/insert-text-into-textarea-with-jquery/946556#946556
         * @param {String} myValue
         * @returns {jQuery}
         */
        insertAtCaret : function(myValue) {
            return this.each(function(i) {
                if (document.selection) {
                    //For browsers like Internet Explorer
                    this.focus();
                    var sel = document.selection.createRange();
                    sel.text = myValue;
                    this.focus();
                } else if (this.selectionStart || this.selectionStart == '0') {
                    //For browsers like Firefox and Webkit based
                    var startPos = this.selectionStart;
                    var endPos = this.selectionEnd;
                    var scrollTop = this.scrollTop;
                    this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos,this.value.length);
                    this.focus();
                    this.selectionStart = startPos + myValue.length;
                    this.selectionEnd = startPos + myValue.length;
                    this.scrollTop = scrollTop;
                } else {
                    this.value += myValue;
                    this.focus();
                }
            });
        }
    });

    /**
     * Simulates a full keystroke
     * @param {String|jQuery|HTMLElement} element
     * @param {Object} options
     */
    var keystroke = function(element, options) {
        var $element =  $(element);
        var optionsType = $.type(options);
        var keyOptions = {
            charCode: 0,
            keyCode: 0,
            key: ""
        };
        var strokeOptions = {
            charCode: 0,
            keyCode: 0,
            key: ""
        };

        if ('number' === optionsType) {
            if (options >= 32) {
                options = String.fromCharCode(options);
                optionsType = 'string';
            } else {
                keyOptions.key = '[' + options + ']';
                keyOptions.charCode = options;
                keyOptions.keyCode = options;

                strokeOptions.key = '[' + options + ']';
                strokeOptions.charCode = options;
                strokeOptions.keyCode = options;
            }
        }

        if ('string' === optionsType) {
            if (options.length > 1) {
                options = options.split('');
                optionsType = 'array';
            } else {
                keyOptions.key = options;
                keyOptions.charCode = 0;
                keyOptions.keyCode = options.toUpperCase().charCodeAt(0);

                strokeOptions.key = options;
                strokeOptions.charCode = options.toLowerCase().charCodeAt(0);
                strokeOptions.keyCode = 0;
            }
        }

        if ('array' === optionsType) {
            options.forEach(function(opt) {
                keystroke(element, opt);
            });
            return;
        }

        if ('object' === optionsType) {
            $.merge(keyOptions, options);
            $.merge(strokeOptions, options);
        }

        if (strokeOptions.charCode >= 32) {
            $element.insertAtCaret(strokeOptions.key);
        }
        $element.simulate('keydown', keyOptions);
        $element.simulate('keypress', strokeOptions);
        $element.simulate('keyup', keyOptions);
    };

    /**
     * Simulates a user input
     * @param {String|jQuery|HTMLElement} element
     * @param {String} string
     */
    var puts = function(element, string) {
        var chars = ('' + string).split();
        chars.forEach(function(char) {
            keystroke(element, char);
        });
    };


    /**
     * Simulates a user input followed by a keypress on Enter
     * @param {String|jQuery|HTMLElement} element
     * @param {String} string
     * @param {Boolean} [terminatedBy] Default to Enter
     */
    var putLine = function(element, string, terminatedBy) {
        var chars = ('' + string).split();
        chars.push(terminatedBy || $.simulate.keyCode.ENTER);
        chars.forEach(function(char) {
            keystroke(element, char);
        });
    };

    return {
        keystroke : keystroke,
        puts      : puts,
        putLine   : putLine,
        keyCode   : $.simulate.keyCode
    };
});
