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
 * @namespace wfApi.ProcessDefinition
 * 
 * This file provide functions to drive the processes definition from a service
 * 
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @version 0.2
 */

  //////////////////////////////////////
 // WF Process Definition Controls    //
//////////////////////////////////////


/**  
 * @namespace wfApi.ProcessDefinition
 */
wfApi.ProcessDefinition = {};

/**
 * 
 * @param {String}
 */
wfApi.ProcessDefinition.initExecution = function(processDefinitionUri, successCallback, errorCallback)
{
	wfApi.request(wfApi.ProcessDefinitionControler, 'initExecution', {processDefinitionUri:processDefinitionUri}, successCallback, errorCallback);
};

/**
 * 
 * @param {String}
 */
wfApi.ProcessDefinition.getName = function(processDefinitionUri, successCallback, errorCallback)
{
	wfApi.request(wfApi.ProcessDefinitionControler, 'getName', {processDefinitionUri:processDefinitionUri}, successCallback, errorCallback);
};
