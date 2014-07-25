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
 * @subpackage 
 *
 */
namespace qtism\runtime\storage\common;

use qtism\data\QtiComponentIterator;
use qtism\data\QtiComponent;
use qtism\data\AssessmentTest;
use \OutOfBoundsException;

/**
 * The AssessmentTestSeeker enables you to search for QTIComponent objects
 * by position in the tree formed by an AssessmentTest object.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentTestSeeker {
    
    /**
     * An array that counts the number of explored components
     * by QTI class name.
     * 
     * @var array
     */
    private $classCounter;
    
    /**
     * An array that stores the already explored components by QTI class name.
     * 
     * @var array
     */
    private $componentStore;
    
    /**
     * A QtiComponent iterator.
     * 
     * @var QtiComponentIterator
     */
    private $iterator;
    
    /**
     * Create an AssessmentTestSeeker object that will be able to search
     * for QTIComponent objects in an AssessmentTest that matches the QTI class
     * names described by $classes.
     * 
     * @param AssessmentTest $test An AssessmentTest object.
     * @param array $classes An array of strings that are QTI class names to be seeked. An empty array means all the QTI classes are seekable.
     */
    public function __construct(AssessmentTest $test, array $classes) {
        $this->setIterator(new QtiComponentIterator($test, $classes));
        $this->setClassCounter(array());
        $this->setComponentStore(array());
    }
    
    /**
     * Set the array that counts the number of explored components by QTI class names.
     * 
     * @param array $classCounter An array where keys are QTI class names and values are the count of explored components for this class name.
     */
    protected function setClassCounter(array $classCounter) {
        $this->classCounter = $classCounter;
    }
    
    /**
     * Get a reference on the array that counts the number of explored components by QTI class names.
     * 
     * @return array An array where keys are QTI class names and values are the count of explored components for this class name.
     */
    protected function &getClassCounter() {
        return $this->classCounter;
    }
    
    /**
     * Get the QtiComponentIterator that explores the test.
     * 
     * @param QtiComponentIterator $iterator
     */
    protected function setIterator(QtiComponentIterator $iterator) {
        $this->iterator = $iterator;
    }
    
    /**
     * Set the QtiComponentIterator that explores the test.
     * 
     * @return QtiComponentIterator
     */
    protected function getIterator() {
        return $this->iterator;
    }
    
    /**
     * Set the componentStore array where keys are QTI class names
     * and value are arrays where the keys are the positions of the already
     * explored components and the values are the components themselves.
     *
     * @param array $componentStore
     */
    protected function setComponentStore(array $componentStore) {
        $this->componentStore = $componentStore;
    }
    
    /**
     * Get the componentStore array where keys are QTI class names
     * and value are arrays where the keys are the positions of the already
     * explored components and the values are the components themselves.
     * 
     * @return array
     */
    protected function &getComponentStore() {
        return $this->componentsStore;
    }
    
    /**
     * Add the component in the ComponentStore.
     * 
     * @param QtiComponent $component A QTI Component.
     * @return integer The position in the AssessmentTest tree the component was found.
     */
    protected function addToComponentStore(QtiComponent $component) {
        $store = &$this->getComponentStore();
        $class = $component->getQtiClassName();
        
        if (isset($store[$class]) === false) {
            $store[$class] = array();
        }
        
        $position = $this->getClassCount($class);
        $store[$class][$position] = $component;
        $this->incrementClassCount($component);
        
        return $position;
    }
    
    /**
     * Get a QtiComponent object from the component store that has $class for QTI class name
     * and which is at position $position in the AssessmentTest tree.
     * 
     * @param string $class A QTI class name.
     * @param integer $position A position in the AssessmentTest tree.
     * @return boolean|QtiComponent A QtiComponent object or false if it is not found.
     */
    protected function getComponentFromComponentStore($class, $position) {
        
        $componentStore = &$this->getComponentStore();
        $component = false;
        
        if (isset($componentStore[$class]) === true && isset($componentStore[$class][$position]) === true) {
            $component = $componentStore[$class][$position];
        }
        
        return $component;
    }
    
    /**
     * Get the position in the AssessmentTest tree for $component.
     * 
     * @param QtiComponent $component
     * @return false|integer The position of $component in the AssessmentTest tree ir false if it could not be found.
     */
    protected function getPositionFromComponentStore(QtiComponent $component) {
        
        $componentStore = &$this->getComponentStore();
        $position = false;
        $class = $component->getQtiClassName();
        
        if (isset($componentStore[$class]) === true) {
            if (($search = array_search($component, $componentStore[$class], true)) !== false) {
                $position = $search;
            }
        }
        
        return $position;
    }
    
    /**
     * Seek for a QtiComponent object that has $class for QTI class name
     * and that is in position $position in the AssessmentTest tree.
     * 
     * @param string $class A QTI class name.
     * @param integer $position A position in the AssessmentTest tree.
     * @return QtiComponent The QtiComponent object that corresponds to $class and $position.
     * @throws OutOfBoundsException If no such QtiComponent could be found in the AssessmentTest tree.
     */
    public function seekComponent($class, $position) {
        
        if (($component = $this->getComponentFromComponentStore($class, $position)) !== false) {
            // Already explored!
            return $component;
        }
        
        // Not already explored.
        // Continue to iterate until its found.
        $iterator = $this->getIterator();
        
        while ($iterator->valid() === true) {
            $component = $iterator->current();
            $newPosition = $this->addToComponentStore($component);
            
            $iterator->next();
            
            if ($class === $component->getQtiClassName() && $newPosition === $position) {
                return $component;
            }
        }
        
        $msg = "Unable to find a QtiComponent object with QTI class '${class}' at position '${position}'.";
        throw new OutOfBoundsException($msg);
    }
    
    /**
     * Seek for the position of $component in the AssessmentTest tree.
     * 
     * @param QtiComponent $component A QtiComponent object which is supposed to be in the AssessmentTest tree.
     * @return integer The position of $component in the AssessmentTest tree.
     * @throws OutOfBoundsException If no such $component could be found in the AssessmentTest tree.
     */
    public function seekPosition(QtiComponent $component) {
        
        if (($position = $this->getPositionFromComponentStore($component)) !== false) {
            // Already explored.
            return $position;
        }
        
        // We have to find it!
        $iterator = $this->getIterator();
        
        while ($iterator->valid() === true) {
            $current = $iterator->current();
            $newPosition = $this->addToComponentStore($current);
            
            $iterator->next();
            
            if ($current === $component) {
                return $newPosition;
            }
        }
        
        $class = $component->getQtiClassName();
        $msg = "Unable to find the position of a QtiComponent with QTI class '${class}'.";
        throw new OutOfBoundsException($msg);
    }
    
    /**
     * Increment the number of explored components by class name thanks
     * to a given $component.
     * 
     * @param QtiComponent $component A QtiComponent object.
     */
    protected function incrementClassCount(QtiComponent $component) {
        $classCounter = &$this->getClassCounter();
        $class = $component->getQtiClassName();
        
        if (isset($classCounter[$class]) === false) {
            $classCounter[$class] = 0;
        }
        
        $classCounter[$class] += 1;
    }
    
    /**
     * Get the number of explored components for a given QTI $class name.
     * 
     * @param $class A QTI class name.
     * @return ninteger The number of explored components that belong to the $class.
     */
    protected function getClassCount($class) {
        $count = 0;
        $classCounter = &$this->getClassCounter();
        
        if (isset($classCounter[$class]) === true) {
            $count = $classCounter[$class];
        }
        
        return $count;
    }
}