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
define(['module' ,'jquery', 'i18n', 'context', 'helpers', 'uiBootstrap'], function(module, $, __, context, helpers, uiBootstrap){

    function initCompilation(uri, clazz){
        $("#initCompilation").hide();
        $('#generatingProcess_info').show();
        $('#generatingProcess_feedback').empty().hide();
        $("#progressbar").empty();
        $('.main-container').append('<div id="compilationReport"></div>');
        
        $.ajax({
	        url: helpers._url('compile', 'Compilation', 'taoDelivery'),
	        type: "POST",
	        dataType: "text",
	        data: {uri : uri, classUri: clazz},
	        success: function (data, textStatus, jqXHR){
	            $('#generatingProcess_info').hide();
	            $('#compilationReport').html(data);
	        },
	        error: function (jqXHR, textStatus, errorThrown) {
	        	finalMessage(errorThrown);
	        }
        });
    }

    function finalMessage(msg, imageFile){
        $('<img/>').attr("src", context.root_url + 'taoDelivery/views/img/' + imageFile).appendTo($("#progressbar"));
        $("#progressbar").append(msg);

        //reinitiate the values and suggest recompilation
        $('#postCompilation').show();
        $("#initCompilation").html( __("Recompile the delivery") );
    }
    
    return {
        start : function(){
            
            var conf = module.config();
            
            $('#generatingProcess_info').hide();
            $('.back').click(function(e){
                e.preventDefault();
                helpers._load(helpers.getMainContainerSelector(), context.root_url + 'taoDelivery/Delivery/editDelivery/', {
                    uri: conf.uri,
                    classUri:  conf.classUri
                });
            });
            $('#compiler').click(function(e){
                e.preventDefault();
                initCompilation(conf.uri, conf.classUri);
            });
        }
    };
});