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

define([
    'jquery',
    'jquery.cookie'
],
    function($){

        'use strict';


        var versionWarning = $('.version-warning');

        /**
         * Hide the warning and add a class to <html>
         *
         * @param slide
         */
        function hideWarning(slide) {

            var callback = function() {
                document.documentElement.className += ' no-version-warning';
                versionWarning.trigger('hiding.versionwarning');
            };

            if(!slide) {
                versionWarning.hide();
                callback();
            }
            else {
                versionWarning.slideUp('slow', function() {
                    versionWarning.slideUp('slow', callback);
                });
            }
        }

    return {
        /**
         * Initialize behaviour of version warning
         */
        init : function(){
            if($.cookie('versionWarning')) {
                hideWarning(false);
                return;
            }

            versionWarning.find('.close-trigger').on('click', function() {
                $.cookie('versionWarning', true, { path: '/' });
                hideWarning(true);
            });

        }
    };
});


