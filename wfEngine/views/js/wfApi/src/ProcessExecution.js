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
 * @namespace wfApi.ProcessExecution
 * 
 * This file provide functions to drive the processes execution from a service
 * 
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @version 0.2
 */

  //////////////////////////////////////
 // WF Process Execution Controls    //
//////////////////////////////////////


/**  
 * @namespace wfApi.ProcessExecution
 */
wfApi.ProcessExecution = {};

/**
 * Delete a process execution
 * @param {String} processExecutionUri The process execution to delete
 */
wfApi.ProcessExecution.remove = function(processExecutionUri, successCallback, errorCallback, options)
{
	wfApi.request(wfApi.ProcessExecutionControler, 'delete', {processExecutionUri:processExecutionUri}, successCallback, errorCallback, options);
};

/**
 * Pause a process execution 
 * @param {String} processExecutionUri The process execution to pause
 */
wfApi.ProcessExecution.pause = function(processExecutionUri, successCallback, errorCallback, options)
{
	wfApi.request(wfApi.ProcessExecutionControler, 'pause', {processExecutionUri:processExecutionUri}, successCallback, errorCallback, options);
};

/**
 * Cancel a process execution
 * @param {String} processExecutionUri The process execution to cancel
 */
wfApi.ProcessExecution.cancel = function(processExecutionUri, successCallback, errorCallback, options)
{
	wfApi.request(wfApi.ProcessExecutionControler, 'cancel', {processExecutionUri:processExecutionUri}, successCallback, errorCallback, options);
};

/**
 * Resule a process execution
 * @param {String} processExecutionUri The process execution to resume
 */
wfApi.ProcessExecution.resume = function(processExecutionUri, successCallback, errorCallback, options)
{
	wfApi.request(wfApi.ProcessExecutionControler, 'resume', {processExecutionUri:processExecutionUri}, successCallback, errorCallback, options);
};

/**
 * Drive the process execution to the next activity
 * @param {String} processExecutionUri The process execution to drive
 */
wfApi.ProcessExecution.next = function(processExecutionUri, successCallback, errorCallback, options)
{
	wfApi.request(wfApi.ProcessExecutionControler, 'next', {processExecutionUri:processExecutionUri}, successCallback, errorCallback, options);
};

/**
 * Drive the process execution to the previous activity
 * @param {String} processExecutionUri The process execution to drive
 */
wfApi.ProcessExecution.previous = function(processExecutionUri, successCallback, errorCallback, options)
{
	wfApi.request(wfApi.ProcessExecutionControler, 'previous', {processExecutionUri:processExecutionUri}, successCallback, errorCallback, options);
};
