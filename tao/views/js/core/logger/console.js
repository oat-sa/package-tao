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
 * A console based logger.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
], function(_){
    'use strict';

    /**
     * Initialize the logger API with the console provider
     * @returns {logger} the logger
     */
    return {
        log : function log(message){

            var level = message.level;
            var messages = message.messages;
            if(message.context){
                messages.unshift('[' + message.context + ']');
            }
            if(_.isFunction(window.console[level])){
                window.console[level].apply(window.console, messages);
            } else {
                window.console.log.apply(window.console, [level.toUpperCase()].concat(messages));
            }
        }
    };
});
