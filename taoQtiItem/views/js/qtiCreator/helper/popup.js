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
define([
    'jquery',
    'lodash',
    'core/dataattrhandler'
], function(
    $,
    _,
    dataAttrHandler
    ){

    'use strict';


    var init = function($trigger, options) {

        var defaults = {
            top: null,     // || pixels relative to the top border of the sidebar
            right: 2,      // pixels relative to the left border of the sidebar
            title: null    // || string title
        };

        options = _.assign(defaults, (options || {}));

        // open the popup
        var open = function($trigger, $popup) {

            // The following sidebar related variables apply only in the case where the popup
            // is attached to a sidebar. In the the case of farbtastic for example
            // the popup is attached to the body.
            var $container = $popup.parents('.sidebar-popup-parent');


            // this makes sure the popup height never exceeds the height of the content part of the page
            var $actionBar = $('.item-editor-action-bar');
            var baseOffsetTop = $actionBar.offset().top - $actionBar.height();
            var maxHeight = $(window).height() - baseOffsetTop;

            var top = _.isNull(options.top) ? $trigger.offset().top - baseOffsetTop - ($popup.height() / 2) : options.top;
            var $titleArea = $popup.find('.sidebar-popup-title');
            var $title = $titleArea.find('h3');
            if($titleArea.length) {
                maxHeight -= $titleArea.height();
            }

            $trigger.trigger('beforeopen.popup', { popup: $popup, trigger: $trigger });
            $popup.show();

            $popup.css({
                top: Math.max(baseOffsetTop, top),
                right: $container.hasClass('item-editor-sidebar-wrapper') ? $container.width() + options.right : options.right
            });

            if(options.title) {
                $title.text(options.title);
            }

            $popup.find('.sidebar-popup-content').css({ maxHeight: maxHeight });

            $trigger.trigger('open.popup', { popup: $popup, trigger: $trigger });
        };

        // close the popup
        var close = function($trigger, $popup) {
            $trigger.trigger('beforeclose.popup', { popup: $popup, trigger: $trigger });
            $popup.hide();
            $trigger.trigger('close.popup', { popup: $popup, trigger: $trigger });
        };


        // find popup, assign basic actions, add it to trigger props
        $trigger.each(function() {
            var _trigger = $(this),
                $popup = options.popup || (function() {
                    return dataAttrHandler.getTarget('popup', _trigger);
                }());

            var $closer = $popup.find('.closer'),
                $dragger = $popup.find('.sidebar-popup-title').not($closer);

            if(!$popup || !$popup.length) {
                throw new Error('No popup found');
            }

            // close popup by clicking on x button
            if($closer.length) {
                $closer.on('click', function() {
                    close(_trigger, $popup);
                });
            }

            // drag popup
            if($dragger.length){
                $popup.draggable({
                    handle : $dragger
                });
            }

            // assign popup to trigger to avoid future DOM operations
            _trigger.prop('popup', $popup);
        });

        // toggle popup
        $trigger.on('click', function(e) {
            var _trigger = $(e.target),
                $popup   = _trigger.prop('popup');

            // in case the trigger is an <a>
            e.preventDefault();

            // toggle popup
            if($popup.is(':visible')) {
                close(_trigger, $popup);
            }
            else {
                open(_trigger, $popup);
            }
        });
    };


    function reorderZindex() {

    }



    return {
        init: init,
        reorderZindex: reorderZindex
    };

});


