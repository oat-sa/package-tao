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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
define(['jquery', 'iframeResizer', 'iframeNotifier'], function($, iframeResizer, iframeNotifier){

    function WfRunner(activityExecutionUri, processUri, activityExecutionNonce) {
            this.activityExecutionUri = activityExecutionUri;
            this.processUri = processUri;
            this.nonce = activityExecutionNonce;
            this.childServiceApi = null;

            this.services = [];

            this.processBrowserModule = window.location.href.replace(/^(.*\/)[^/]*/, "$1");
    }

    WfRunner.prototype.initService = function(serviceApi, $serviceFrame, style) {
        
        var self = this;
        this.childServiceApi = serviceApi;
        this.services.push(serviceApi);

        serviceApi.onFinish(function() {
            return self.forward();
        });

        iframeResizer.eventHeight($serviceFrame, parseInt($('#navigation').height(), 10));

        serviceApi.loadInto($serviceFrame.get(0), function(){
            iframeNotifier.parent('unloading');
        });
    };

    WfRunner.prototype.forward = function() {
        var that = this;
        
        this.childServiceApi.kill(function (signal) {
            var url = that.processBrowserModule + 'next'
            + '?processUri=' + encodeURIComponent(that.processUri)
            + '&activityUri=' + encodeURIComponent(that.activityExecutionUri)
            + '&nc=' + encodeURIComponent(that.nonce);
            WfRunner.move(url);
        });
    };

    WfRunner.prototype.backward = function() {
        var that = this;
        
        this.childServiceApi.kill(function (signal) {
            var url = that.processBrowserModule + 'back'
            + '?processUri=' + encodeURIComponent(that.processUri)
            + '&activityUri=' + encodeURIComponent(that.activityExecutionUri)
            + '&nc=' + encodeURIComponent(that.nonce);

            WfRunner.move(url, true);
        });
    };

    WfRunner.move = function(url, back){
        $('#tools').empty().height('300px');
        $('#navigation').hide();

        iframeNotifier.parent('loading', [back]);

        // this should be change in favor of an ajax request to get data and set
        // up again the wfRunner
        window.location.href = url;
    };

    return WfRunner;
});