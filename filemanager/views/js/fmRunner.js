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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */
/**
 * FmRunner Class 
 * Enable you to run the filemanager from an other extension
 * @example FmRunner.load({width: '1024', height: '768'})
 */
FmRunner = function() {
	
	//save the instance
	window.fmRunner = this.constructor;
	
	//this part is loaded only the first call
	if(window.fmRunner.single == undefined){
		window.fmRunner.single = this;
	
		window.fmRunner.single.element = null;
		window.fmRunner.single.window = null; 
		window.fmRunner.single.defaultOpt = {
			'width' 	: '800px',
			'height'	: '650px',
			'menubar'	: 'no',
			'resizable'	: 'yes',
			'status'	: 'no',
			'toolbar'	: 'no',
			'dependent' : 'yes',
			'scrollbars': 'yes'
		};
		
		window.fmRunner.single.load = function(options, callback){
			if(options.elt){
				window.fmRunner.single.element = options.elt;
			}
			if(window.fmRunner.single.window != null){
				//close previous window
				window.fmRunner.single.window.close();
			}
			var params = '';
			for (i in window.fmRunner.single.defaultOpt){
				params += i + '=';
				(options[i]) ? params += options[i] :  params += window.fmRunner.single.defaultOpt[i];
				params += ',';
			}
			for (i in options) {
				if(!window.fmRunner.single.defaultOpt[i]){
					params += i + '=' + options[i] + ',';
				}
			}

			// Show or not the "Select" button.
			var showSelectString;
			if (typeof(options['showselect']) != 'undefined' && options['showselect'] == false){
				showSelectString = '0';
			}else{
				showSelectString = '1';
			}
			
			window.fmRunner.single.window = window.open(root_url + 'filemanager/Browser/index?showselect=' + showSelectString, 'filemanager', params);
			window.fmRunner.single.window.focus();
			$(document).off('fmSelect').on('fmSelect', function(e){
				e.preventDefault();
				if(window.fmRunner.single.urlData && callback != null && callback != undefined){
					if(window.fmRunner.single.mediaData){
						callback(window.fmRunner.single.element, window.fmRunner.single.urlData, window.fmRunner.single.mediaData);
					}
					else{
						callback(window.fmRunner.single.element, window.fmRunner.single.urlData);
					}
				}
			});
			
			return window.fmRunner.single.window;
		};
	}		
	else {
		//return singleton if already initialized
		return window.fmRunner.single;
	}
};

/**
 * Use this method instead of constructor to use the shared instance (singleton)
 * @param {Object} options the popup options
 * @return {Object} the created window ref
 */
FmRunner.load = function(options, callback){
	if(options == undefined){
		options = {};
	}
	return new FmRunner().load(options, callback); 	//instanciate and load it
};
