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
function ItemApi() {
    this.implementation = null;
    this.pendingCalls = new Array();
}

ItemApi.prototype.setImplementation = function(implementation) {
    this.implementation = implementation;
    for ( var i = 0; i < this.pendingCalls.length; i++) {
        this.pendingCalls[i](implementation);
    }
    ;
    this.pendingCalls = new Array();
};

ItemApi.prototype.__delegate = function(call) {
    if (this.implementation != null) {
        return call(this.implementation);
    } else {
        this.pendingCalls.push(function(implementation) {
            return call(implementation);
        });
    }
};

// interface to implement

ItemApi.prototype.saveResponses = function(valueArray) {
    this.__delegate((function(valueArray) {
        return function(implementation) {
            implementation.saveResponses(valueArray);
        }
    })(valueArray));
};

ItemApi.prototype.getUserPropertyValues = function(property, callback) {
    this.__delegate((function(property, callback) {
        return function(property, implementation) {
            implementation.getUserPropertyValues(property, callback);
        }
    })(callback));
};

// Scoring
ItemApi.prototype.saveScores = function(valueArray) {
    this.__delegate((function(valueArray) {
        return function(implementation) {
            implementation.saveScores(valueArray);
        }
    })(valueArray));
};

ItemApi.prototype.traceEvents = function(eventArray) {
    this.__delegate((function(eventArray) {
        return function(implementation) {
            implementation.traceEvents(eventArray);
        }
    })(eventArray));
};

// Flow
ItemApi.prototype.beforeFinish = function(callback) {
    this.__delegate((function(callback) {
        return function(implementation) {
            implementation.beforeFinish(callback);
        }
    })(callback));
};

ItemApi.prototype.finish = function() {
    this.__delegate(function(implementation) {
        implementation.finish();
    });
};

ItemApi.prototype.onKill = function(callback) {
    this.__delegate(function(implementation) {
        implementation.onKill(callback);
    });
}

// Runtime variables, will not be submited to result service
ItemApi.prototype.setVariable = function(identifier, value) {
    this.__delegate((function(identifier, value) {
        return function(implementation) {
            implementation.setVariable(identifier, value);
        }
    })(identifier, value));
};

ItemApi.prototype.getVariable = function(identifier, callback) {
    this.__delegate((function(identifier, callback) {
        return function(implementation) {
            implementation.getVariable(identifier, callback);
        }
    })(identifier, callback));
};

// Submission of data.
ItemApi.prototype.submit = function(callback) {
    this.__delegate((function(identifier, callback) {
        return function(implementation) {
            implementation.submit(callback);
        }
    })(callback));
};