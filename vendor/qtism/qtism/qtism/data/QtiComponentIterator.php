<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *   
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package 
 */


namespace qtism\data;

use \Iterator;
use \InvalidArgumentException;

/**
 * An Iterator that makes you able to loop on the QtiComponent objects
 * contained by a given QtiComponent object.
 * 
 * The following example demonstrates how QtiComponentIterator works:
 * 
 * <code>
 * $baseValues = new ExpressionCollection();
 * $baseValues[] = new BaseValue(BaseType::FLOAT, 0.5);
 * $baseValues[] = new BaseValue(BaseType::INTEGER, 25);
 * $baseValues[] = new BaseValue(BaseType::FLOAT, 0.5);
 *	
 * // Let's iterate on the components containted by a Sum object.
 * $iterator = new QtiComponentIterator(new Sum($baseValues));
		
 * $iterations = 0;
 * foreach ($iterator as $k => $i) {
 *    // $k contains the QTI class name of the component.
 *    // $i contains a reference to the component objec.
 *    var_dump($k, $i);
 * }
 * 
 * // Output is...
 * // string(9) "baseValue"
 * // float(0.5)
 * // string(9) "baseValue"
 * // int(25)
 * // string(9) "baseValue"
 * // float(0.5)
 * </code>
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class QtiComponentIterator implements Iterator {
	
	/**
	 * The QtiComponent object which contains the QtiComponent objects
	 * to be traversed.
	 * 
	 * @var QtiComponent
	 */
	private $rootComponent = null;
	
	/**
	 * The QtiComponent object being traversed.
	 * 
	 * @var QtiComponent
	 */
	private $currentComponent = null;
	
	/**
	 * Whether the iterator state is valid.
	 * 
	 * @var boolean
	 */
	private $isValid = true;
	
	/**
	 * A stack containing the QtiComponents to be traversed.
	 * 
	 * Each value in the trail is an array where:
	 * * index [0] contains the source of the trailing phase
	 * * index [1] contains the next QtiComponent object to traverse.
	 * 
	 * @var array
	 */
	private $trail = array();
	
	/**
	 * An array of already traversed QtiComponent objects. 
	 * 
	 * @var array
	 */
	private $traversed = array();
	
	/**
	 * The QtiComponent object which is the container of the QtiComponent object
	 * returned by QtiComponentIterator::current().
	 * 
	 * @var QtiComponent
	 */
	private $currentContainer = null;
	
	/**
	 * The QTI classes the Iterator must take into account.
	 * 
	 * @var array
	 */
	private $classes;
	
	/**
	 * The number of occurences in the trail.
	 * 
	 * @var integer
	 */
	private $trailCount = 0;
	
	/**
	 * Create a new QtiComponentIterator object.
	 * 
	 * @param QtiComponent $rootComponent The QtiComponent which contains the QtiComponent objects to be traversed.
	 */
	public function __construct(QtiComponent $rootComponent, array $classes = array()) {
		$this->setRootComponent($rootComponent);
		$this->setClasses($classes);
		$this->rewind();
	}
	
	/**
	 * Set the root QtiComponent. In other words, the QtiComponent which
	 * contains the QtiComponent objects to be traversed.
	 * 
	 * @param QtiComponent $component
	 */
	protected function setRootComponent(QtiComponent $rootComponent) {
		$this->rootComponent = $rootComponent;
	}
	
	protected function setCurrentContainer(QtiComponent $currentContainer = null) {
		$this->currentContainer = $currentContainer;
	}
	
	public function getCurrentContainer() {
		return $this->currentContainer;
	}
	
	/**
	 * Get the root QtiComponent. In other words, the QtiComponent which contains
	 * the QtiComponent objects to be traversed.
	 *
	 * @return QtiComponent
	 */
	public function getRootComponent() {
		return $this->rootComponent;
	}
	
	/**
	 * Set the currently traversed QtiComponent object.
	 * 
	 * @param QtiComponent $currentComponent
	 */
	protected function setCurrentComponent(QtiComponent $currentComponent = null) {
		$this->currentComponent = $currentComponent;
	}
	
	/**
	 * Get the currently traversed QtiComponent object.
	 * 
	 * @return QtiComponent A QtiComponent object.
	 */
	protected function getCurrentComponent() {
		return $this->currentComponent;
	}
	
	/**
	 * Set the QTI classes the Iterator must take into account.
	 * 
	 * @param array $classes An array of QTI class names.
	 */
	protected function setClasses(array $classes) {
	    $this->classes = $classes;
	}
	
	/**
	 * Get the QTI classes the Iterator must take into account.
	 * 
	 * @return array An array of QTI class names.
	 */
	protected function &getClasses() {
	    return $this->classes;
	}
	
	/**
	 * Push a trail entry on the trail.
	 * 
	 * @param QTIComponent $source From where we are coming from.
	 * @param QTIComponentCollection $components The next components to explore.
	 */
	protected function pushOnTrail(QtiComponent $source, QtiComponentCollection $components) {
		
		foreach (array_reverse($components->getArrayCopy()) as $c) {
			array_push($this->trail, array($source, $c));
			$this->trailCount++;
		}
	}
	
	/**
	 * Pop a trail entry from the trail.
	 * 
	 * @return array 
	 */
	protected function popFromTrail() {
	    $this->trailCount--;
		return array_pop($this->trail);
	}
	
	/**
	 * Get a reference on the trail array.
	 * 
	 * @return array An array of QtiComponent objects.
	 */
	protected function &getTrail() {
		return $this->trail;
	}
	
	/**
	 * Set the trail array.
	 * 
	 * @param array $trail An array of QtiComponent objects.
	 */
	protected function setTrail(array &$trail) {
		$this->trail = $trail;
		$this->trailCount = count($trail);
	}
	
	/**
	 * Set the array of QtiComponents which contains the already traversed
	 * components.
	 * 
	 * @param array $traversed An array of QtiComponent objects.
	 */
	protected function setTraversed(array &$traversed) {
		$this->traversed = $traversed;
	}
	
	/**
	 * Get a reference on the array of QtiComponents which contains the already
	 * traversed components.
	 * 
	 * @return array An array of QtiComponent objects.
	 */
	protected function &getTraversed() {
		return $this->traversed;
	}
	
	/**
	 * Mark a QTIComponent object as traversed.
	 * 
	 * @param QtiComponent $component A QTIComponent object.
	 */
	protected function markTraversed(QtiComponent $component) {
		array_push($this->traversed, $component);
	}
	
	protected function isTraversed(QtiComponent $component) {
		return in_array($component, $this->traversed, true);
	}
	
	/**
	 * Indicate Whether the iterator is still valid.
	 * 
	 * @param boolean $isValid
	 */
	protected function setValid($isValid) {
		$this->isValid = $isValid;
	}
	
	/**
	 * Rewind the iterator.
	 */
	public function rewind() {
		$trail = array();
		$this->setTrail($trail);
		$classes = &$this->getClasses();
		
		$traversed = array();
		$this->setTraversed($traversed);
		
		$root = $this->getRootComponent();
		$this->pushOnTrail($root, $root->getComponents());
		
		while(count($this->getTrail()) > 0) {
			$trailEntry = $this->popFromTrail();
			
			$this->setValid(true);
			$this->setCurrentComponent($trailEntry[1]);
			$this->setCurrentContainer($trailEntry[0]);
			$this->markTraversed($this->getCurrentComponent());
			$this->pushOnTrail($this->getCurrentComponent(), $this->getCurrentComponent()->getComponents());
			
			if (empty($classes) === true || in_array($this->getCurrentComponent()->getQtiClassName(), $classes) === true) {
			    break;
			}
		}
		
		if (count($this->getTrail()) === 0) {
		    $this->setValid(false);
		    $this->setCurrentComponent(null);
		    $this->setCurrentContainer(null);
		}
	}
	
	/**
	 * Get the current QtiComponent object the iterator
	 * is traversing.
	 * 
	 * @return QtiComponent A QtiComponent object.
	 */
	public function current() {
		return $this->getCurrentComponent();
	}
	
	/**
	 * Get the parent component of the one given by
	 * the QtiComponentIterator::current() method.
	 * 
	 * This method will return the null value in the following circumstances:
	 * 
	 * * The QtiComponentIterator::valid method returns false.
	 * * The component returned by QtiComponentIterator::current is the root component.
	 * 
	 * @return null|QtiComponent The null value if there is no parent, otherwise a QtiComponent.
	 * @see QtiComponentIterator::current()
	 */
	public function parent() {
	    return $this->getCurrentContainer();
	}
	
	/**
	 * Get the key of the current QtiComponent. The value of the key is actually
	 * its QTI class name e.g. 'assessmentTest', 'assessmentItemRef', ...
	 * 
	 * @return string A QTI class name.
	 */
	public function key() {
		return $this->getCurrentComponent()->getQtiClassName();
	}
	
	/**
	 * Moves the current position to the next QtiComponent object to be
	 * traversed.
	 */
	public function next() {
		
		if ($this->trailCount > 0) {
			
			while($this->trailCount > 0) {
				$trailEntry = $this->popFromTrail();
				$component = $trailEntry[1];
				$source = $trailEntry[0];
				
				if ($this->isTraversed($component) === false) {
					$this->currentComponent = $component;
					$this->currentContainer = $source;
					$this->pushOnTrail($component, $this->currentComponent->getComponents());
					$this->markTraversed($this->currentComponent);
					
					if (empty($this->classes) === true || in_array($component->getQTIClassName(), $this->classes) === true) {
					    // If all classes are seeked or the current component has a class name
					    // that must be seeked, stop the iteration.
					    return;
					}
				}
			}
			
			$this->isValid = false;
			$this->currentContainer = null;
		}
		else {
			$this->isValid = false;
			$this->currentContainer = null;
		}
	}
	
	/**
	 * Checks if current position is valid.
	 * 
	 * @return boolean Whether the current position is valid.
	 */
	public function valid() {
		return $this->isValid;
	}
}
