/**
 * The ConsoleItemApi is a basic implementation of the Item API you can use during your development
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */

/**
 * @constructor
 * @augments ItemApi
 * @returns {ConsoleItemApi}
 */
function ConsoleItemApi() {
    this.responses = {};
    this.scores = {};
    this.store = {};
    this.before = [];
};

/**
 * Connect the API to the onItemApiReady function
 */
ConsoleItemApi.prototype.connect = function(){
    var ready = window.onItemApiReady;
    if (typeof(ready) === "function") {
        ready(this);
    }
};

/**
 * Save test taker's responses
 * @param {Object} responses
 */
ConsoleItemApi.prototype.saveResponses = function(responses){
    for(var key in responses){
        this.responses[key] = responses[key];
    }
};

/**
 * Log events
 * @param {Object} events
 */
ConsoleItemApi.prototype.traceEvents = function(events){
    for(var key in events){
        console.log("Event [%s] : %s", key, events[key]);
    }
};

/**
 * Add a callback to be executed in stack before the finish
 * @param {function} callback
 */
ConsoleItemApi.prototype.beforeFinish = function(callback){
    if(typeof callback === 'function'){
	this.before.push(callback);
    }
};

/**
 * Save test taker's scores
 * @param {Object} scores
 */
ConsoleItemApi.prototype.saveScores = function(scores){
    for (var key in scores) {
        this.scores[key] = scores[key];
    }
};

/**
 * Store variable (not persistant)
 * @param {string} key
 * @param {string|number|Object|Array} value
 */
ConsoleItemApi.prototype.setVariable = function(key, value){
    this.store[key] = value;
};

/**
 * Get a stored variable
 * @param {string} key
 * @param {function} callback - as callback(value)
 */
ConsoleItemApi.prototype.getVariable = function(key, callback){
     if(typeof callback === 'function'){
        return callback(this.store[key]);
     }
     return this.store[key];
};

/**
 * Flag the item as finish 
 */
ConsoleItemApi.prototype.finish = function(){
    this.before.forEach(function(callback){
        if (typeof callback === 'function') {
            return callback();
        }
    });
    console.log("Responses : %s",  JSON.stringify(this.responses));
    console.log("Scores : %s",  JSON.stringify(this.scores));
};