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
define(['jquery'], function($){

    /**
     * TaoInstall API interface. 
     */
    function TaoInstall (){
        this.url = 'api.php';
        this.feedbackUrl = 'http://forge.taotesting.com/support/installation/';
        this.templatePrefix = 'tpl_';
        this.templateSuffix = '.html';
        this.frameId = null;
        this.frame = null;
        this.currentTemplateId = null;
        this.nextable = false;
        this.onNextable = function() {};
        this.onUnnextable = function() {};
        this.registeredElements = [];
        this.helpMessages = {};
        this.data = {};
    }

    /**
     * Changes the current template by giving its ID. 
     */
    TaoInstall.prototype.setTemplate = function (templateId){
        if (templateId != this.currentTemplateId){
            
            // If it is not the first template displayed...
            if (this.frame != null){
                // We unregister any element bound with
                // the API.
                this.clearRegisteredElements();
                
                // We remove any help message in the store.
                this.clearHelp();
                
                // It is not nextable anymore.
                this.nextable = false;
                
                // Event heandlers are reset.
                this.onNextable = function() {};
                this.onUnnextable = function() {};
                
                // We remove the frame handling the last
                // template.
                $('#mainFrame').remove();
                this.frame = null;
            }
            
            this.frame = document.createElement('iframe');
            $(this.frame).attr('id', this.frameId)
                         .attr('scrolling', 'no')
                         .attr('frameborder', 0);
            this.currentTemplateId = templateId;
            
            // We initialize the API and handlers for DOM injection.
            this.init();
            
            // We set the frame content.
            var frameSrc = this.templatePrefix + this.currentTemplateId + this.templateSuffix;
            $(this.frame).attr('src', frameSrc);
            $('body').append(this.frame);
        }
    };

    /**
     * Check if the server-side can talk JSON.
     */
    TaoInstall.prototype.sync = function(callback){
        // Check if we can discuss using JSON and receive information
        // from the server about the installation to perform.
        var data = "type=Sync";
        
        var options = {data: data,
                   type: 'GET',
                   dataType: 'json'};
                   
        $.ajax(this.url, options).done(function(data, textStatus, jqxhr){callback(jqxhr.status, data)})
                                 .fail(function(jqxhr){callback(jqxhr.status)});
    };

    /**
     * Check the configuration on the server-side. 
     */
    TaoInstall.prototype.checkConfiguration = function(checks, callback){
        
        if (checks != null){
            // We send the checks to perform.
            var data = {type: 'CheckPHPConfig',
                        value: checks};
        
            var options = {data: JSON.stringify(data),
                           type: 'POST',
                           dataType: 'json'};	
        }
        else{
            // The checks to perform are chosen by the server-side.
            var data = {type: 'CheckPHPConfig'}
            if (typeof this.data['extensions'] != undefined) {
                data['extensions'] = this.data['extensions'];
            }
            var options = {data: data, type: 'GET', dataType: 'json'};
        }
                    
        $.ajax(this.url, options).done(function(data, textStatus, jqxhr){callback(jqxhr.status, data)})
                                .fail(function(jqxhr){callback(jqxhr.status)});
    };

    /**
     * Check the database connection on the server-side 
     */
    TaoInstall.prototype.checkDatabaseConnection = function(check, callback){
        check.password = this.nullToEmptyString(check.password);
        
        var data = {type: 'CheckDatabaseConnection',
                    value: check};
                    
        var options = {data: JSON.stringify(data),
                       type: 'POST',
                       dataType: 'json'};
                       
        $.ajax(this.url, options).done(function(data, textStatus, jqxhr){callback(jqxhr.status, data)})
                                 .fail(function(jqxhr){callback(jqxhr.status)});
    };

    /**
     * Check connection to TAO Forge
     */
    TaoInstall.prototype.checkTAOForgeConnection = function(check, callback){
        
            check.password = this.nullToEmptyString(check.password);
        
        var data = {type: 'CheckTAOForgeConnection',
                    value: check};
                    
        var options = {data: JSON.stringify(data),
                       type: 'POST',
                       dataType: 'json'};
                       
        $.ajax(this.url, options).done(function(data, textStatus, jqxhr){callback(jqxhr.status, data)})
                                 .fail(function(jqxhr){callback(jqxhr.status)});
    };

    TaoInstall.prototype.install = function(inputs, callback){
        inputs.db_pass = this.nullToEmptyString(inputs.db_pass);
        
        var data = {type: 'Install',
                    value: inputs,
                    timeout: 300000}; // 5 minutes max.
                    
        var options = {data: JSON.stringify(data),
                       type: 'POST',
                       dataType: 'json'};
                       
        $.ajax(this.url, options).done(function(data, textStatus, jqxhr){callback(jqxhr.status, data)})
                                 .fail(function(jqxhr){callback(jqxhr.status)});		   
    };

    /**
     * Indicates if the current template is 'nextable' or not.
     */
    TaoInstall.prototype.isNextable = function(){
        return this.nextable;
    };

    /**
     * Tell the API that the current template is 'nextable'. 
     */
    TaoInstall.prototype.setNextable = function(value){
        this.nextable = value;
        
        if (value == true){
            this.onNextable();
        }
        else{
            this.onUnnextable();
        }
    };

    /**
     * Register a tao-input element for validation. 
     */
    TaoInstall.prototype.register = function(element){
        if ($(element).hasClass('tao-input') && typeof(element.isValid) == 'function'){
            this.registeredElements.push(element);
        }
        else {
            throw "Tao Install API Error: only 'tao-input' elements with an 'isValid' function are registrable. Element '" + $element.attr('id') + "' is not registrable.";
        }
    };

    /**
     * Unregister a particular tao-input element. 
     */
    TaoInstall.prototype.unregister = function(element){
        for (i in this.registeredElements){
            if (this.registeredElements[i] == element){
                delete this.registeredElements[i];
            }
        }
    };

    /**
     * Unregister all registered tao-input elements. 
     */
    TaoInstall.prototype.clearRegisteredElements = function(){
        this.registeredElements = [];
    };

    /**
     * Notify the API that tao-input elements were added, modified, removed...
     * and that the registered elements can be checked again. 
     */
    TaoInstall.prototype.stateChange = function(){
        this.checkRegisteredElements();
        this.storeDataForRegisteredElements();
    };

    /**
     * Add an help message in the help store identified by a given key.
     *  
     * @param {Object} key
     * @param {Object} message
     */
    TaoInstall.prototype.addHelp = function(key, message){
        this.helpMessages[key] = message;
    };

    /**
     * Remove an help message identified in the help store by a given key.
     *  
     * @param {Object} key
     */
    TaoInstall.prototype.removeHelp = function(key){
        if (typeof(this.helpMessages[key]) != 'undefined'){
            delete this.helpMessages[key];
        }
    };

    /**
     * Reinitialize the help store. Messages contained
     * by the help store before calling this method will be lost. 
     */
    TaoInstall.prototype.clearHelp = function(){
        this.helpMessages = {};
    };

    /**
     * Get an help message from the help store for a given key.
     * Returns null if no message is bound for the given key.
     *  
     * @param {Object} key
     */
    TaoInstall.prototype.getHelp = function(key){
        if (typeof(this.helpMessages[key]) != 'undefined'){
            return this.helpMessages[key];
        }
        else{
            return null;
        }
    };

    /**
     * Add a piece of data associated to a given key in the data store. 
     * If some data was already associated with this key, it will be overriden
     * by the new one.
     * @param {String} key
     * @param {Object} data
     */
    TaoInstall.prototype.addData = function(key, data){
        this.data[key] = data;
    };

    /**
     * Remove a pice of data associated to in a given key in the data store.
     * If no data is bound to the provided key, nothing happens. 
     * @param {String} key
     */
    TaoInstall.prototype.removeData = function(key){
        if (typeof(this.data[key]) != 'undefined'){
            delete this.data[key];
        }
    };

    /**
     * Get an 'isValid' validation method for an input that will be registered
     * by the API.
     * 
     * @param {Object} element The Input HTML element for which you want a validator.
     * @param {Object} options Validation options (integer -> min, max; string -> length, ...)
     * @return {function}
     */
    TaoInstall.prototype.getValidator = function(element, options){
        var $element = $(element);
        var api = this;
        
        // Mandatory field by default.
        $element.prop('tao-mandatory', true);
        
        switch ($element.prop('tagName').toLowerCase()){
            case 'input':
            case 'textarea':
                if ($element.prop('tagName').toLowerCase() == 'textarea' || 
                    $element.attr('type') == 'text' ||
                    $element.attr('type') == 'password' ||
                                $element.attr('type') == 'hidden'){
                    
                    var firstValueFunction = function () { return $element.val() != $element[0].firstValue || !mandatory; };
                    
                    if (typeof(options) != 'undefined'){
                        
                        var mandatory = (typeof(options.mandatory) == 'undefined') ? true : options.mandatory;
                        $element.prop('tao-mandatory', mandatory);
                        
                        if (typeof(options.dataType) != 'undefined'){
                            
                            switch (options.dataType){
                                case 'regexp':
                                    var reg = new RegExp(options.pattern);
                                    element.isValid = function(){ return firstValueFunction() && reg.test($element.val()); };
                                break;
                            
                                case 'url':
                                    var reg = new RegExp("(http|https)://[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:/~+#-]*[\w@?^=%&amp;/~+#-])?");
                                    element.isValid = function(){ return firstValueFunction() && reg.test($element.val()); };							
                                break;
                                
                                case 'dbname':
                                    var reg = new RegExp("^[a-zA-Z0-9_]{3,63}$");
                                    element.isValid = function(){ return firstValueFunction() && reg.test($element.val()); };
                                break;
                                
                                case 'dbhost':
                                    var reg = new RegExp("[a-zA-Z0-9]{3,}(?::[0-9]{1,5})*");
                                    element.isValid = function(){ return firstValueFunction() && reg.test($element.val()); };
                                break;
                                
                                case 'host':
                                    var reg = new RegExp("(https?:\/\/)(www\.)?([a-zA-Z0-9\-.\/_]){3,}(:[0-9]{1,5})?");
                                    element.isValid = function(){ return firstValueFunction() && reg.test($element.val()); };
                                break;
                                
                                case 'email':
                                    element.isValid = function(){
                                        var reg = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i;
                                        
                                        if (mandatory == false && $element[0].getData() == null){
                                            return true;
                                        }
                                        else{
                                            return firstValueFunction() && reg.test($element.val());
                                        }
                                    }
                                break;
                                
                                case 'string':
                                    var sameAsFunction = function(){ var value = ($element[0].getData() != null) ? $element[0].getData() : ''; return api.getRegisteredElement(options.sameAs).getData() == value; };
                                                            
                                    if (typeof(options.min) == 'undefined' && typeof(options.max) == 'undefined'){
                                        // no min, no max.
                                        if (typeof(options.sameAs) == 'undefined'){
                                            
                                            element.isValid = function(){ 
                                                if (mandatory == false && $element[0].getData() == null){
                                                    return true;
                                                }
                                                else{
                                                    return firstValueFunction();	
                                                } 
                                            };
                                        }
                                        else{
                                            element.isValid = function(){
                                                if (mandatory == false && $element[0].getData() == null){
                                                    return true;
                                                }
                                                else{
                                                    return firstValueFunction() && sameAsFunction();
                                                }
                                            };
                                        }
                                    }
                                    else if (typeof(options.min) == 'undefined'){
                                        // max only.
                                        if (typeof(options.sameAs) == 'undefined'){
                                            element.isValid = function() {
                                                if (mandatory == false && $element[0].getData() == null){
                                                    return true;	
                                                }
                                                else{
                                                    var value = ($element[0].getData() != null) ? $element[0].getData() : '';
                                                    return firstValueFunction() && value.length <= options.max;
                                                }
                                            };
                                        }
                                        else{
                                            element.isValid = function() {
                                                if (mandatory == false && $element[0].getData() == null){
                                                    return true;	
                                                }
                                                else{
                                                    var value = ($element[0].getData() != null) ? $element[0].getData() : '';
                                                    return firstValueFunction && value.length <= options.max && sameAsFunction();
                                                }
                                            };
                                        }
                                    }
                                    else if (typeof(options.max) == 'undefined'){
                                        // min only.
                                        if (typeof(options.sameAs) == 'undefined'){
                                            element.isValid = function() { var value = ($element[0].getData() != null) ? $element[0].getData() : ''; return firstValueFunction() && value.length >= options.min; };
                                        }
                                        else{
                                            element.isValid = function() {
                                                if (mandatory == false && $element[0].getData() == null){
                                                    return true;
                                                }
                                                else{
                                                    var value = ($element[0].getData() != null) ? $element[0].getData() : '';
                                                    return firstValueFunction() && value.length >= options.min && sameAsFunction();	
                                                }
                                            };
                                        }
                                    }
                                    else{
                                        // min and max.
                                        if (typeof(options.sameAs) == 'undefined'){
                                            
                                            element.isValid = function() { 
                                                if (mandatory == false && $element[0].getData() == null){
                                                    return true;
                                                }
                                                else{
                                                    var value = ($element[0].getData() != null) ? $element[0].getData() : ''; 
                                                    return firstValueFunction() && value.length >= options.min && value.length <= options.max;	
                                                } 
                                            };
                                        }
                                        else{
                                            element.isValid = function() {
                                                if (mandatory == false && $element[0].getData() == null){
                                                    return true;
                                                }
                                                else{
                                                    var value = ($element[0].getData() != null) ? $element[0].getData() : '';
                                                    return firstValueFunction() && value.length >= options.min && value.length <= options.max && sameAsFunction();	
                                                }
                                            };
                                        }
                                    }
                                break;
                            }
                        }
                    }
                    else{
                        var mandatory = true;
                        element.isValid = function(){ return firstValueFunction() && $element.val().length > 0; };	
                    }
                }
                else if ($element.attr('type') == 'checkbox' || $element.attr('type') == 'radio'){
                    element.isValid = function(){ return true; };
                }
            break;
            
            case 'select':
                element.isValid =  function() { return true; };
            break;
            
            default:
                throw "No support for element with id '" + $element.attr('id') + "'.";
            break;
        }
    };

    /**
     * Get a 'getData' validation method for an input that will be registered
     * by the API.
     * @param {Object} element The Input HTML element for which you want a data getter.
     * @return {function}
     */
    TaoInstall.prototype.getDataGetter = function(element){
        $element = $(element);

        switch ($element.prop('tagName').toLowerCase()){
            case 'input':
            case 'textarea':
                if ($element.prop('tagName').toLowerCase() == 'textarea' || $element.attr('type') == 'text' || $element.attr('type') == 'password'){
                    element.getData = function(){ return (this.value != this.firstValue) ? ((this.value == '') ? null : this.value) : null; };	
                }
                else if ($element.attr('type') == 'hidden') {
                    element.getData = function(){ return this.value; };
                }
                else if ($element.attr('type') == 'checkbox'){
                    element.getData = function(){ return element.checked; };
                }
            break;
            
            case 'select':
                element.getData = function(){ return this.options[this.selectedIndex].value; };
            break;
            
            default:
                throw "No support for element with id '" + $element.attr('id') + "'.";
            break;
        }
    };

    TaoInstall.prototype.getDataSetter = function(element){
        $element = $(element);
        var api = this;
        
        switch ($element.prop('tagName').toLowerCase()){
            
            case 'input':
            case 'textarea':
                if ($element.prop('tagName').toLowerCase() == 'textarea' || $element.attr('type') == 'text' || $element.attr('type') == 'password' || $element.attr('type') == 'hidden'){
                    element.setData = function(data) { this.value = data; };	
                }
                else if ($element.attr('type') == 'checkbox'){
                    element.setData = function(data) { $(this).attr('checked', data); };
                }
            break;
            
            case 'select':
                element.setData = function(data) {
                    var count = this.options.length;
                    for (i = 0; i < count; i++){
                        if (this.options[i].value == data){
                            this.options[i].selected = true;
                            break;
                        }
                    }
                };
            break;
            
            default:
                throw "No support for element with id '" + $element.attr('id') + "'.";
            break;
        }
    };

    /**
     * Clear the data store. 
     */
    TaoInstall.prototype.clearData = function(){
        this.data = {};
    };

    /**
     * Get a piece of data associated to the provided key in the data
     * store. If no data is bound to the key, null is returned.
     * @param {String} key; 
     */
    TaoInstall.prototype.getData = function(key){
        if (typeof(this.data[key]) != 'undefined'){
                // type
            return this.data[key];
        }
        else {
            return null;
        }
    };

    /**
     * Popuplate registered inputs of the currently displayed template with
     * data stored in the data store. If keys of the store matches
     * an input id, this input will be populated.
     * @return {Array} A set of element that were successfully populated.
     */
    TaoInstall.prototype.populate = function(){
        var populated = [];
        
        for (i in this.registeredElements){
            var element = this.registeredElements[i];
            if (typeof(element.setData) == 'function' && this.getData(element.id) != null){
                element.setData(this.getData(element.id));
                populated.push(element);
            }
        }
        
        // If something was populated, it means a state change might occur.
        this.stateChange();
        
        return populated;
    };

    /**
     * Invoke this method to redirect the end user to a specific URL.
     * @param {String} A Uniform Resource Locator.
     */
    TaoInstall.prototype.redirect = function(url) {
        window.location.href = url;
    };

    // ----------- Private methods
    TaoInstall.prototype.init = function(){	
        var that = this;
        
        if (jQuery.browser.msie)
        {
            this.frame.onreadystatechange = function(){	
                if(this.readyState == 'complete'){
                    that.inject();	
                }
            };
        }
        else
        {		
            this.frame.onload = function(){
                that.inject();					
            };
        }
    };

    TaoInstall.prototype.inject = function(){	
        var doc = this.getDocument();
        doc.install = this;
        
        // Trigger the onLoad method of the API client to bootstrap
        // the template logic.
        if (doc.onLoad)
        {
            doc.onLoad();
        }
    };

    TaoInstall.prototype.checkRegisteredElements = function(){
        var validity = true;
        
        for (i in this.registeredElements){
            
            var $registeredElement = $(this.registeredElements[i]);
            
            if (!$registeredElement[0].isValid()){
                validity = false;
                
                // The field is not valid.
                // If it is not mandatory and that we have no value,
                // we cannot consider it as 'invalid'.
                if (typeof($registeredElement[0].onInvalid) == 'function'){
                    $registeredElement[0].onInvalid();
                }
            }
            else{
                // The field is valid.
                // If not mandatory and empty, we call onValidButEmpty otherwise
                // we call onValid.
                if ($registeredElement.prop('tao-mandatory') == false &&
                    $registeredElement[0].getData() == null &&
                    typeof($registeredElement[0].onValidButEmpty) == 'function'){
                    
                    $registeredElement[0].onValidButEmpty();
                }
                else if(typeof($registeredElement[0].onValid) == 'function') {
                    $registeredElement[0].onValid();
                }
            }
        }
        
        this.setNextable(validity);
    };

    TaoInstall.prototype.storeDataForRegisteredElements = function(){
        for (i in this.registeredElements){
            var element = this.registeredElements[i];

            if (typeof(element.getData) == 'function'){
                this.addData(element.id, element.getData());
            }
        }
    };

    TaoInstall.prototype.getDocument = function(){
        var doc = (this.frame.contentWindow || this.frame.contentDocument);
        return doc;
    };

    TaoInstall.prototype.getRegisteredElement = function(id){
        for (i in this.registeredElements){
            var element = this.registeredElements[i];
            if (element.id == id){
                return element;
            }
        }
        
        return null;
    };

    TaoInstall.prototype.nullToEmptyString = function(value){
        if (typeof(value) == 'undefined' || value == null){
            value = '';
        }
        
        return value;
    };
    return TaoInstall;
});
