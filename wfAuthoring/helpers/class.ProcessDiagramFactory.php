<?php

/**
 * 
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
 * Factory for creating Process Disagrams
 *
 * @access public
 * @author Joel Bout
 * @package wfAuthoring
 
 */
class wfAuthoring_helpers_ProcessDiagramFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Builds a simple Diagram of the Process
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @return string
     */
    public static function buildDiagramData( core_kernel_classes_Resource $process)
    {
        $returnValue = (string) '';

        
		
		common_Logger::i("Building diagram for ".$process->getLabel());
		
        $authoringService = wfAuthoring_models_classes_ProcessService::singleton();
		$activityService = wfEngine_models_classes_ActivityService::singleton();
		$connectorService = wfEngine_models_classes_ConnectorService::singleton();
		$activityCardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
		
		$activities = $authoringService->getActivitiesByProcess($process);
		
		$todo = array();
		foreach ($activities as $activity) {
			if ($activityService->isInitial($activity)) {
				$todo[] = $activity;
			}
		}
		
		$currentLevel = 0;
		$diagram = new wfAuthoring_models_classes_ProcessDiagram();
		$done = array();
		while (!empty($todo)) {
		
			$nextLevel = array();
			$posOnLevel = 0;
			foreach ($todo as $item) {
				
				$next = array();
				
				if ($activityService->isActivity($item)) {
					// add this activity
					$diagram->addActivity($item, 54 + (200 * $posOnLevel) + (10*$currentLevel), 35 + (80 * $currentLevel));
					$next = array_merge($next, $activityService->getNextConnectors($item));
					common_Logger::d('Activity added '.$item->getUri());
				} elseif ($connectorService->isConnector($item)) {
					// add this connector
					$diagram->addConnector($item, 100 + (200 * $posOnLevel) + (10*$currentLevel), 40 + (80 * $currentLevel));
					$next = array_merge($next,$connectorService->getNextActivities($item));
				} else {
					common_Logger::w('unexpected ressource in process '.$item->getUri());
				}
				
				//replace cardinalities
				foreach ($next as $key => $destination) {
					if ($activityCardinalityService->isCardinality($destination)) {
						// not represented on diagram
						$next[$key] = $activityCardinalityService->getDestination($destination);
					}
				}	
				
				//add arrows
				foreach ($next as $destination) {
					$diagram->addArrow($item, $destination);
				}
		
				$posOnLevel++;
				$nextLevel = array_merge($nextLevel, $next);
			}
			$done = array_merge($done, $todo);
			$todo = array_diff($nextLevel, $done);
			$currentLevel++;
		}
		$returnValue = $diagram->toJSON();
        

        return (string) $returnValue;
    }

} /* end of class wfAuthoring_helpers_ProcessDiagramFactory */

?>