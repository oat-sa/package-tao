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

use \InvalidArgumentException;

/**
 * Any class which corresponds to a QTI component
 * must implement this class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class QtiComponent {
	
	/**
	 * Returns the QTI class name as per QTI 2.1 specification.
	 * 
	 * @return string A QTI class name.
	 */
	abstract public function getQtiClassName();
	
	/**
	 * Get the direct child components of this one.
	 * 
	 * @return QtiComponentCollection A collection of QtiComponent objects.
	 */
	abstract public function getComponents();
	
	/**
	 * Get a QtiComponentIterator object which allows you to iterate
	 * on all QtiComponent objects hold by this one.
	 * 
	 * @return QtiComponentIterator A QtiComponentIterator object.
	 */
	public function getIterator() {
		return new QtiComponentIterator($this);
	}
	
	/**
	 * Get a QtiComponent object which is contained by this one on the basis
	 * of a given $identifier.
	 * 
	 * @param string $identifier The identifier to search for.
	 * @param boolean $recursive Whether to search recursively in contained QtiComponent objects.
	 * @return QtiComponent|null A QtiComponent object or null if not found.
	 * @throws InvalidArgumentException If $identifier is not a string.
	 */
	public function getComponentByIdentifier($identifier, $recursive = true) {
		
		if (gettype($identifier) !== 'string') {
			$msg = "The QtiComponent::getComponentByIdentifier method only accepts a string as its ";
			$msg.= "argument. '" . gettype($identifier) . "' given.";
			throw new InvalidArgumentException($msg);
		}
		
		$toIterate = ($recursive === true) ? $this->getIterator() : $this->getComponents();
		
		foreach ($toIterate as $component) {
			if ($component instanceof QtiIdentifiable && $component->getIdentifier() === $identifier) {
				return $component;
			}
		}
		
		return null;
	}
	
	/**
	 * Get QtiComponents object which is contained by this one the basis of
	 * a given QTI className. If nothing found, an empty QtiComponentCollection
	 * object is returned.
	 * 
	 * Example where we look for all assessmentSection class instances contained
	 * in an assessmentTest.
	 * <code>
	 * $search = $assessmentTest->getComponentByClassName('assessmentSection');
	 * // $search contains a QTIComponentCollection composed of AssessmentSection objects.
	 * </code>
	 * 
	 * @param array|string An array of strings or a string.
	 * @param boolean $recursive Whether to search recursively in contained QtiComponent objects.
	 * @return QtiComponentCollection
	 * @throws InvalidArgumentException If $classNames is not an array nor a string value.
	 */
	public function getComponentsByClassName($classNames, $recursive = true) {
		if (gettype($classNames) !== 'string' && !is_array($classNames)) {
			$msg = "The QtiComponent::getComponentsByClassName method only accepts ";
			$msg.= "a string or an array as its main argument, '" . gettype($classNames) . "' given.";
			throw new InvalidArgumentException($classNames);
		}
		
		if (!is_array($classNames)) {
			$classNames = array($classNames);
		}
		
		$toIterate = ($recursive === true) ? $this->getIterator() : $this->getComponents();
		$foundComponents = new QtiComponentCollection();
		
		foreach ($toIterate as $component) {
			if (in_array($component->getQtiClassName(), $classNames)) {
				$foundComponents[] = $component;
			}
		}
		
		return $foundComponents;
	}
	
	/**
	 * Get the QtiIdentifiableComponent object contained by this QtiComponent. Be carefull,
	 * the type of the returned collection depends on the values collected:
	 * 
	 * * If all the objects found have a unique identifier within the retrieved set, a QtiIdentifiableCollection is returned.
	 * * If an identifier collision occurs within the retrieved set, a QtiComponentCollection is returned.
	 * 
	 * @param boolean $recursive Whether to search recursively in contained QtiComponent objects.
	 * @return QtiComponentCollection A QtiIdentifiableCollection or a QtiComponentCollection in case of collision.
	 */
	public function getIdentifiableComponents($recursive = true) {
	    
	    $iterator = ($recursive === true) ? $this->getIterator() : $this->getComponents();
	    $foundComponents = array();
	    $identifiers = array();
	    $collision = false;
	    
	    foreach ($iterator as $component) {
	        if ($component instanceof QtiIdentifiable) {
                $foundComponents[] = $component;
                $identifier = $component->getIdentifier();
                
                if (in_array($identifier, $identifiers) === true && $collision === false) {
                    $collision = true;
                }
                
                $identifiers[] = $identifier;
	        }
	    }
	    
	    return ($collision === true) ? new QtiComponentCollection($foundComponents) : new QtiIdentifiableCollection($foundComponents);
	}
}
