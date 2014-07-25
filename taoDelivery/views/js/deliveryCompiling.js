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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

var img_url = root_url + "taoDelivery/views/img/";
var deliveryUri = '';
var classUri = '';


function initCompilation(uri,clazz){
	$("#initCompilation").hide();
	$('#generatingProcess_info').show();
	
	$('#generatingProcess_feedback').empty().hide();
	
	deliveryUri = uri;
	classUri = clazz;
	
	$("#progressbar").empty();
	
	$.ajax({
		type: "POST",
		url: root_url + ctx_extension + '/' + ctx_module + "/compile",
		dataType: "json",
		data: {uri : uri, classUri: clazz},
		success: function(r){
			$('#generatingProcess_info').hide();

			if(r.success){
				finalMessage(__('complete!'),'ok.png');
			}else{
				if (typeof r.error != 'undefined') {
					var msg = r.error;
				} else {
					var msg = __('Please select a valid result server in the delivery editing section then try again.<br/>(No valid wsdl contract found for the defined result server)');
				}
				finalMessage(msg, 'failed.png');
			}
		}
	});
}

function finalMessage(msg, imageFile){
	$('<img/>').attr("src", img_url + imageFile).appendTo($("#progressbar"));
	$("#progressbar").append(msg);
        
	//reinitiate the values and suggest recompilation
	$('#postCompilation').show();
	$("#initCompilation").html( __("Recompile the delivery") );
}

