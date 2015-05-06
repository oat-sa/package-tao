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
define(['jquery', 'iframeResizer', 'spin'], function($, iframeResizer, Spinner){
    
    function loading(reverse) {
        if($('#overlay').length === 0){
            
            $('<div id="overlay"></div>').appendTo(document.body);
            $('<div id="loading"><div></div></div>').appendTo(document.body);
        }

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
        };
        new Spinner(opts).spin($('#loading > div').get(0));
    }
    
    function unloading() {
        setTimeout(function(){
            $('#loading').fadeOut(300, function(){
                $(this).remove();
                $('#overlay').remove();
            });
        }, 300);
    }
    
    function resizeMainFrame() {
        var $frame = $('#iframeDeliveryExec');
        var windowHeight = $(window).height();
        var controlHeight = 0;
        var $control = $('#control');
        
        if ($control.length === 1) {
            controlHeight = $control.outerHeight(true);
        }
        
        var newHeight = windowHeight - controlHeight;
        
        if (navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false == true) {
            newHeight -= 20;
        }
        
        $frame.css('height', newHeight + 'px');
    }
    
    return {
        start: function(options){
            
            var $frame = $('#iframeDeliveryExec');
            $('#tools').css('height', 'auto');
            
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
            });
            
            $(document)
                .on('loading', function(e, reverse){
                    loading(reverse);
                })
                .on('unloading', function(){
                    unloading();
                })
                .on('shutdown-com', function(){
                    //use when we want to stop all exchange between frames
                    $(document).off('heightchange');
                    $frame.off('load.eventHeight')
                           .off('load.cors');
                });
            
            serviceApi.loadInto($frame.get(0));
            
            $(window).bind('resize', function() {
                resizeMainFrame();
            });
            
            resizeMainFrame();
        }
    };
});