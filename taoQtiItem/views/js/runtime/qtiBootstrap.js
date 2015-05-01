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
 */
define(['jquery', 'lodash', 'taoQtiItem/qtiRunner/core/QtiRunner', 'taoQtiItem/qtiCommonRenderer/renderers/Renderer', 'iframeNotifier', 'core/history'],
    function($, _, QtiRunner, Renderer, iframeNotifier, history){
    'use strict';

    //fix backspace going back into the history
    history.fixBrokenBrowsers();

    /**
     * The bootstrap is used to set up a QTI item at runtime. It connects to the itemApi.
     * 
     * @author Bertrand Chevrier <bertrand@taotesting.com>
     * @export taoQtiItem/runtime/qtiBootstrap
     * 
     * @param {Object} runnerContext - the item context
     */
    return function bootstrap (runnerContext){
       
        //reconnect to global itemApi function
        window.onItemApiReady = function onItemApiReady(itemApi) {
            var qtiRunner = new QtiRunner(),
                coreItemData = runnerContext.itemData,
                variableElementsData = _.merge(runnerContext.variableElements, itemApi.params.contentVariables || {});
            
            // Makes the runner interface available from outside the frame
            // for preview.
            window.qtiRunner = qtiRunner;
            
            qtiRunner.setItemApi(itemApi);
            qtiRunner.setRenderer(new Renderer({'baseUrl': ''}));
            
            qtiRunner.loadItemData(coreItemData, function() {

                qtiRunner.loadElements(variableElementsData, function() {
                    
                    qtiRunner.renderItem(undefined, function() {
                        
                       //exec user scripts
                        if (_.isArray(runnerContext.userScripts)) {
                            require(runnerContext.userScripts, function() {
                                _.forEach(arguments, function(dependency) {
                                    if (_.isFunction(dependency.exec)) {
                                        dependency.exec.call(null, runnerContext.userVars);
                                    }
                                });
                            });
                        }
                        
                        iframeNotifier.parent('itemloaded');
                        
                        //IE9/10 loose the iframe focus, so we force getting it back.
                        _.defer(function(){
                            window.focus();
                        });
                    });
                });
            });
        };

        //if the item is longer to load,
        _.defer(function(){
           //tell the parent to connect the item api
           iframeNotifier.parent('itemready');
       });
   
    };
});

