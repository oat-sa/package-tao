<?php
/**
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
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 *  
 *
 */
namespace qtism\runtime\tests;

class BasicOrdering extends AbstractOrdering {
    
    public function order() {
        
        if (($ordering = $this->getAssessmentSection()->getOrdering()) !== null && $ordering->getShuffle() === true) {
            
            // $orderedRoutes will contain the result of the ordering algorithm.
            $orderedRoutes = new SelectableRoute();
            $selectableRoutes = $this->getSelectableRoutes();
            $selectableRoutesCount = count($selectableRoutes);
            
            // What are the child elements that can be shuffled?
            $shufflingIndexes = array();
            for ($index = 0; $index < $selectableRoutesCount; $index++) {
                $selectableRoute = $selectableRoutes[$index];
                
                if ($selectableRoute->isVisible() === false && $selectableRoute->mustKeepTogether() === false) {
                    $oldIndex = $index;
                    // The RouteItems in the Route must be merged
                    // with the parent's one.
                    unset($selectableRoutes[$index]);
                    
                    // Split the current selection in multiple selections.
                    foreach ($selectableRoute as $routeItem) {
                        $item = $routeItem->getAssessmentItemRef();
                        $newRoute = new SelectableRoute($item->isFixed(), $item->isRequired(), true, true);
                        $newRoute->addRouteItem($routeItem->getAssessmentItemRef(), $routeItem->getAssessmentSection(), $routeItem->getTestPart(), $routeItem->getAssessmentTest());
                        $selectableRoutes->insertAt($newRoute, $index);
                        $index++;
                    }
                    
                    // reload...
                    $index = $oldIndex;
                    $selectableRoutesCount = count($selectableRoutes);
                    $selectableRoute = $selectableRoutes[$index];
                }
                
                if ($selectableRoute->isFixed() === false) {
                    $shufflingIndexes[] = $index;
                }
            }
            
            $shufflingIndexesCount = count($shufflingIndexes);
            
            $shufflingMaxIndex = $shufflingIndexesCount - 1;
            
            // Let's swap 2 routes together N times where N is the amount of 'shufflable' routes.
            // (A 'shufflable' route is a route wich is not 'fixed'.
            for ($i = 0; $i < $shufflingIndexesCount; $i++) {
                $swapIndex1 = $shufflingIndexes[mt_rand(0, $shufflingMaxIndex)];
                $swapIndex2 = $shufflingIndexes[mt_rand(0, $shufflingMaxIndex)];
            
                $selectableRoutes->swap($swapIndex1, $swapIndex2);
            }
            
            return $selectableRoutes;
        }
        else {
            // Simple return as it is...
            return $this->getSelectableRoutes();
        }
    }
}