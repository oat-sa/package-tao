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

define(['module', 'jquery','i18n', 'context', 'helpers'], 
        function(module, $, __, context, helpers){
    
        var migrateRunner = {
            start : function(options){
               $('.opButton').click(
                       
                            function(e) {
                                
                                
                                var operation = $(this).attr("id");
                                var source = []; 
                                var target = [];
                                
                                //todo move
                                var loaderPic = context.root_url+'/tao/views/img/ajax-loader.gif';
                                var migrateDataUrl = context.root_url+'taoResultServer/ResultServerMgt/migrateData';

                                //clean any former feedback built in the dom
                                $("#selSource").empty();
                                $("#selTarget").empty();
                                $("#selOperation").empty();
                                
                               
                                
                                $('#sourceStorage :checked').each(function() {
                                    source.push($(this).val());
                                    
                                    //prepare html for confirmation
                                    //check
                                    
                                    label = $(this).parent().text();
                                    
                                    //label = $("label[for='"+$(this).attr('id')+"']").text();
                                    var li = $('<li />').html(label).appendTo("#selSource");
                                     
                                  });
                                $('#targetStorage :checked').each(function() {
                                    target.push($(this).val());
                                    
                                    label = $(this).parent().text();
                                    //prepare html for confirmation
                                    //label = $("label[for='"+$(this).attr('id')+"']").text();
                                    var li = $('<li />').text(label).appendTo("#selTarget");
                                  });
                                  
                                $('<label>'+operation+'</label>').appendTo("#selOperation");  

                                $("#migrationProgress").attr("style", "visibility: visible")
                                $('#migrationProgress').dialog({
                                        modal: true,
                                        width: 500,
                                        height: 430,
                                        buttons: [
                                                {
                                                        text: __('Cancel'),
                                                        
                                                        click: function() {
                                                                $(this).dialog('close');
                                                        }
                                                },
                                                {
                                                        text: __('Migrate'),
                                                        click: function() {
                                                            $(" #migrationProgress").empty();
                                                            $('<img src="'+loaderPic+'" />').appendTo("#migrationProgress");
                                                         
                                                            
                                                            $.ajax({
                                                              url: migrateDataUrl,
                                                              type: 'POST',
                                                              data: {   
                                                                   source: source,
                                                                   target: target,
                                                                   operation: operation
                                                               },
                                                              success: function(data){
                                                                  $(" #migrationProgress").empty();
                                                                  $('<label>'+data+'<label>').appendTo("#migrationProgress");
                                                                  
                                                                  $('#feedback').attr("style", "visibility: visible")
                                                                  $('#migrationProgress').dialog('close');
                                                              }
                                                            });
                                                            
                                                            
                                                        }
                                                }
                                        ],

                                });
                            }
               );

         }
       };
   
        return migrateRunner;
});