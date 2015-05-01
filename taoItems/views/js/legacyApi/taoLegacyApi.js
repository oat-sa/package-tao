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
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

  ////////////////////
 // Constants      //
////////////////////

var RECOVERY_CONTEXT_PREFIX = 'rc_';

/*
 * 
 */
var _itemApi = new ItemApi();

/**
 * Called once the API is setup
 */
function onItemApiReady() {
	_itemApi.setImplementation(itemApi);
};

////////////////////
// TAO  variables //
////////////////////

function getEndorsment(){
	
}
function setEndorsment(endorsment){
	
}

function getScore(){
	
}

function setRecoveryContext(identifier, value) {
	_itemApi.setVariable(RECOVERY_CONTEXT_PREFIX + identifier, value);
}

function getRecoveryContext(identifier, callback) {
	_itemApi.getVariable(RECOVERY_CONTEXT_PREFIX + identifier, callback);
}
/**
 * Set the final score of the item
 * 
 * @function
 * @param {String|Number} score
 */
function setScore(score){
	values = {SCORE : score};
	_itemApi.saveScores(values);
}

function getScoreRange(){
	
}

function setScoreRange(max, min){
	
}

function getAnsweredValues(){
	
}

/**
 * Set the values answered by the subject.
 * If the item contains a free text field, 
 * you can record here the complete response. 
 * 
 * @function
 * @param {Object} values
 */
function setAnsweredValues(encodedValues){
	var values = JSON.parse(encodedValues);
	_itemApi.saveResponses(values);
}

/**
 * Get the data of the user currently doing the item  (the subject)
 * 
 * @function
 * @returns {Object} all the data related to the subject
 */
function getSubject(){
	
}

/**
 * Get the login of the subject
 * 
 * @function
 * @returns {String} the subject's login
 */
function getSubjectLogin(){
	
}

/**
 * Get the name of the subject (firstname and lastname)
 * 
 * @function
 * @returns {Object} the subject's name
 */
function getSubjectName(){
	
}

/**
 * Get the current item's informations 
 * 
 * @function
 * @returns {Object} the item's data (uri, label)
 */
function getItem(){
	return taoStack.getTaoVar(URI.ITEM);
}


/**
 * Get the informations of the currently running test 
 * 
 * @function
 * @returns {Object} the test's data (uri, label)
 */
function getTest(){
	
}

/**
 * Get the informations of the current delivery
 * 
 * @function
 * @returns {Object} the delivery's data (uri, label)
 */
function getDelivery(){
	
}


  //////////////////////
 // User's variables //
//////////////////////

/**
 * This function enables you to create and edit custom variables: the <i>user's variables</i>
 * The variable is identified by a key you have chosen.
 * This variable will be saved temporarly into the taoApi.
 * When you call the <code>push()</code> function, the <i>user's variables</i> are sent to the server.
 * It's a way to record some data other than the results and the events.
 * 
 * @function
 * @param {String} key to identify of the variable
 * @param {String|number|boolean} the value of the variable
 */
function setUserVar(key, value){
	var arr = {};
	arr[key] = value;
	_itemApi.saveScores(arr);
}

/**
 * Get a previously defined user's variable.
 * 
 * @function
 * @param {String} key the key of the variable you want to retrieve
 * @returns {String|number|boolean}
 */
function getUserVar(key){
	
}


  /////////////
 // STATES  //
/////////////
var STATE = {
	'ITEM' : {
		'PRE_FINISHED' 	: 'pre_item_finished',
		'FINISHED' 		: 'item_finished',
		'POST_FINISHED' : 'post_item_finished'
	}
};
/**
 * Add a callback that will be executed on finish state.
 * 
 * @function
 * @param {function} callback
 */
function onFinish(callback){
	$(window).bind(STATE.ITEM.FINISHED, callback);
}

/**
 * Add a callback that will be executed on finish but before the other callbacks  
 * 
 * @function
 * @param {function} callback
 */
function beforeFinish(callback){
	$(window).bind(STATE.ITEM.PRE_FINISHED, callback);
}

/**
 * Add a callback that will be executed on finish but after the other callbacks  
 * 
 * @function
 * @param {function} callback
 */
function afterFinish(callback){
	$(window).bind(STATE.ITEM.POST_FINISHED, callback);
}

/**
 * Register a callback to run the legacy triggers
 */
_itemApi.beforeFinish(function() {
	$(window).trigger(STATE.ITEM.PRE_FINISHED);
	$(window).trigger(STATE.ITEM.FINISHED);
	$(window).trigger(STATE.ITEM.POST_FINISHED);
});


/**
 * Define the item's state as finished.
 * This state can have some consequences.
 * 
 * @function
 */
function finish(){
	_itemApi.finish();
}

  //////////////////////////////
 // INTERFACE COMMUNICATION  //
//////////////////////////////

/**
 * Get the communication token (this token is sent at each communication)
 * 
 * @function
 * @returns {String} the token
 */
function getToken(){
}

/**
 * This fuction enables you to set up the data the item need.
 * You can retrieve this data from either a remote or a manual source.
 * <b>If you don't need to change the default values, don't call this function.</b>
 * 
 * @function
 * 
 * @param {Object} environment <i>set to null if you want to keep all the default values</i>
 * @param {String} [environment.type = "async"] the datasource type <b>(manual|sync|async)</b> 
 * @param {String} [environment.url = "/taoDelivery/ResultDelivery/initialize"] the url of the server where the data are sent 
 * @param {Object} [environment.params] the additional parameters to send with the data
 * 
 * @param {Object} settings <i>set to null if you want to keep all the default values</i>
 * @param {String} [settings.format = "json"] the data format. <i>Only json is supported in the current version</i> 
 * @param {String} [settings.method = "post"] HTTP method to push the data <b>(get|post)</b>
 */
function initDataSource(environment, settings){
}

/**
 * This function is a convenience method to add directly the datasource 
 * by writing the data in the source object (JSON) .
 *   
 * @function
 * @param {Object} source
 */
function initManualDataSource(source){
}


/**
 * Initialize the push communication.
 * <b>If you don't need to change the default values, don't call this function.</b>
 * 
 * @function
 * 
 * @param {Object} environment <i>set to null if you want to keep all the default values</i>
 * @param {String} [environment.url = "/taoDelivery/ResultDelivery/save"] the url of the server where the data are sent 
 * @param {Object} [environment.params] the additional parameters to send with the data
 * 
 * @param {Object} settings <i>set to null if you want to keep all the default values</i>
 * @param {String} [settings.format = "json"] the data format. <i>Only json is supported in the current version</i> 
 * @param {String} [settings.method = "post"] HTTP method to push the data <b>(get|post)</b>
 * @param {boolean}[settings.async = true]	if the request is asynchronous
 * @param {boolean}[settings.clearAfter= true]	if the variables stacks are cleared once the push is done
 */
function initPush(environment, settings){
}


/**
 * This method enables you to push the data to the server.
 * 
 * @function
 */
function push(){
}

/*
 * By default, the variables are pushed when the item is finished
 */
beforeFinish(push);



/////////////
// EVENTS  //
/////////////

/**
* Log the an <i>eventType</i> bound on <i>elementName</i> by sending the <i>data</i>.
* 
* @function
* @param {String} elementName an HTML tag name
* @param {String} eventType a JS User Events
* @param {mixed} data any data strucuture you want to trace
*/
function logEvent(elementName, eventType, data){
}

/**
* Log the a <i>eventName</i> by sending the <i>data</i>
* 
* @function
* @param {String} eventName the name of the custom event
* @param {mixed} data 
*/
function logCustomEvent(eventName, data){
}

function initEventServices(var1, var2) {
};
