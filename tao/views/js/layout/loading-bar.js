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
 * Loading bar a.k.a. Knight Rider
 *
 * @author dieter <dieter@taotesting.com>
 */
define(['jquery'],
    function ($) {

        'use strict';

        /**
         * the TAO header can have three different forms
         * 1. version warning on alpha/beta + main navi
         * 2. main navi only on regular version
         * 3. nothing in the case of LTI
         *
         * @param headerElements
         */
        function getHeaderHeight(headerElements){
            var headerHeight = 0, $element;
            for($element in headerElements) {
                if(headerElements[$element].length && headerElements[$element].is(':visible')) {
                    headerHeight += headerElements[$element].outerHeight();
                }
            }
            return headerHeight;
        }

        var $loadingBar = $('.loading-bar'),
            originalHeight = $loadingBar.height(),
            $win = $(window),
            $doc = $(document),
            $contentWrap    = $('.content-wrap'),
            headerElements  = {
                $versionWarning: $contentWrap.find('.version-warning'),
                $header: $contentWrap.find('header:first()')
            },
            headerHeight   = getHeaderHeight(headerElements);
        
        $win.on('scroll.loadingbar', function () {
            if(!$loadingBar.hasClass('loading')) {
                return;
            }
            // status of height would change for instance when version warning is hidden
            headerHeight = getHeaderHeight(headerElements);

            if (headerHeight <= $win.scrollTop()) {
                $loadingBar.addClass('fixed');
            }
            else {
                $loadingBar.removeClass('fixed');
            }
            $loadingBar.height($doc.height());
        });

        return {
            start: function () {
                if($loadingBar.hasClass('loading')) {
                    $loadingBar.stop();
                }
                $loadingBar.addClass('loading');
                $win.trigger('scroll.loadingbar');
            },
            stop: function () {
                $loadingBar.removeClass('loading fixed').height(originalHeight);
            }
        };
    });
