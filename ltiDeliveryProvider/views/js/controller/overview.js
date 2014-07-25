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
//the url of the app config is set into the data-config attr of the loader.
var appConfig = document.getElementById('amd-loader').getAttribute('data-config');
require([appConfig], function(){
    require(['jquery', 'helpers'], function ($, helpers) {
		$('#repeatButton').click(function(e) {
			e.preventDefault();
			$.ajax({
				url: helpers._url('repeat', 'DeliveryRunner'),
	            type: "POST",
	            data: {
	            	uri : $(this).data('uri')
	            },
	            success: function(response){
                    if(response.added){
                            initFileTree(parentDir.replace(/\/$/, ''));
                    }
	            }
			});
		});
	});
});