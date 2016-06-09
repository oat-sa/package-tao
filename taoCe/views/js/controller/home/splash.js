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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */
define(['jquery', 'taoCe/controller/home/custom-scrollbar'], function ($) {
    'use strict';


    /**
     * The SplashScreen creates a modal popup that contains a  dynamic diagram of the TAO's workflow.
     * It relies on content that should be there on a .splash-screen-wrapper element. (This content is loaded from the
     * server by {@link module:taoCe/controller/home}
     *
     * @exports taoCe/controller/home/splash
     */
    var SplashScreen = {

        /**
         * Initialize the splash screen
         * @param {Boolean} [isHomePage = false] - less options if not used as an entry splash
         */
        init: function (isHomePage) {

            //console.log(this)
            this.$splashScreen = $('#splash-screen');
            var $splashWrapper = $('.splash-screen-wrapper');
            var $splashDesc = $('.desc', this.$splashScreen);
            var $splashDiagram = $('.diagram', this.$splashScreen);

            //Url to redirect after closing
            this.redirectUrl = '';

            //overwrites main styles
            $splashWrapper.css('display', 'block');

            if (!isHomePage) {
                $('.modal-footer', this.$splashScreen).hide();
            }
            else {
                $('.modal-footer', this.$splashScreen).show();
            }

            /**
             * Place lock icon for disabled modules
             */
            $('[data-module-name]', $splashDiagram).each(function () {
                var $this = $(this);

                if ($this.hasClass('disabled')) {


                    $this.find('span').remove();
                    $this.prepend('<span class="icon-lock"></span>');
                }
            });

            /**
             * Initialize custom scrollbar for the description
             */
            $splashDesc.customScrollbar({
                updateOnWindowResize: true,
                skin: 'gray-skin',
                hScroll: false
            });

            /**
             * Open modal window immediately
             */
            this.$splashScreen.modal({disableClosing: isHomePage});

            this.initNav();
            this.initModulesNav();
            this.initCloseButton();
            //this.adaptHeight();
        },

        /**
         * Initialize a listener for the navigation tab buttons
         */
        initNav: function () {
            $('.modal-nav a', this.$splashScreen).on('click', function () {
                var selectedEl = $(this),
                    selectedPanelId = selectedEl.data('panel');

                $('.modal-nav li').removeClass('active');
                $("a[data-panel='" + selectedPanelId + "']").parent().addClass('active');

                $('.panels').hide();
                $("div[data-panel-id='" + selectedPanelId + "']").show();
            });
        },

        /**
         * Initialize a listener for the modules buttons
         */
        initModulesNav: function () {
            var splashObj = this;

            $('[data-module-name]', this.$splashScreen).not('.disabled').on('click', function () {
                var selectedEl = $(this),
                    selectedModuleName = selectedEl.data('module-name');
                splashObj.redirectUrl = selectedEl.data('url');

                $('#splash-close-btn').removeAttr('disabled').find('.module-name').text(selectedEl.text());

                if (!selectedEl.hasClass('new-module')) {
                    var selectedClass = selectedEl.hasClass('groups') ?
                        $('.test-takers').find('span').first().attr('class') :
                        selectedEl.find('span').first().attr('class');
                    $('.module-desc>span').attr({'class': selectedClass});
                }
                else {
                    $('.module-desc>span').attr({'class': ''});
                }

                $('[data-module-name]').removeClass('active');
                $('.module-desc').hide();

                selectedEl.addClass('active');
                $("div[data-module='" + selectedModuleName + "']").show();

                $('.desc').customScrollbar('resize', true);
            });
        },

        /**
         * Initialize a listener for the close button
         */
        initCloseButton: function () {
            var splashObj = this;
            var $closeButton = $('#splash-close-btn');

            //trigger the close by keypress enter
            $(document).on('keypress', function (e) {
                if (e.which === 13) {
                    $closeButton.trigger('click');
                }
            });

            //clean unbind
            this.$splashScreen.on('closed.modal', function () {
                $(document).off('keypress');
            });

            $closeButton.on('click', function (e) {
                e.preventDefault();

                //if the checkbox is checked, then add and set the additional GET parameter 'nosplash'
                if ($('#nosplash').prop('checked')) {
                    splashObj.redirectUrl += '&nosplash=true';
                }

                splashObj.closeSplash(splashObj.redirectUrl);
            });
        },

        /**
         * Close the splash screen and redirect to selected module
         * @param {string} url
         */
        closeSplash: function (url) {
            window.location = url;
        },

        /**
         * limit height of splash to make sure all buttons can be accessed on smaller screens
         */
        adaptHeight: function() {
            //console.log(this)
            var splashObj = this,
                $splashContentWrap = splashObj.find('.splash-content-wrap'),
                // 40 to have some nice margin below
                maxHeight = $(window).height() - splashObj.find('.modal-title').offset().top - 40;
            $splashContentWrap.css({ maxHeight: maxHeight });
        }

    };

    return SplashScreen;
});
