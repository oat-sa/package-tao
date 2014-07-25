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
 * Copyright (c) 2013
 *  
 */

define(['jquery', 'helpers'], function($, helpers){

    function Lock (resourceUri){
        this.uri = resourceUri;
    }

    /*
     * @param {type} callback
     * @returns {undefined}
     */
    Lock.prototype.release = function (successCallBack, failureCallBack){

        var releaseUrl = helpers._url('release', 'lock', 'tao' );
        var options = { 
            data: { uri : this.uri },
            type: 'POST',
            dataType: 'json'
        };
        $.ajax(releaseUrl, options).done(function(retData, textStatus, jqxhr){
            successCallBack();
        }).fail(function(jqxhr){
            failureCallBack();
        });
    };

    return Lock;
    
});
