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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 */

/**
 * The history router is a router that dispatch based on the browser history
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'router',
    'history',
    'core/eventifier'
], function ($, _, router, history, eventifier) {
    'use strict';

    //start the history polyfill, see https://github.com/devote/HTML5-History-API (a getter should trigger it)
    var historyRouter;
    var location = window.history.location || window.location;


    /**
     * Create an history router
     * @exports core/historyRouter
     *
     * @example
     * var router = historyRouter();
     * router.trigger('dispatch', url);
     *
     * @returns {historyRouter} the router (same instance)
     */
    var historyRouterFactory = function historyRouterFactory(){

        if(historyRouter){
            return historyRouter;
        }

        /**
         * @typedef historyRouter
         * @see core/eventifier
         */
        historyRouter =  eventifier({

            /**
             * Dispatch manually and replace the current state if necessary
             * @param {Object|String} state - the state object or directly the URL
             * @param {String} state.url - if the state is an object, then it must have an URL to dispatch
             * @param {Boolean} [replace = false] - if we replace the current state
             *
             * @fires historyRouter#dispatching before dispatch
             * @fires historyRouter#dispatched  once dispatch succeed
             */
            dispatch : function dispatch(state, replace){
                var self = this;
                if(_.isString(state)){
                    state = { url : state };
                }
                if(!state || !state.url){
                    return;
                }

               self.trigger('dispatching', state.url);

                if(replace === true){
                    history.replaceState(state, '', state.url);
                }

                router.dispatch(state.url, function(){
                    self.trigger('dispatched', state.url);
                });
            },

            /**
             * Push a new state.
             * You can either call pushState or trigger the 'dispatch' event.
             * @param {Object|String} state - the state object or directly the URL
             * @param {String} state.url - if the state is an object, then it must have an URL to dispatch
             */
            pushState : function pushState(state){
                if(_.isString(state)){
                    state = { url : state };
                }
                history.pushState(state, '', state.url);
                this.dispatch(state);
            }
        });

        //back & forward button, and push state
        $(window).on('popstate', function (event) {
            historyRouter.dispatch(history.state);
        });

        //listen for dispatch event in order to push a state
        historyRouter.on('dispatch', function (state) {
            if(state){
                this.pushState(state);
            }
        });

        return historyRouter;
    };

    return historyRouterFactory;
});
