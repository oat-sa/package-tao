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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

/**
 * NOTE: under certain circumstances iframes will not grow higher than 150px.
 * This has been addressed in CSS already. Should this re-occur though refer to this gist
 * https://gist.github.com/dietertaotesting/512eef98b1db36dd3f59
 */
define(['jquery', 'iframeNotifier' ,'jquery.sizechange'], function ($, iframeNotifier) {
    'use strict';

    /**
     * Helps you to resize an iframe from it's content
     *
     * todo migrate to a jQuery plugin ?
     *
     * @author Bertrand Chevrier <betrand@taotesting.com>
     * @exports iframeResizer
     */
    var Resizer = {

        /**
         * Set the height of an iframe regarding it's content, on load and if the style changes.
         *
         * @param {jQueryElement} $frame - the iframe to resize
         * @param {string} [restrict = 'body'] - restrict the elements that can have a style change
         * @param {Number} [plus] - additional height
         * @returns {jQueryElement} $frame for chaining
         */
        autoHeight : function ($frame, restrict, plus) {
            var self = this;
            restrict = restrict || 'body';
            plus = plus || 0;
            $frame.on('load', function () {
                var $frameContent = $frame.contents();
                var height = $frameContent.height();

                //call resizePop to change only to the last value within a time frame of 1ms
                var sizing = false;
                var resizePop = function resizePop () {
                    if (sizing === false) {
                        sizing = true;
                        setTimeout(function () {
                            self._adaptHeight($frame, height, plus);
                            sizing = false;
                       }, 1);
                    }
                };

                //resize on load
                self._adaptHeight($frame, height);

                try {

                    //then listen for size change
                    var onSizeChange = function onSizeChange () {
                        var newHeight = $frameContent.height();
                        if (newHeight > height) {
                           height = newHeight;
                           resizePop();
                        }
                        if (newHeight > height) {
                           height = newHeight;
                           resizePop();
                        }
                    };

                    $frameContent.find(restrict).sizeChange(onSizeChange);

                    $frameContent.on('resize', onSizeChange);

                } catch (e) {
                    console.warning("Fallback to set interval");
                    //fallback to an interval mgt
                    setInterval(function () {
                        var newHeight = $frameContent.height();
                        if (newHeight > height) {
                            height = newHeight;
                            resizePop();
                        }
                    }, 10);
                }
            });

            return $frame;
        },

        /**
         * Listen for heightchange event to adapt the height
         * @param {jQueryElement} $frame - the frame to listen for height changes
         */
        eventHeight : function ($frame, diff) {
            var self = this;

            $frame.on('load.eventHeight', function () {
                var newdiff = parseInt($frame.contents().height(), 10) - parseInt($frame.height(), 10);
                if(newdiff > diff){
                    diff = newdiff;
                }
                self._adaptHeight($frame, $frame.contents().height() + diff);
            });

            $(document).on('heightchange', function (e, height, plus) {
                plus = plus || 0;
                self._adaptHeight($frame, height + plus + diff);
            });
        },

        /**
         * Notify the parent document of an height change in case we are in an iframe
         * @private
         * @param {Number} height - the value of the new height
         * @fires heightchange
         */
        _notifyParent : function (height, plus) {
            iframeNotifier.parent('heightchange', [height, plus || 0]);
        },

        /**
         * Change the height of the targeted iframe
         * @private
         * @param {jQueryElement} $frame  - the frame to resize
         * @param {number} height  - the value of the new height
         * @fires heightchange
         */
        _adaptHeight : function ($frame, height, plus) {
            $frame.height(height);
            this._notifyParent(height, plus);
        }

    };
    return Resizer;
});
