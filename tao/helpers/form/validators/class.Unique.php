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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class tao_helpers_form_validators_Unique
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
class tao_helpers_form_validators_Unique
    extends tao_helpers_form_Validator
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array $options
     * @return mixed
     */
    public function __construct($options = array())
    {
        
		parent::__construct($options);
		
		(isset($options['message'])) ? $this->message = $options['message'] : $this->message = __('Entity with such field already present');

    }

    /**
     * Short description of method evaluate
     *
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  mixed $values
     * @return boolean
     */
    public function evaluate($values)
    {
		$result = true;
		/** @var core_kernel_classes_Class $resource */
		$resourceClass = $values[0];
		/** @var string $property */
		$property = $values[1];
		$value = $values[2];

		$parentClasses = $resourceClass->getParentClasses(true);
		if (is_array($parentClasses)) {
			$veryParentClass = end($parentClasses);
			if ($veryParentClass) {
				$resources = $veryParentClass->searchInstances(array($property => $value,), array('recursive' => true));
				$result = (count($resources) === 0);
			}
		}

		return $result;
    }

}
