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
define([
    'jquery',
    'helpers',
    'taoCe/controller/home/splash'
], function($, helpers, splash) {
    'use strict';

    var $mainContainer = $('body');

    /**
     * The homeController set up the splash screen.
     *
     * @exports taoCe/controller/home
     */
    var homeController = {


        entrySplash : true,

        /**
         * Setup the splash screen: loads it's content and initialize the component.
         */
        start: function start() {
            var self = this;

            //the splash content is loaded only once.
            if ($('#splash-screen', $mainContainer).length === 0) {
                $.get(helpers._url('splash', 'Home', 'taoCe')).success(function(response) {
                    var $splash = $(response);
                    $splash.css('display', 'none');
                    $mainContainer.append($splash);

                    splash.init(self.entrySplash);
                });
            } else {
                splash.init(self.entrySplash);
            }
        }
    };

    return homeController;
});
