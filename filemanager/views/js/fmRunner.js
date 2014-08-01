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
define(['jquery', 'context'], function($, context){

    function FmRunner() {
	
	this.element = null;
	this.window = null; 
	this.defaultOpt = {
			'width' 	: '800px',
			'height'	: '650px',
			'menubar'	: 'no',
			'resizable'	: 'yes',
			'status'	: 'no',
			'toolbar'	: 'no',
			'dependent' : 'yes',
			'scrollbars': 'yes'
		};
                
    }
    
    FmRunner.prototype.load = function(options, callback){
        var self = this;
        if(options.elt){
                this.element = options.elt;
        }
        if(this.window !== null){
                //close previous window
                this.window.close();
        }
        var params = '';
        for (var i in this.defaultOpt){
                params += i + '=';
                (options[i]) ? params += options[i] :  params += this.defaultOpt[i];
                params += ',';
        }
        for (i in options) {
                if(!this.defaultOpt[i]){
                        params += i + '=' + options[i] + ',';
                }
        }

        // Show or not the "Select" button.
        var showSelectString;
        if (typeof(options['showselect']) !== 'undefined' && !options['showselect']){
                showSelectString = '0';
        } else{
                showSelectString = '1';
        }

        this.window = window.open(context.root_url + 'filemanager/Browser/index?showselect=' + showSelectString, 'filemanager', params);
        this.window.focus();
        
        $(window.top.document).on('fmSelect', function(e, urlData, mediaData){
                if(typeof callback === 'function'){
                    if(mediaData){
                            callback(self.element, urlData, mediaData);
                    }
                    else{
                            callback(self.element, urlData);
                    }
                }
        });
    };

    /**
     * Use this method instead of constructor to use the shared instance (singleton)
     * @param {Object} options the popup options
     * @return {Object} the created window ref
     */
    FmRunner.load = function(options, callback){
        return new FmRunner().load(options || {}, callback); 	//instanciate and load it
    };

    return FmRunner;

});