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
define(['jquery', 'i18n', 'helpers', 'module', 'layout/section', 'ui/feedback'], 
function ($, __, helpers, module, section, feedback) {
    'use strict';

	function switchList() {
		var current = $(this).parent().attr('id');
        var target; 
		if (current == 'excludedList') {
			target = $('#assignedList'); 
		} else if (current == 'assignedList') {
			target = $('#excludedList');
		}
		if (typeof target !== 'undefined') {
			target.append(this);
			this.scrollIntoView();
		}
	}
	
	var $search = $('#tt-filter');
    var $list   = $('#assignedList');
    var timeout;
    
    var liveSearch = function(){
        var pattern = $search.val();
        clearTimeout(timeout);
        timeout = setTimeout(function(){
        	filterList(new RegExp(pattern), $list);
        }, 300);
    };
    
    var filterList = function(regex, list) {
    	list.children().each(function(index, element) {
    		if ($(element).text().match(regex) === null) {
    			$(element).hide();
    		} else {
    			$(element).show();
    		}
    	});
    };
	
    return {
        start : function(){
	        $('#save-tt').click(function() {
	        	var excluded = [];
	        	$('#excludedList > li').each(function(index) {
	        		excluded.push($(this).data('uri'));
	        	});
	        	var assemblyUri = $('input[name="assemblyUri"]').val();
	        	$.ajax({
	        		url: helpers._url('saveExcluded', 'DeliveryMgmt', 'taoDeliveryRdf'),
					type: "POST",
					data: {
						uri: assemblyUri,
						excluded: JSON.stringify(excluded)
					},
					dataType: 'json',
					success: function(response) {
						if (response.saved) {
                            feedback().success(__('Selection saved successfully'));
                            section.loadContentBlock(helpers._url('editDelivery', 'DeliveryMgmt', 'taoDeliveryRdf'), {uri: assemblyUri});
						}
					}
				});
	        });
        	$('#assignedList > li').click(switchList);
        	$('#excludedList > li').click(switchList);
        	
        	$('#close-tt').click(function() {
        		$('#testtaker-form').modal('close');
        	});
                
            //trigger the search on keyp and on the magnifer button click
            $search.keyup(liveSearch).siblings('.ctrl').click(liveSearch);
        	
        }
    };
});


