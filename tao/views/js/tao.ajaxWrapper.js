//tao namespace
if(typeof(tao) == 'undefined'){
    tao = {};
}

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @subpackage views
 * @namespace wfApi
 * 
 * This file provides an ajax wrapper build on the top of the jquery
 * library.
 * 
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @version 0.2
 */
tao.ajaxWrapper = {};

/**
 * The global success callbacks
 * @private
 * @type {array}
 */
tao.ajaxWrapper.successCallbacks = [];

/**
 * The global error callbacks
 * @private
 * @type {array}
 */
tao.ajaxWrapper.errorCallbacks = [];

/**
 * The ajax method is an overloading of the jQuery ajax function, this function
 * get the same options than the original one. To get more information take a
 * look to the following documentation http://api.jquery.com/jQuery.ajax
 * 
 * This overloading makes homogeneous exchanges between the client and the 
 * server. 
 * 
 * The behavior of its parent function has been adapted to control every 
 * exchanges between the client and the server and so 
 * * intercept server exceptions 
 * * generate under control errors and allow client to manage them
 * 
 * @public
 * @param {array}       options Array of options
 * @param {function}    options.success The success callback function
 * @param {function}    options.error The error callback function which is called
 * if : the call failed; the server generate an exception; the server return a 
 * success=false
 */
tao.ajaxWrapper.ajax = function(options)
{
    var successCallback = tao.ajaxWrapper.defaultSuccessCallback;
    var errorCallback = tao.ajaxWrapper.defaultErrorCallback;
    if(options){
        if(typeof options.success != 'undefined'){
            successCallback = options.success;
            delete options.success;
        }
        if(typeof options.error != 'undefined'){
            errorCallback = options.error;
            delete options.error;
        }
    }
   
    //modify options, with jquery > 1.5 use $.ajax(...).success or $.ajax(...).error
    
    //Override the success callback
    options.success = function(result){
        //The ajax call has been executed with success
        tao.ajaxWrapper.ajaxSuccess(result, successCallback, errorCallback);
    };
    
    //Override the error callback
    options.error = function(result){
        //An error occured during the ajax call
        tao.ajaxWrapper.ajaxError(result, errorCallback);
    };
    
    return $.ajax(options);
};

/**
 * Ajax success callback.
 * 
 * Internal function.
 * @private
 * @param {array}       result The result' server
 * @param {string}      result.type Type of result (json,exception)
 * @param {boolean}     result.success The remote action has been performed with success or not
 * @param {mixed}       result.data Data sent by the server
 * @param {string}      result.message Message joined to the result
 * @param {function}    successCallback The success callback function
 * @param {function}    errorCallback The error callback function which is called
 * if the call failed or the server generate an exception.
 */
tao.ajaxWrapper.ajaxSuccess = function(result, successCallback, errorCallback)
{
    //Extract the result server data
    var resultData  = typeof result.data != 'undefined'         ? result.data       : null;
    var resultSuccess  = typeof result.success != 'undefined'   ? result.success    : null;
    var resultType  = typeof result.type != 'undefined'         ? result.type       : null;
    var resultMsg   = typeof result.message != 'undefined'      ? result.message    : null;
    
    //The result success has to be defined
    if(resultSuccess == null){
        throw new Error('tao.ajaxWrapper::ajaxSuccess, an error occured, the result server has to contain a "success" field');
    }
    
    //The result type has to be defined
    if(resultType == null){
        throw new Error('tao.ajaxWrapper::ajaxSuccess, an error occured, the result server has to contain a "type" field');
    }
    
    //If a trouble occurs
    if(resultType.toLowerCase   == 'exception'        // EXCEPTION
        || resultSuccess        != true               // REQUEST FAILED
    ){
        tao.ajaxWrapper.ajaxError(result, errorCallback);
    }
    //Else fire the succcess callback
    else{
        //Launch default success callbacks
        for(var i in tao.ajaxWrapper.successCallbacks){
            tao.ajaxWrapper.successCallbacks[i](resultData, result);
        }
        //Launch attached success callback
        successCallback(resultData, result);
    }
};

/**
 * Ajax error callback. 
 * 
 * Internal function.
 * @private
 * @param {function}    errorCallback The error callback function which is called
 */
tao.ajaxWrapper.ajaxError = function(result, errorCallback)
{
    errorCallback(result);
};

/**
 * The tao ajax default success callback.
 * Override this method to customize the default success callback
 * 
 * Internal function.
 * @private
 */
tao.ajaxWrapper.defaultSuccessCallback = function (){};

/**
 * The tao ajax default error callback.
 * Override this method to customize the default error callback
 * 
 * Internal function.
 * @private
 */
tao.ajaxWrapper.defaultErrorCallback = function (result)
{
    throw new Error (result.message);
};

/**
 * Add global success callback
 * @param {function}    successCallback The success callback function
 * @param {string}      position (optional) (begin, end, position number). By 
 * default the callback will be added to end of the list
 */
tao.ajaxWrapper.addSuccessCallback = function (successCallback, position)
{
    if(typeof position != 'undefined' && position != null){
        //insert at the begining
        if(position == 'begin'){
            tao.ajaxWrapper.successCallbacks.unshift(successCallback);
        }
        //insert at the end
        else if(position == 'end'){
            tao.ajaxWrapper.successCallbacks.push(successCallback);
        }
        //insert at the given position
        else if(parseInt(input)==input){
            for(var i=tao.ajaxWrapper.successCallbacks.length-1; i>=position; i--){
                tao.ajaxWrapper.successCallbacks[i+1] = tao.ajaxWrapper.successCallbacks[i];
            }
            tao.ajaxWrapper.successCallbacks[i] = successCallback;
        }
    }
    //By default the callback will be added to end of the list
    else{
        tao.ajaxWrapper.successCallbacks.push(successCallback);
    }
};

/**
 * Remove global success callback
 * @param {mixed}       target The target success callback to remove
 */
tao.ajaxWrapper.removeSuccessCallback = function (target)
{
    //tao.ajaxWrapper.successCallbacks.push(successCallback);
    throw new Error('the function has not been implemented tao.ajaxWrapper::removeSuccessCallback');
};

/**
 * Add global error callback
 * @param {function}    errorCallback The error callback function
 * @param {string}      position (optional) (begin, end, position number). By 
 * default the callback will be added to end of the list
 */
tao.ajaxWrapper.addErrorCallback = function (errorCallback, position)
{
    if(typeof position != 'undefined' && position != null){
        //insert at the begining
        if(position == 'begin'){
            tao.ajaxWrapper.errorCallbacks.unshift(errorCallback);
        }
        //insert at the end
        else if(position == 'end'){
            tao.ajaxWrapper.errorCallbacks.push(errorCallback);
        }
        //insert at the given position
        else if(parseInt(input)==input){
            for(var i=tao.ajaxWrapper.errorCallbacks.length-1; i>=position; i--){
                tao.ajaxWrapper.errorCallbacks[i+1] = tao.ajaxWrapper.errorCallbacks[i];
            }
            tao.ajaxWrapper.errorCallbacks[i] = errorCallback;
        }
    }
    //By default the callback will be added to end of the list
    else{
        tao.ajaxWrapper.errorCallbacks.push(errorCallback);
    }
};

/**
 * Remove global error callback
 * @param {mixed}       target The target error callback to remove
 */
tao.ajaxWrapper.removeErrorCallback = function (position)
{
    //tao.ajaxWrapper.errorCallbacks.push(successCallback);
    delete tao.ajaxWrapper.errorCallbacks[position];
};
