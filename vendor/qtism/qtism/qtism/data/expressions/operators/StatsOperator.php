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


namespace qtism\data\expressions\operators;

use qtism\common\enums\Cardinality;
use qtism\data\expressions\ExpressionCollection;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The statsOperator operator takes 1 sub-expression which is a container of multiple 
 * or ordered cardinality and has a numerical base-type. The result is a single float.
 * If the sub-expression or any value contained therein is NULL, the result is NULL.
 * If any value contained in the sub-expression is not a numerical value, then the 
 * result is NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class StatsOperator extends Operator {
	
	/**
	 * The name of the statistics operator to use.
	 * 
	 * @var integer
	 * @qtism-bean-property
	 */
	private $name;
	
	/**
	 * Create a new instance of StatsOperator.
	 * 
	 * @param ExpressionCollection $expressions A collection of Expression objects.
	 * @param integer $name A value from the Statistics enumeration.
	 * @throws InvalidArgumentException If $name is not a value from the Statistics enumeration or if the count of $expressions is greather than 1.
	 */
	public function __construct(ExpressionCollection $expressions, $name) {
		parent::__construct($expressions, 1, 1, array(Cardinality::MULTIPLE, Cardinality::ORDERED), array(OperatorBaseType::INTEGER, OperatorBaseType::FLOAT));

		$this->setName($name);
	}
	
	/**
	 * Set the statistics operator to use.
	 * 
	 * @param integer $name A value from the Statistics enumeration.
	 * @throws InvalidArgumentException If $name is not a value from the Statistics enumeration.
	 */
	public function setName($name) {
		if (in_array($name, Statistics::asArray())) {
			$this->name = $name;
		}
		else {
			$msg = "The name argument must be a value from the Statistics enumeration, '" . $name . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the name of the statistics operator to use.
	 * 
	 * @return integer A value from the Statistics enumeration.
	 */
	public function getName() {
		return $this->name;
	}
	
	public function getQtiClassName() {
		return 'statsOperator';
	}
}
