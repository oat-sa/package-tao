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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 *
 */

/**
 * Themes configuration, enables you to access the available themes.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'module'
], function(_, module){
    'use strict';

    var config = module.config();

    /**
     * Let's you access to platform themes
     * @exports ui/themes
     */
    return {

        /**
         * Get the themes config for.
         *
         * @example themes().get('items');
         *
         * @param {String} what - themes are classified, what is the theme for ?
         * @returns {Object?} the themes config
         */
        get : function get(what){
            if(_.isPlainObject(config[what])){
                return config[what];
            }
        },

        /**
         * Get the list of available themes.
         *
         * @example themes().getAvailable('items');
         *
         * @param {String} what - themes are classified, what is the theme for ?
         * @returns {Array} the themes
         */
        getAvailable : function getAvailable(what){
            var available = [];
            var themes = this.get(what);
            if(themes && _.isArray(themes.available)){
                available = themes.available;
            }
            return available;
        }
    };
});
