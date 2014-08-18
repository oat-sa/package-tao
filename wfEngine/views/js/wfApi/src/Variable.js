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
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @subpackage views
 * @namespace wfApi.Variable
 * 
 * This file provide functions to drive the variables from a service
 * 
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @version 0.2
 */

  //////////////////////////////
 // WF Variables Controls    //
//////////////////////////////


/**  
 * @namespace wfApi.Variable
 */
wfApi.Variable = {};

/**
 * Get the variable's value
 * @param {String} activityExecutionUri The target activity exectuion 
 * @param {String} code The code of the variable to get
 */
wfApi.Variable.get = function(activityExecutionUri, code, successCallback, errorCallback, options)
{
	return wfApi.request(wfApi.VariableControler, 'get', {activityExecutionUri:activityExecutionUri, code:code}, successCallback, errorCallback, options);
};

/**
 * Push a variable
 * @param {String} activityExecutionUri The target activity exectuion 
 * @param {String} code The code of the variable to push
 * @param {String} value The value of the variable to push
 */
wfApi.Variable.push = function(activityExecutionUri, code, value, successCallback, errorCallback, options)
{
	return wfApi.request(wfApi.VariableControler, 'push', {activityExecutionUri:activityExecutionUri, code:code, value:value}, successCallback, errorCallback, options);
};

/**
 * Set the variable's value
 * @param {String} activityExecutionUri The target activity exectuion 
 * @param {String} code The code of the variable to set
 * @param {String} value The value of the variable to set
 */
wfApi.Variable.edit = function(activityExecutionUri, code, value, successCallback, errorCallback, options)
{
	return wfApi.request(wfApi.VariableControler, 'edit', {activityExecutionUri:activityExecutionUri, code:code, value:value}, successCallback, errorCallback, options);
};

/**
 * Remove a variable
 * @param {String} activityExecutionUri The target activity exectuion 
 * @param {String} code The code of the variable to remove
 */
wfApi.Variable.remove = function(activityExecutionUri, code, successCallback, errorCallback, options)
{
	return wfApi.request(wfApi.VariableControler, 'remove', {activityExecutionUri:activityExecutionUri, code:code}, successCallback, errorCallback, options);
};
