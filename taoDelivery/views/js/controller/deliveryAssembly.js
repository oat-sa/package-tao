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
define(['jquery', 'i18n', 'helpers', 'uiBootstrap', 'module'], function ($, __, helpers, uiBootstrap, module) {

        var $tabs = uiBootstrap.tabs;
        var templatesIndex = helpers.getTabIndexByName('manage_delivery_templates');
        
        return {
            start : function(){
                var conf = module.config();
                $tabs.each(function(i) {
                	if (i === templatesIndex) {
                		$(this).css({border: '3px'});
                	}
                })
                if(conf.action !== 'authoring'){
                    //$tabs.tabs('remove', templatesIndex);
                }
                
                if(conf.reload){
                    uiBootstrap.initTrees();
                }
                if(conf.message){
                    helpers.createMessage(conf.message);
                }
                
                $('#saver-status').click(function(e){
                	var status = $('input[name="status"]:checked').val();
                	var uri = $('input[name="assemblyUri"]').val();
                    $.ajax({
            	        url: helpers._url('setStatus', 'Delivery', 'taoDelivery'),
            	        type: "POST",
            	        dataType: "text",
            	        data: {uri : uri, status: status},
            	        success: function (data, textStatus, jqXHR){
                        	//$('#active-inactive').modal('close');
                        	helpers.createMessage("Status saved");
                        	uiBootstrap.initTrees();
            	        },
            	        error: function (jqXHR, textStatus, errorThrown) {
                        	console.log('Error occured: ' + errorThrown);
            	        }
                    });
            	});
            }
        };
});


