<?php
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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

/**
 *  Montitor Controler provide actions to manage processes
 *
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @package wfEngine
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class wfAuthoring_actions_Monitor extends tao_actions_TaoModule {

	protected $variableService = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->processMonitoringGridOptions = array(
			'columns' => array(
				RDFS_LABEL 												=> array('weight'=>3)
				, PROPERTY_PROCESSINSTANCES_EXECUTIONOF 				=> array('weight'=>2)
				, PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS	=> array(
					'weight'=>6
					, 'widget'=>'CurrentActivities'
					, 'columns' => array(
						'variables' => array(
							'widget'=>'ActivityVariables'
							, 'columns' => array(
								'value' => array('weight'=>3, 'widget'=>'ActivityVariable')
							)
						)
					)
				)
			)
		);

		$this->variableService = wfEngine_models_classes_VariableService::singleton();
	}

	/**
	 *
	 */
	public function getRootClass()
	{
		return null;
	}

	/**
	 * The monitoring front page
	 * -> Display current process status
	 * -> Display current activities status
	 * -> Display activities history
	 */
	public function index()
	{
		//Class to filter on
		$clazz = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);

		//Properties to filter on
		$properties = array();
		$properties[] = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_EXECUTIONOF);
		$properties[] = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_STATUS);

		//Monitoring grid
		$processMonitoringGrid = new wfAuthoring_helpers_Monitoring_ProcessMonitoringGrid(array(), $this->processMonitoringGridOptions);
		$grid = $processMonitoringGrid->getGrid();
		$model = $grid->getColumnsModel();

		//Process history grid
		$processHistoryGrid = new wfAuthoring_helpers_Monitoring_ExecutionHistoryGrid(new core_kernel_classes_Resource(' '), array(
			'columns' => array(
				'variables' => array(
					'widget'=>'ActivityVariables'
					, 'columns' => array(
						'value' => array('weight'=>3, 'widget'=>'ActivityVariable')
					)
				)
			)
		));
		$historyProcessModel = $processHistoryGrid->getGrid()->getColumnsModel();

		//Filtering data
		$this->setData('clazz', $clazz);
		$this->setData('properties', $properties);

		//Monitoring data
		$this->setData('model', json_encode($model));
		$this->setData('historyProcessModel', json_encode($historyProcessModel));
		$this->setData('data', $processMonitoringGrid->toArray());

		//WF Variables
		$this->setData('wfVariables', json_encode($this->variableService->getAllVariables()));

		$this->setView('monitor/index.tpl');
	}

	/**
	 * Get JSON monitoring data
	 */
	public function monitorProcess()
	{   
		$filters = array();
		if($this->hasRequestParameter('filter')){
		$filters = $this->getFilterState('filter');
		}

		//get the processes uris
		$processesUri = $this->hasRequestParameter('processesUri') ? $this->getRequestParameter('processesUri') : null;

		$processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		if (!is_null($filters)) {
			$processExecutions = $processInstancesClass->searchInstances($filters, array ('recursive'=>true));
		} else if(!is_null($processesUri)) {
			foreach ($processesUri as $processUri) {
				$processExecutions[$processUri] = new core_kernel_classes_resource($processUri);
			}
		} else {
			$processExecutions = $processInstancesClass->getInstances();
		}

		$processMonitoringGrid = new wfAuthoring_helpers_Monitoring_ProcessMonitoringGrid(array_keys($processExecutions), $this->processMonitoringGridOptions);
		$data = $processMonitoringGrid->toArray();

		echo json_encode($data);
	}

	/**
	 * Get JSON activity history
	 */
	public function processHistory()
	{
		if($this->hasRequestParameter('uri')){
			$uri = $this->getRequestParameter('uri');
		}

		$processMonitoringGrid = new wfAuthoring_helpers_Monitoring_ExecutionHistoryGrid(new core_kernel_classes_Resource($uri));
		$data = $processMonitoringGrid->toArray();

		echo json_encode($data);
	}
}
