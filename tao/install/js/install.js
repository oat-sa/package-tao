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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
//load the AMD config
require(['config'], function(){

    require(['jquery', 'spin', 'api', 'help', 'jqueryui'], function($, Spinner, TaoInstall){

        // API instanciation to be ready for template
        // injection. (apiInstance has no var statement -> global scope).
        var apiInstance = new TaoInstall();
        apiInstance.frameId = 'mainFrame';
        apiInstance.setTemplate('step_requirements');
        apiInstance.addData('extensions', ['taoCe']);

        // feedback popup show/hide
        $('#supportTab').bind('click', openSupportTab);
        $('#supportPopupClose').bind('click', closeSupportTab);


        function openSupportTab(){
            
            $('#supportTab').unbind('click');
            
            // We display the first state of the support frame (loading...)	   
            $('#mainSupportPopup').show();
            var opts = {
                lines: 9, // The number of lines to draw
                length: 3, // The length of each line
                width: 2, // The line thickness
                radius: 4, // The radius of the inner circle
                rotate: 0, // The rotation offset
                color: '#000', // #rgb or #rrggbb
                speed: 1.9, // Rounds per second
                trail: 60, // Afterglow percentage
                shadow: false, // Whether to render a shadow
                hwaccel: false, // Whether to use hardware acceleration
                className: 'spinner', // The CSS class to assign to the spinner
                zIndex: 2e9, // The z-index (defaults to 2000000000)
                top: -2,
                left: 0
            };
            
            var $supportLoading = $('<div id="supportLoading"></div>').css('display', 'block');
            $('#supportPopupContent').append($supportLoading);
            var spinner = new Spinner(opts).spin($supportLoading[0]);
            $supportLoading.append('<span>Loading from the World Wide Web...</span>');
            
            setTimeout(function(){ // Fake delay for user experience.
                
                var $iframe = $('<iframe/>');
                $iframe.attr('name', 'supportFrame')
                       .attr('id', 'supportFrameId')
                       .attr('alt', 'Support frame')
                       .attr('frameborder', 0)
                       .attr('scrolling', 'no');
                
                // bind events.
                if ($.browser.msie)
                {
                    $iframe[0].onreadystatechange = function(){	
                        if(this.readyState == 'complete'){
                            showRemoteSupport(spinner);
                        }
                    };
                }
                else
                {
                    // Other great browsers.		
                    $iframe[0].onload = function(){
                        showRemoteSupport(spinner);	
                    };
                }
                
                $iframe.attr('src', apiInstance.feedbackUrl);
                $('#supportPopupContent').append($iframe);
                
            }, 500);
        }

        function closeSupportTab(){
            $('#mainSupportPopup').hide();
            $('#supportLoading, #supportFrameId').remove();
        }

        function showRemoteSupport(spinner){
                spinner.stop();
                $('#supportLoading').remove();
                $('#supportFrameId').css('display', 'block');
        }
    });
});
