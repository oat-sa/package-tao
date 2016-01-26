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
define(['jquery', 'jquery.labelify'], function($){

	// bind click events on question mark
	$(".ui-icon-help").bind("click", displayTaoHelp);
	// display help inside inout fields
	$("input:text, textarea").labelify({labelledClass: "helpTaoInputLabel"});

	// feedback popup show/hide
	$("#supportTab").bind("click",openSupportTab);

	$("#supportPopupClose").bind("click",function(){
		$("#mainSupportPopup").hide();
		$("#screenShield").hide();
		$("#mainSupportPopup").find("#supportFrameId").attr("src","supportFrameIndex.html");
	});

	function openSupportTab(){
		$("#mainSupportPopup").show();
		$("#screenShield").show();
		$("#mainSupportPopup").find("#supportFrameId").attr("src","supportFrameIndex.html");
	}

    function displayTaoHelp(event){

        var inputId = $(event.currentTarget).attr('id'),
            msg = 'No help for input <strong>' + inputId + '</strong>.',
            storeMsg;

        if ((storeMsg = install.getHelp(inputId)) != null){
            msg = storeMsg;
        }
        displayPopup({
            msg : msg,
            title : 'Help',
            type : 'help'
        });
    }
    
    function displayTaoError(msg, title){
        displayPopup({
            msg : msg.replace(/\n/g, "<br/>"), 
            title : title, 
            type : 'error'
        });
    }
    
    function displayPopup(options)
    {
        var defaultOptions = {
            title : 'Info',
            type : 'help'
        },
        popupDocContext = parent.document;
        
        options = $.extend(true, defaultOptions, options);

        $(popupDocContext).find("#mainGenericPopup").show();
        $(popupDocContext).find("#screenShield").show();
        $(popupDocContext)
            .find("#genericPopup h4")
            .removeClass('help error')
            .addClass(options.type)
            .html(options.title);

        $(popupDocContext).find("#genericPopupContent").html(options.msg);
        $(popupDocContext).find(".js-genericPopupClose").one('click', function(){
            $(popupDocContext).find("#mainGenericPopup").hide();
            $(popupDocContext).find("#screenShield").hide();
            if (typeof options.onClose === 'function') {
                options.onClose();
            }
        });
    }
    
    window.displayPopup = displayPopup;
    window.displayTaoHelp = displayTaoHelp;
    window.displayTaoError = displayTaoError;
});
