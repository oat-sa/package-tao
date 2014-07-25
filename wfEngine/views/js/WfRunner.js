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
var autoResizeId;

function autoResize(frame, frequence) {
	
	var $frame = $(frame);
        if(autoResizeId && autoResizeId.repeat === true){
            clearInterval(autoResizeId);
        }
	autoResizeId = setInterval(function() {
            if($frame.length > 0){
                $frame.height($frame.contents().height());
            }
	}, frequence);
}

function overlay(){
    var $overlay = $('#overlay');
    if ($overlay.length > 0) {
        $overlay.remove();
    }
    else {
        $('<div id="overlay"></div>').appendTo(document.body);
    }
}

function loading(reverse) {
	var $loading = $('#loading');
        reverse = reverse || false;
	
	if ($loading.length > 0) {
		$loading.remove();
	}
	else {
            $loading = $('<div id="loading"></div>').appendTo(document.body);
            var opts = {
                    lines: 11, // The number of lines to draw
                    length: 21, // The length of each line
                    width: 8, // The line thickness
                    radius: 36, // The radius of the inner circle
                    corners: 1, // Corner roundness (0..1)
                    rotate: 0, // The rotation offset
                    direction: (reverse === true) ? -1 : 1, // 1: clockwise, -1: counterclockwise
                    color: '#888', // #rgb or #rrggbb or array of colors
                    speed: 1.5, // Rounds per second
                    trail: 60, // Afterglow percentage
                    shadow: false, // Whether to render a shadow
                    hwaccel: false, // Whether to use hardware acceleration
                    className: 'spinner', // The CSS class to assign to the spinner
                    zIndex: 2e9, // The z-index (defaults to 2000000000)
                    top: 'auto', // Top position relative to parent in px
                    left: 'auto' // Left position relative to parent in px
            };
            new Spinner(opts).spin($loading[0]);
	}
}

function WfRunner(activityExecutionUri, processUri, activityExecutionNonce) {
	this.activityExecutionUri = activityExecutionUri;
	this.processUri = processUri;
	this.nonce = activityExecutionNonce;
	
	this.services = [];
	
	this.processBrowserModule = window.location.href.replace(/^(.*\/)[^/]*/, "$1");
}

WfRunner.prototype.initService = function(serviceApi, style) {
    var self = this;
    this.services.push(serviceApi);

    serviceApi.onFinish(function() {
        return self.forward();
    });

    $('<iframe class="toolframe" frameborder="0" scrolling="no" src="'+serviceApi.getCallUrl()+'"></iframe>')
        .appendTo('#tools')
        .on('load', function(){
            autoResize(this, 10);
            serviceApi.connect(this);
        });
};

WfRunner.prototype.forward = function() {
    var url = this.processBrowserModule + 'next'
            + '?processUri=' + encodeURIComponent(this.processUri)
            + '&activityUri=' + encodeURIComponent(this.activityExecutionUri)
            + '&nc=' + encodeURIComponent(this.nonce);
    WfRunner.move(url);
};

WfRunner.prototype.backward = function() {
    var url = this.processBrowserModule + 'back'
            + '?processUri=' + encodeURIComponent(this.processUri)
            + '&activityUri=' + encodeURIComponent(this.activityExecutionUri)
            + '&nc=' + encodeURIComponent(this.nonce);

    WfRunner.move(url, true);
};

WfRunner.move = function(url, back){
    clearInterval(autoResizeId);
    $('#tools').empty().height('300px');
    $('#navigation').hide();
    
    overlay();
    loading(back || false);
    
    setTimeout(function(){
        
        //this should be change in favor of an ajax request to get data and set up again the wfRunner 
        window.location.href = url;
    }, 300);
};