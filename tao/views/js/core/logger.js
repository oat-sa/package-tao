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
 *
 * Logger facade
 *
 * Load the logger providers based on the module configuration
 * and exposes the logger api
 *
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'module',
    'core/logger/api',
    'core/logger/console'
], function(_, module, loggerApi, consoleLogger){
    'use strict';

    //the logger providers are configured through the AMD module config
    var config = module.config();
    if(_.isArray(config.loggers) && config.loggers.length){

        //we can load the loggers dynamically
        require(config.loggers, function(){
            var loggerProviders = [].slice.call(arguments);
            _.forEach(loggerProviders, function (provider){
                loggerApi.register(provider);
            });

            //flush messages that arrived before the providers are there
            loggerApi.flush();
        });

    } else {

        //defaults to the console provider
        loggerApi.register(consoleLogger);
    }

    //exposes the API
    return loggerApi;
});
