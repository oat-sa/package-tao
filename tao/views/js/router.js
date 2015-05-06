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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
define(['jquery', 'lodash', 'context', 'urlParser', 'async'], function ($, _, context, UrlParser, async) {

    /**
     * The router helps you to execute a controller when an URL maps a defined route.
     * 
     * The routes are defined by extension, into the module {extension}/controller/routes
     * @see http://forge.taotesting.com/projects/tao/wiki/Front_js
     * 
     * @author Bertrand Chevrier <bertrand@taotesting.com>
     * @exports router
     */
    var router = {

        /**
         * Routing dispatching: execute the controller for the given URL.
         * If more than one URL is provided, we try to dispatch until a valid routing if found 
         * (used mainly for forward/redirects).
         * 
         * @param {Array|String} url - the urls to try to dispatch
         * @param {Function} cb - a callback executed once dispatched
         */
         dispatch : function(urls, cb){
             if(!_.isArray(urls)){
                 urls = [urls];
             }
             
             var routed = false;
             var counter = 0, size = urls.length;
             async.whilst(
                function whileTest () { return routed === false && counter < size; }, 
                function tryToDispatch (done) {
                    _dispatch(urls[counter], function(){
                        if(_.isFunction(cb)){
                            cb();
                        }
                        routed = true;
                    }, done);
                    counter++;
                }, 
                function finished (err){
                    if(err){
                        return console.error(err);
                    }
                }
             );
         }
    };
    
    /**
    * Parse an URL and extract MVC route
    * @private
    * @param {String} url - the URL to parse
    * @returns {Object} the route structure
    */
    var parseMvcUrl = function parseMvcUrl(url){
        var route;
        var parser = new UrlParser(url);
        var paths = parser.getPaths();
        if(paths.length >= 3){
            route = {
                action      : paths[paths.length - 1],
                module      : paths[paths.length - 2],
                extension   : paths[paths.length - 3],
                params      : parser.getParams()
            };
        }
        return route;
    };
     
   /**
    * Routing dispatching: execute the controller for the given URL
    * @private
    * @param {String} url - the 
    * @param {Function} cb - a callback executed once dispatched
    */
    var _dispatch = function _dispatch(url, cb, done){
            
        //parse the URL
        var route = parseMvcUrl(url);
        if(route){
            
            //loads the routing for the current extensino
            require([route.extension + '/controller/routes'], function(routes){
                
                if(routes && routes[route.module]){

                    //get the dependencies for the current context
                    var moduleRoutes = routes[route.module];
                    var dependencies = [];
                    if(moduleRoutes.deps){
                       dependencies = dependencies.concat(moduleRoutes.deps);
                    }
                    if(moduleRoutes.actions && moduleRoutes.actions[route.action]){
                        dependencies = dependencies.concat(moduleRoutes.actions[route.action]);
                    }
                    dependencies = _.map(dependencies, function(dep){
                        return /^controller/.test(dep) ?  route.extension + '/' + dep : dep;
                    });
                    
                    if(moduleRoutes.css){
                        var styles = _.isArray(moduleRoutes.css) ? moduleRoutes.css : [moduleRoutes.css];
                        dependencies = dependencies.concat(_.map(styles, function(style){
                            return 'css!' + route.extension + '_css/' +  style;
                        }));
                    }

                    //URL parameters are given by default to the required module (through module.confid()) 
                    if(!_.isEmpty(route.params)){
                        var moduleConfig =  {};
                        _.forEach(dependencies, function(dependency){
                            moduleConfig[dependency] = _.merge(_.clone(requirejs.s.contexts._.config.config[dependency] || {}), route.params);
                        });
                        requirejs.config({ config : moduleConfig });
                    }

                    //loads module and action's dependencies and start the controllers.
                    if(dependencies.length > 0){
                        require(dependencies, function(){
                            _.forEach(arguments, function(dependency){
                                if(dependency && _.isFunction(dependency.start)){
                                    dependency.start();
                                }
                            });
                            if(_.isFunction(cb)){
                                cb();
                            }
                        });
                    }
                }
                if(_.isFunction(done)){
                    done();
                }
            });
        } 
    };
    
    return router;
});