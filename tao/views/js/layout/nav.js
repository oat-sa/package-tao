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
 * This component manage the navigation bar of TAO.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @author Dieter Raber <dieter@taotesting.com>
 */
define(['jquery', 'lodash'], function($, _) {

    'use strict';

    var $body = $('body'),
        $navContainer = $('header.dark-bar'),
        $nav = $navContainer.find($('nav')),
        $mainMenu = $nav.find('.main-menu'),
        $settingsMenu = $nav.find('.settings-menu'),
        navIsOversized = false,
        expandedMinWidth = (function() {
            var _width = $navContainer.find('img').parent().outerWidth();
            $mainMenu.add($settingsMenu).each(function() {
                var oldDisplay = window.getComputedStyle(this,null).getPropertyValue('display');
                this.style.display = 'block';
                _width += $(this).outerWidth();
                this.style.display = oldDisplay;
            });
            // 20 makes sure there is always a bit of distance between the menus
            return _width + 20;
        }());


    /**
     * If logo and main menu leave not enough space for the settings menu
     * the mobile menu will be shown instead.
     */
    var checkHeight = function checkHeight() {
        if(!$mainMenu.length || !$settingsMenu.length) {
            return;
        }
        // - nav is too wide
        if($mainMenu.offset().top !== $settingsMenu.offset().top) {
            $body.addClass('oversized-nav');
            navIsOversized = true;
        }
        // - body.oversized-nav has been set in a previous call
        //      find out if there is enough space now
        else if(navIsOversized && expandedMinWidth <= $navContainer.width()) {
            $body.removeClass('oversized-nav');
            navIsOversized = false;
        }
        // in all other cases leave things as they are
    };


    /**
     * @exports layout/nav
     */
    return {

        /**
         * Initialize the navigation bar
         *
         * @author Bertrand Chevrier <bertrand@taotesting.com>
         */
        init : function(){
            //here the bindings are controllers or even the name of any AMD file to load
            $('[data-action]', $nav).off('click').on('click', function(e){
                e.preventDefault();
                var binding = $(this).data('action');
                if(binding){
                    require([binding], function(controller){
                        if(controller &&  typeof controller.start === 'function'){
                            controller.start();
                        }
                    });
                }
            });

            // check the height of the header on load and on resize
            checkHeight();
            $(window).off('resize.navheight').on('resize.navheight', _.debounce(function () {
                checkHeight();
            }, 100));
        }
    };
});
