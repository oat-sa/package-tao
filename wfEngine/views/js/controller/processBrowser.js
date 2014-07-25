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
define(['jquery', 'lodash', 'wfEngine/WfRunner'], function($, _, WfRunner){
    
    return {
        start : function(options){
            
            var $back = $('#back');
            var $next = $("#next");
            
            function deactivateControls(){
                 $back.off('click').attr('active', false);
                 $next.off('click').attr('active', false);
            }
            
            var wfRunner = new WfRunner(
                options.activityExecutionUri,
                options.processUri,
                options.activityExecutionNonce
            );
    
            _.forEach(options.services, function(service){
                wfRunner.initService(service.api, $('#' + service.frameId), service.style);
            });

            $back.click(function(e){
                e.preventDefault();
                deactivateControls();
                wfRunner.backward();
            });
            
            $next.click(function(e){
                e.preventDefault();
                deactivateControls();
                wfRunner.forward();
            });

            $("#debug").click(function(){
                $("#debugWindow").toggle('slow');
            });
        }
    };
            
});