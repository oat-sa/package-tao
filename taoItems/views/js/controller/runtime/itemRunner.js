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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
define(['jquery', 'lodash', 'iframeNotifier', 'urlParser'],
    function($, _, iframeNotifier, UrlParser){
        'use strict';

        var itemRunner = {
            start : function(options){

                var $frame = $('<iframe id="item-container" class="toolframe" frameborder="0" style="width:100%;" scrolling="no"></iframe>');
                $frame.appendTo('body');
                var itemId = options.itemId;
                var itemPath = options.itemPath;
                var resultServer = _.defaults(options.resultServer, {
                    module : 'taoResultServer/ResultServerApi',
                    params : {}
                });
                var itemService = _.defaults(options.itemService, {
                    module : 'taoItems/runtime/ItemServiceImpl',
                    params : {}
                });
                var clientConfigUrl = options.clientConfigUrl;
                var timeout = options.timeout || 30;

                //load dynamically the right ItemService and ResultServerApi
                require([itemService.module, resultServer.module], function(ItemService, ResultServerApi){

                    var resultServerApi = new ResultServerApi(resultServer.endpoint, resultServer.params);

                    var setIframeHeight = function(newHeight) {

                        var neverResized = $frame.height() === 150;

                        var resize = function resize(newHeight, notifyParent) {
                            $frame.css({height: newHeight});
                            if(notifyParent) {
                                iframeNotifier.parent('heightchange', newHeight);
                            }
                        };

                        resize(newHeight || $frame.contents().outerHeight(true), !newHeight);

                        if(neverResized) {
                            _.delay(function() {
                                resize($frame.contents().outerHeight(true), true);
                            }, 200);
                        }
                    };

                    window.onServiceApiReady = function(serviceApi){

                        var itemApi = new ItemService(_.merge({
                            serviceApi : serviceApi,
                            itemId : itemId,
                            resultApi : resultServerApi
                        }, {
                            params : itemService.params
                        }));

                        var itemUrl = new UrlParser(itemPath);
                        var isCORSAllowed = itemUrl.checkCORS();
                        itemUrl.addParam('clientConfigUrl', clientConfigUrl);
                        itemUrl.addParam('timeout', timeout);

                        $(document).on('itemloaded', function() {
                            setIframeHeight();
                            iframeNotifier.parent('serviceloaded');

                            $frame.contents().find('img').on('load', _.throttle(function() {
                                setIframeHeight();
                            }, 50));
                        }).on('responsechange', function(e, responseId, response){
                            iframeNotifier.parent('responsechange', [responseId, response]);
                        }).on('stateready', function(e, elmentId, state){
                            iframeNotifier.parent('stateready', [elmentId, state]);
                        }).on('itemready', function() {
                            // item is ready, we can connect.
                            itemApi.connect($frame[0]);
                        }).on('itemcontentchange', function() {
                            setIframeHeight();
                        });

                        $frame.on('load', function(){
                            itemApi.connect($frame[0]);

                            if (isCORSAllowed === true) {
                                this.contentWindow.__knownParent__ = true;
                            }

                            $('body').on('setheight', function(event, height) {
                                setIframeHeight(height);
                            });
                        });
                        $frame.attr('src', itemUrl.getUrl());
                    };

                    //tell the parent he can trigger onServiceApiReady
                    _.defer(function(){
                        iframeNotifier.parent('serviceready');
                    });
                });
            }
        };

        return itemRunner;
    });
