
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
 * Copyright (c) 2015 (original work) Open Assessment Technlogies SA (under the project TAO-PRODUCT);
 *
 */

/**
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash'
], function(_){
    'use strict';

    //TODO comment out and raise to tao-core

    var errorHandlerContext = function(){

        var typedHandlers = {};
        var globalHandler;

        return {
            'listen': function listenError(type, handler){
                if(_.isFunction(type) && !handler){
                    handler = type;
                }
                if(_.isFunction(handler)){
                    if(_.isString(type) && !_.isEmpty(type)){
                        typedHandlers[type] = handler;
                    } else {
                        globalHandler = handler;
                    }
                }
            },
            'throw' : function throwError(err){
                if(_.isString(err)){
                    err = new Error(err);
                }
                if(_.isFunction(typedHandlers[err.name])){
                    typedHandlers[err.name](err);
                }
                if(_.isFunction(globalHandler)){
                    globalHandler(err);
                }
                return false;
            }
        };
    };

    var errorHandler = {
        _contexts : {},

        getContext : function getErrorContext(name){
            if(_.isString(name) && name.length){
                this._contexts[name] = this._contexts[name] || errorHandlerContext();
                return this._contexts[name];
            }
        },

        'listen' : function listenInContext(name, type, handler){
            var context = this.getContext(name);
            if(context){
                context.listen.apply(context, [].slice.call(arguments, 1));
            }
        },

        'throw' : function throwInContext(name, err){
            var context = this.getContext(name);
            if(context){
                return context.throw(err);
            }
        },

        reset : function resetContext(name){
            if(this._contexts[name]){
                this._contexts = _.omit(this._contexts, name);
            }
        }

    };
    return errorHandler;
});

