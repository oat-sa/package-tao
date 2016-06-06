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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA
 **/
define(['lodash', 'jquery'], function (_, $){
    'use strict';
    
    /**
     * Prefix used to the variable storage
     * @type String
     */
    var _prefix = 'x-tao-';
    
    /**
     * Check if the element is of a qti container type
     * 
     * @private
     * @param {Object} element
     * @returns {Boolean}
     */
    function _checkContainerType(element){
        if(_.isFunction(element.initContainer) && _.isFunction(element.body)){
            return true;
        }else{
            throw 'the element is not of a container type';
        }
    }
    
    /**
     * Get the body element of the container
     * 
     * @private
     * @param {Object} element
     * @returns {JQuery}
     */
    function _getBodyDom(element){
        if(_checkContainerType(element)){
            return $('<div>').html(element.body()).find('.x-tao-wrapper');
        }
    }
    
    /**
     * Add a class to the body element of the qti container
     * 
     * @private
     * @param {Object} element
     * @param {String} newClass
     * @param {String} [oldClass]
     */
    function _setBodyDomClass(element, newClass, oldClass){

        if(_checkContainerType(element) && (oldClass || newClass)){
            var $wrapper = $('<div>').html(element.body());
            //set css class to element
            _setDomClass($wrapper, newClass, oldClass);
            //set to the model
            element.body($wrapper.html());
        }
    }
    
    /**
     * Switch class to the wrapped DOM
     * 
     * @param {JQuery} $wrapper
     * @param {String} newClass
     * @param {String} oldClass
     * @returns {undefined}
     */
    function _setDomClass($wrapper, newClass, oldClass){
        var $bodyDom = $wrapper.find('.x-tao-wrapper');
        if(!$bodyDom.length){
            //create one
            $wrapper.wrapInner('<div class="x-tao-wrapper">');
            $bodyDom = $wrapper.find('.x-tao-wrapper');
        }
        if(oldClass){
            $bodyDom.removeClass(oldClass);
        }
        if(newClass){
            $bodyDom.addClass(newClass);
        }
    }
    
    /**
     * Add manually the encoded information to a dom element
     * 
     * @param {JQuery} $wrapper - the wrapper of the element that will holds the information
     * @param {String} dataName - the name of the information
     * @param {String} newValue - the new value to be added
     * @param {String} [oldValue] - the old value to be removed
     * @returns {undefined}
     */
    function setEncodedDataToDom($wrapper, dataName, newValue, oldValue){
        _setDomClass($wrapper, _getEncodedDataString(dataName, newValue), _getEncodedDataString(dataName, oldValue));
    }
    
    /**
     * Get the full variable name for the data store
     * 
     * @param {String} dataName
     * @param {String} value
     * @returns {String}
     */
    function _getEncodedDataString(dataName, value){
        if(dataName && value){
            return _prefix + dataName + '-' + value;
        }
        return '';
    }
    
    /**
     * Set a data string to the element identified by its dataName
     * 
     * @param {Object} element
     * @param {String} dataName
     * @param {String} newValue
     * @returns {undefined}
     */
    function setEncodedData(element, dataName, newValue){
        var oldValue = getEncodedData(element, dataName);
        return _setBodyDomClass(element, _getEncodedDataString(dataName, newValue), _getEncodedDataString(dataName, oldValue));
    }
    
    /**
     * Remove the stored data from the element by its dataName
     * 
     * @param {Object} element
     * @param {String} dataName
     * @returns {unresolved}
     */
    function removeEncodedData(element, dataName){
        var oldValue = getEncodedData(element, dataName);
        if(dataName && oldValue){
            _setBodyDomClass(element, '', _getEncodedDataString(dataName, oldValue));
        }
    }

    /**
     * Check if the stored data exist
     * 
     * @param {Object} element
     * @param {String} dataName
     * @param {String} value
     * @returns {Boolean}
     */
    function hasEncodedData(element, dataName, value){
        var $body = _getBodyDom(element);
        if($body && $body.length && dataName && value){
            return $body.hasClass(_getEncodedDataString(dataName, value));
        }
        return false;
    }
    
    /**
     * Get the encoded data identified by its dataName
     * 
     * @param {Object} element
     * @param {String} dataName
     * @returns {String}
     */
    function getEncodedData(element, dataName){
        var regex, matches;
        var $body = _getBodyDom(element);
        if(dataName && $body && $body.length && $body.attr('class')){
            regex = new RegExp(_prefix + dataName + '-([a-zA-Z0-9\-._]*)');
            matches = $body.attr('class').match(regex);
            if(matches){
                return matches[1];
            }
        }
    }
    
    /**
     * Provide a set of helper functions to set,retirve and manage string data to a container type qti element.
     */
    return {
        setEncodedData : setEncodedData,
        hasEncodedData : hasEncodedData,
        getEncodedData : getEncodedData,
        removeEncodedData : removeEncodedData,
        setEncodedDataToDom : setEncodedDataToDom
    };
});