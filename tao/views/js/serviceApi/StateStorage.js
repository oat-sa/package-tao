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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA ;
 */
define(['jquery'], function($){
    'use strict';
    
    function StateStorage(state, submitUrl) {
        this.state = state;
        this.submitUrl = submitUrl;
    }

    StateStorage.prototype.get = function(callback){
        if (typeof callback === 'function') {
                callback(this.state);
        }
        return this.state;
    };

    StateStorage.prototype.set = function(state, callback){

        if (state === this.state) {
            if (typeof callback === "function") {
                    callback();
            }
        } else {
            this.state = state;
            $.ajax({
                url : this.submitUrl,
                data 		: {
                    'state' : state
                },
                type        : 'post',
                dataType	: 'json',
                success     : typeof callback === "function" ? callback : null
            });
        }
    };

    return StateStorage;
});