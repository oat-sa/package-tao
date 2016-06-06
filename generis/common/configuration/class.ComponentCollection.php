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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class common_configuration_ComponentCollection
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class common_configuration_ComponentCollection
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The components that have to be checked.
     *
     * @access private
     * @var array
     */
    private $components = array();

    /**
     * An array of arrays. The arrays contained in this field are associative
     * with the following keys: 'component' is the component on which other
     * have dependencies. 'depends' is an array containing components that have
     * dependency on 'component'.
     *
     * @access private
     * @var array
     */
    private $dependencies = array();

    /**
     * Short description of attribute checkedComponents
     *
     * @access private
     * @var array
     */
    private $checkedComponents = array();

    /**
     * Short description of attribute reports
     *
     * @access public
     * @var array
     */
    public $reports = array();

    /**
     * Short description of attribute silentComponents
     *
     * @access private
     * @var array
     */
    private $silentComponents = array();

    /**
     * Short description of attribute rootComponent
     *
     * @access private
     * @var Component
     */
    private $rootComponent = null;

    // --- OPERATIONS ---

    /**
     * Short description of method addComponent
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Component component
     * @return void
     */
    public function addComponent( common_configuration_Component $component)
    {
        
        $components = $this->getComponents();
        
        // Search for a similar...
        foreach ($components as $c){
        	if ($c === $component){
        		// Already stored.
        		return;
        	}
        }
        
        // Not stored yet.
        $components[] = $component;
        $this->setComponents($components);
        
        
    }

    /**
     * Short description of method addDependency
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Component component
     * @param  Component dependency
     * @return void
     */
    public function addDependency( common_configuration_Component $component,  common_configuration_Component $dependency)
    {
        
        $dependencies = $this->getDependencies();
        
    	$found = false;
        foreach ($dependencies as $dep){
        	if ($dependency === $dep['component'] && $component === $dep['isDependencyOf']){
        		$found = true;
        		break;
        	}
        }
    	
        if (false == $found){
        	$dependencies[] = array('component' => $dependency, 'isDependencyOf' => $component);
        	$this->setDependencies($dependencies);
        }
        
    }

    /**
     * Short description of method reset
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function reset()
    {
        
        $this->setComponents(array());
        $this->setDependencies(array());
        $this->setCheckedComponents(array());
        $this->setSilentComponents(array());
        
    }

    /**
     * Returns an array of Reports.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function check()
    {
        $returnValue = array();

        
        // Reset what should be reset for another check on the same instance.
        $this->setCheckedComponents(array());
        $this->setReports(array());
        
		$components = $this->getComponents();
		$dependencies = $this->getDependencies();
		$traversed = array();
		
		// Any node that has no incoming edge and is not
		// the root mock should be bound to it.
		foreach ($components as $c){
			$found = false;
			foreach ($this->getDependencies() as $d){
				
				if ($c === $d['isDependencyOf']){
					// Incoming edge(s).
					$found = true;
					break;
				}
			}
			
			// No incoming edge.
			if ($found === false && $c !== $this->getRootComponent()){
				$this->addDependency($c, $this->getRootComponent());
			}
		}
        
		if (count($components) > 0){
	        if (true == $this->isAcyclic()){
	        	
	        	// We go for a Depth First Search in the graph.
	        	$stack = array();
	        	$node = $components[0];
	        	
	        	// Do something with my node.
	        	$status = $this->checkComponent($node);
	        	array_push($traversed, $node); // mark the node as 'traversed'.
	        	
	        	if ($status == common_configuration_Report::VALID){
		        	$stack = self::pushTransitionsOnStack($stack, $this->getTransitions($node)); // put all transitions from the node to stack.
		        	
		   			while (count($stack) > 0){
		   				$transition = array_pop($stack);
		   				$node = $transition['isDependencyOf'];
		   				
		   				// If not already traversed, do something with my node.
		   				if (false == in_array($node, $traversed)){
		   					
		   					// Do something with my node.
		   					$status = $this->checkComponent($node);
		   					array_push($traversed, $node);
		   					
		   					if ($status == common_configuration_Report::VALID){
		   						$stack = self::pushTransitionsOnStack($stack, $this->getTransitions($node));
		   					}
		   				}
		   			}
	        	}
	        	
	        	$returnValue = $this->getReports();
	        }
	        else{
	        	throw new common_configuration_CyclicDependencyException("The dependency graph is cyclic. Please review your dependencies.");
	        }
		}
        
        

        return (array) $returnValue;
    }

    /**
     * Short description of method isAcyclic
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    private function isAcyclic()
    {
        $returnValue = (bool) false;

        
        
        // To detect if the dependency graph is acyclic or not,
    	// we first perform a usual Topological Sorting algorithm.
    	// If at the end of the algorith, we still have edges,
    	// the graph is cyclic !
    	
    	
    	$l = array(); // Empty list where elements are sorted.
    	$q = array(); // Set of nodes with no incoming edges.
    	
    	$components = $this->getComponents();
    	$dependencies = $this->getDependencies(); // used as a copy !
    	
    	// Set q with nodes with no incoming edges.
    	foreach ($components as $c){
    		$incomingEdges = false;
    		
    		foreach ($dependencies as $d){
    			if ($c === $d['isDependencyOf']){
    				// $c has incoming edges thus we reject it.
    				$incomingEdges = true;
    				break;
    			}
    		}
    		
    		if ($incomingEdges == false){
    			array_push($q, $c);
    		}
    	}
    	
    	while (count($q) > 0){
    		$n = array_pop($q);
    		array_push($l, $n);
    		
    		foreach ($components as $m){
    			// edge from n to m ?
    			foreach ($dependencies as $k => $dep){
    				if ($dep['component'] === $n && $dep['isDependencyOf'] === $m){
    					unset($dependencies[$k]);
    					
    					// other incoming edges for m ?
    					foreach ($dependencies as $dep){
    						if ($dep['isDependencyOf'] === $m){
    							break 2;
    						}
    					}
    					
    					// no incoming edges from m !
    					array_push($q, $m);
    				}
    			}
    		}
    	}
    	
    	$returnValue = count($dependencies) == 0;
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getTransitions
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Component component
     * @return array
     */
    private function getTransitions( common_configuration_Component $component)
    {
        $returnValue = array();

        
    	$dependencies = $this->dependencies;
    	foreach($dependencies as $d){
    		if ($d['component'] === $component){
    			array_push($returnValue, $d);
    		}
    	}
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getCheckedComponents
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getCheckedComponents()
    {
        $returnValue = array();

        
    	// Sort the checked components to make them ordered in the same
        // way the related components where added.
        $components = $this->getComponents();
        $checkedComponents = array();
        foreach ($components as $c){
        	foreach ($this->checkedComponents as $cC){
        		if ($cC === $c){
        			array_push($checkedComponents, $cC);
        		}
        	}
        }
        
        
        $returnValue = $checkedComponents;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getUncheckedComponents
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getUncheckedComponents()
    {
        $returnValue = array();

        
        $rootMock = $this->getRootComponent();
    	foreach($this->getComponents() as $c){
    		if (false === in_array($c, $this->getCheckedComponents()) && $c !== $rootMock){
    			array_push($returnValue, $c);
    		}
    	}
    	
    	// Sort the checked components to make them ordered in the same
        // way the related components where added.
        $components = $this->getComponents();
        $uncheckedComponents = array();
        foreach ($components as $c){
        	foreach ($returnValue as $uC){
        		if ($uC === $c){
        			array_push($uncheckedComponents, $uC);
        		}
        	}
        }
        
        $returnValue = $uncheckedComponents;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method pushTransitionsOnStack
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array stack
     * @param  array transitions
     * @return array
     */
    public static function pushTransitionsOnStack($stack, $transitions)
    {
        $returnValue = array();

        
    	foreach ($transitions as $t){
    		array_push($stack, $t);
    	}
    	
    	$returnValue = $stack;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method setComponents
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array components
     * @return void
     */
    public function setComponents($components)
    {
        
        $this->components = $components;
        
    }

    /**
     * Short description of method getComponents
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getComponents()
    {
        $returnValue = array();

        
        $returnValue = $this->components;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method setCheckedComponents
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array checkedComponents
     * @return void
     */
    private function setCheckedComponents($checkedComponents)
    {
        
        $this->checkedComponents = $checkedComponents;
        
    }

    /**
     * Short description of method setDependencies
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array dependencies
     * @return void
     */
    private function setDependencies($dependencies)
    {
        
        $this->dependencies = $dependencies;
        
    }

    /**
     * Short description of method getDependencies
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    private function getDependencies()
    {
        $returnValue = array();

        
        $returnValue = $this->dependencies;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method setReports
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array reports
     * @return mixed
     */
    private function setReports($reports)
    {
        
        $this->reports = $reports;
        
    }

    /**
     * Short description of method getReports
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getReports()
    {
        $returnValue = array();

        
        if (count($this->reports) == 0){
        	return $returnValue;
        }
        else{
        	// Sort the reports to make them ordered in the same
        	// order the related components where added.
        	$components = $this->getComponents();
        	$reports = array();
        	foreach ($components as $c){
        		foreach ($this->reports as $r){
        			if ($r->getComponent() === $c){
        				array_push($reports, $r);
        			}
        		}
        	}
        }
        
        $returnValue = $reports;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method addReport
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Report report
     * @return void
     */
    private function addReport( common_configuration_Report $report)
    {
        
        array_push($this->reports, $report);
        
    }

    /**
     * Short description of method componentChecked
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Component component
     * @return void
     */
    private function componentChecked( common_configuration_Component $component)
    {
        
        if ($component !== $this->getRootComponent()){
        	array_push($this->checkedComponents, $component);
        }
        
    }

    /**
     * Short description of method checkComponent
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Component component
     * @return int
     */
    private function checkComponent( common_configuration_Component $component)
    {
        $returnValue = (int) 0;

        
        $report = $component->check(); // Check the node.
	    $this->componentChecked($component); // Mark the node as 'checked'.
	    
	    // Store the report if not silenced.
	    if (false == $this->isSilent($component)){
	    	$this->addReport($report); // Store the report.
	    }
	    
	    $returnValue = $report->getStatus();
        

        return (int) $returnValue;
    }

    /**
     * Short description of method getSilentComponents
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getSilentComponents()
    {
        $returnValue = array();

        
        $returnValue = $this->silentComponents;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method setSilentComponents
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array silentComponents
     * @return void
     */
    private function setSilentComponents($silentComponents)
    {
        
        $this->silentComponents = $silentComponents;
        
    }

    /**
     * Short description of method silent
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Component component
     * @return void
     */
    public function silent( common_configuration_Component $component)
    {
        
        $silentComponents = $this->getSilentComponents();
        foreach ($silentComponents as $silent){
        	if ($silent === $component){
        		return;
        	}
        }
        
        $silentComponents[] = $component;
        $this->setSilentComponents($silentComponents);
        
    }

    /**
     * Short description of method noisy
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Component component
     * @return void
     */
    public function noisy( common_configuration_Component $component)
    {
        
        $silentComponents = $this->getSilentComponents();
        
        foreach ($silentComponents as $k => $silent){
        	if ($silent === $component){
        		unset($silentComponents[$k]);
        	}
        }
        
        $this->setSilentComponents($silentComponents);
        
    }

    /**
     * Short description of method isSilent
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Component component
     * @return boolean
     */
    private function isSilent( common_configuration_Component $component)
    {
        $returnValue = (bool) false;

        
        $returnValue = in_array($component, $this->getSilentComponents());
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method isNoisy
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Component component
     * @return boolean
     */
    private function isNoisy( common_configuration_Component $component)
    {
        $returnValue = (bool) false;

        
        $returnValue = !in_array($component, $this->getSilentComponents());
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        
        
        // A mock root check on which any added component has a dependence. The goal
        // of this is to make sure that components will not stay alone with no
        // incoming edges in the dependency graph, making them unreachable.
        $rootStatus = common_configuration_Report::VALID;
        $root = new common_configuration_Mock($rootStatus, 'tao.dependencies.root');
        $this->setRootComponent($root);
    	
        
    }

    /**
     * Short description of method setRootComponent
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Component component
     * @return void
     */
    private function setRootComponent( common_configuration_Component $component)
    {
        
        $this->rootComponent = $component;
        $components = $this->getComponents();
        $components[] = $component;
        $this->setComponents($components);
        $this->silent($component);
        
    }

    /**
     * Short description of method getRootComponent
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_configuration_Component
     */
    private function getRootComponent()
    {
        $returnValue = null;

        
        $returnValue = $this->rootComponent;
        

        return $returnValue;
    }

}