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

define(['module', 'jquery','i18n', 'context', 'helpers', 'ui/feedback'], 
        function(module, $, __, context, helpers, feedback){
    
        var migrateRunner = {
            start : function(options){
               $('.opButton').click(
                       
                            function(e) {

                                var operation = $(this).attr("id"),
                                    sources = [],
                                    targets = [],
                                    $source = $("#selSource"),
                                    $target = $("#selTarget"),
                                    $migrationDialog = $("#migrationProgress"),
                                    $migrationInfo = $migrationDialog.children(".migrationInfo"),
                                    source;

                                //todo move
                                var migrateDataUrl = context.root_url+'taoResultServer/ResultServerMgt/migrateData';

                                //clean any former feedback built in the dom
                                $source.empty();
                                $target.empty();

                                $('#sourceStorage :checked').each(function() {
                                    source = $(this).val();
                                    sources.push(source);

                                    $source
                                        .append($("<li />", {id: source.replace(/[^a-z0-9-_]+/ig, "_")})
                                        .html($(this).parent().text()));
                                });
                                if (!sources.length) {
                                  feedback().error(__('Please select a migration source'));
                                  return;
                                }

                                $("#targetStorage :checked").each(function() {
                                    targets.push($(this).val());

                                    $target.append($("<li />").text($(this).parent().text()));
                                });
                                if (!targets.length) {
                                  feedback().error(__("Please select a destination target"));
                                  return;
                                }

                                $("#selOperation").html("<label>"+operation+"</label>");

                                $migrationInfo.children().show();

                                $migrationDialog
                                    .attr("style", "visibility: visible")
                                    .dialog({
                                        modal: true,
                                        width: 500,
                                        height: 430,
                                        buttons: [
                                                {
                                                        id: "MigrationClose",
                                                        text: __('Cancel'),
                                                        
                                                        click: function() {
                                                                $(this).dialog('close');
                                                        }
                                                },
                                                {
                                                        id: "MigrationStart",
                                                        text: __('Migrate'),
                                                        click: function() {
                                                            $migrationInfo.hide();
                                                            $migrationDialog.children(".migrationResult").hide();
                                                            $migrationDialog.children(".migrationProgress").show();
                                                            $("#MigrationStart").addClass("disabled").off("click");

                                                            $.ajax({
                                                              url: migrateDataUrl,
                                                              type: "POST",
                                                              dataType: "JSON",
                                                              data: {   
                                                                   source: sources,
                                                                   target: targets,
                                                                   operation: operation
                                                              },
                                                              success: function(data){

                                                                  $migrationDialog.children(".migrationProgress").hide();

                                                                  if (!data.success) {
                                                                      info.show();
                                                                      feedback().error(data.status);
                                                                      return;
                                                                  }

                                                                  $migrationInfo.children(":not(#selSource)").hide();

                                                                  var $ul = $("<ul/>", {class: "StorageResult"}),
                                                                      id;
                                                                  $.each(data.data, function(i, source){
                                                                      id = source.uri.replace(/[^a-z0-9-_]+/ig, "_");
                                                                      $ul.append($('<li />', {text: source.testTakers + __(" test takers")}));
                                                                      $ul.append($('<li />', {text: source.deliveries + __(" deliveries")}));
                                                                      $ul.append($('<li />', {text: source.callIds + __(" call ids")}));
                                                                      $("#" + id).addClass("success").append($ul);
                                                                  });

                                                                  $("#selSource").prepend($("<h3/>", {text: data.status}));
                                                                  $migrationInfo.show();
                                                                  $("#MigrationClose").text(__("Close"));
                                                                  feedback().success(data.status);
                                                              },
                                                              error : function(){
                                                                  $migrationDialog.children(".migrationProgress").hide();
                                                                  $migrationInfo.show();
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