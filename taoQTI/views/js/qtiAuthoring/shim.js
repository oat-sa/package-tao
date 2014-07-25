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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * custom object serialization method to jQuery
 */

$.fn.serializeObject = function(print)
{
    var o = {};
    var a = this.serializeArray();
	var out = '';
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
				if(print) out += this.name + ':' + o[this.name] +', ';
            }
            o[this.name].push(this.value || '');
			if(print) out += this.name + ':' + this.value +', ';
        } else {
            o[this.name] = this.value || '';
			if(print) out += this.name + ':' + this.value +', ';
        }
    });
	
	if(print){
		if(console){
			CL(out)
		}else{
			alert(out);
		}
	}
    return o;
};

/**
 * Temporaly highly a div container
 */
$.fn.highlight = function(){
	if(!$(this).hasClass('ui-state-highlight')){
		$(this).addClass('ui-state-highlight', 0);
		var $this = $(this);
		setTimeout(function(){
			$this.removeClass('ui-state-highlight', 1000);
		},1000);
	}
}

/**
* Add  Object.keys for ie8
*/
if (!Object.keys) {
    Object.keys = function keys(object){
		var keys = [];
        for(var key in object){
			keys.push(key);
		}
        return keys;
    };

}