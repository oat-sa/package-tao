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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
function onBackward(event){
	install.setTemplate(event.target.parentNode.id);
}

function getSpinnerOptions(size){
	var opts;
	if (size == 'small'){
		opts = {
			lines: 9, // The number of lines to draw
			length: 3, // The length of each line
			width: 2, // The line thickness
			radius: 4, // The radius of the inner circle
			rotate: 0, // The rotation offset
			color: '#000', // #rgb or #rrggbb
			speed: 1.9, // Rounds per second
			trail: 60, // Afterglow percentage
			shadow: false, // Whether to render a shadow
			hwaccel: false, // Whether to use hardware acceleration
			className: 'spinner', // The CSS class to assign to the spinner
			zIndex: 2e9, // The z-index (defaults to 2000000000)
			top: '0', // Top position relative to parent in px
			left: '0' // Left position relative to parent in px
		};	
	}
	else if (size == 'large'){
		opts = {
			lines: 11, // The number of lines to draw
			length: 21, // The length of each line
			width: 8, // The line thickness
			radius: 26, // The radius of the inner circle
			rotate: 0, // The rotation offset
			color: '#555', // #rgb or #rrggbb
			speed: 1.5, // Rounds per second
			trail: 60, // Afterglow percentage
			shadow: false, // Whether to render a shadow
			hwaccel: false, // Whether to use hardware acceleration
			className: 'spinner', // The CSS class to assign to the spinner
			zIndex: 2e9, // The z-index (defaults to 2000000000)
			top: '0', // Top position relative to parent in px
			left: '0' // Left position relative to parent in px
		};
	}
		
	return opts;
}

function validify(element){
	element.onValid = function() { displayValidationMark(element); };
	element.onInvalid = function () { removeValidationMark(element); };
}

function validifyNotMandatory(element){
	element.onValid = function() { displayValidationMark(element); };
	element.onValidButEmpty = function() { removeValidationMark(element); };
	element.onInvalid = function () { removeValidationMark(element); };
}

function displayValidationMark(element){
	var $parent = $(element).parent();
	$parent.find('.validField').remove();
	$parent.append('<img src="images/valide.png" alt="valid" class="validField"/>');
}

function removeValidationMark(element){
	var $parent = $(element).parent();
	$parent.find('.validField').remove();
}
