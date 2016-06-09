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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define(function () {
    'use strict';

    /**
     * Stores the security token
     * @returns {tokenHandler}
     */
    function tokenHandlerFactory() {
        var token = null;

        return {
            /**
             * Gets the current security token.
             * Once the token is got, it is erased from the memory and a new token must be provided.
             * @returns {String}
             */
            getToken: function getToken() {
                var currentToken = token;
                token = null;
                return currentToken;
            },

            /**
             * Sets the current security token
             * @param newToken
             * @returns {jquery}
             */
            setToken: function setToken(newToken) {
                token = newToken;
                return this;
            }
        };
    }

    return tokenHandlerFactory;
});
