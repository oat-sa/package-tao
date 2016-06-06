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
define([
    'lodash',
    'jquery',
    'taoDelivery/controller/runtime/service/fullScreen',
    'layout/loading-bar',
    'ui/dialog/alert',
    'layout/logout-event'
], function(_, $, fullScreen, loadingBar, dialogAlert, logoutEvent){
    'use strict';

    var $frameContainer,
        $frame,
        $headerHeight,
        $footerHeight;

    /**
     * Forces a browser repaint
     * Solution from http://stackoverflow.com/questions/3485365/how-can-i-force-webkit-to-redraw-repaint-to-propagate-style-changes?answertab=votes#tab-top
     * @param {jQuery} $target
     */
    var forceRepaint = function($target) {
        var sel = $target[0];
        if (sel) {
            sel.style.display = 'none';
            sel.offsetHeight; // no need to store this anywhere, the reference is enough
            sel.style.display = '';
        }
    };

    function resizeMainFrame() {
        var height = $(window).outerHeight() - $headerHeight - $footerHeight;
        $frameContainer.height(height);
        $frame.height(height);
        forceRepaint($frameContainer);
    }

    return {
        start: function(options){

            if(!!options.deliveryServerConfig.requireFullScreen){
                fullScreen.init();
            }

            $frameContainer = $('#outer-delivery-iframe-container');
            $frame = $frameContainer.find('iframe');
            $headerHeight = $('body > .content-wrap > header').outerHeight() || 0;
            $footerHeight = $('body > footer').outerHeight() || 0;

            $(document).on('serviceforbidden', function() {
                logoutEvent();
            });

            var serviceApi = options.serviceApi;

            serviceApi.onFinish(function() {
                $.ajax({
                    url : options.finishDeliveryExecution,
                    data : {
                        'deliveryExecution' : options.deliveryExecution
                    },
                    type : 'post',
                    dataType : 'json',
                    success : function(data) {
                        window.location = data.destination;
                    }
                });
            }).onExit(function() {
                window.location = options.exitDeliveryExecution;
            });

            $(document)
                .on('loading', function(e){
                    loadingBar.start();
                })
                .on('unloading', function(){
                    setTimeout(function(){
                        loadingBar.stop();
                    }, 300);
                })
                .on('messagealert', function(e, data) {
                    if (data) {
                        dialogAlert(data.message, data.action);
                    }
                })
                .on('shutdown-com', function(){
                    //use when we want to stop all exchange between frames
                    $(document).off('heightchange');
                    $frame.off('load.eventHeight')
                           .off('load.cors');
                });

            serviceApi.loadInto($frame.get(0));

            $(window).on('resize', _.throttle(function() {
                resizeMainFrame();
            }, 250));

            resizeMainFrame();
        }
    };
});
