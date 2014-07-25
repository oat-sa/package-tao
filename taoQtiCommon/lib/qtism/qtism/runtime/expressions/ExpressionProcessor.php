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
namespace qtism\runtime\expressions;

use qtism\runtime\common\Processable;
use qtism\runtime\common\State;
use qtism\data\expressions\Expression;

/**
 * The ExpressionProcessor class aims at processing QTI Data Model
 * Expressions.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class ExpressionProcessor implements Processable {
	
	/**
	 * The QTI Data Model expression to be Processed.
	 * 
	 * @var Expression
	 */
	private $expression = null;
	
	/**
	 * A state.
	 * 
	 * @var State
	 */
	private $state = null;
	
	/**
	 * Create a new ExpressionProcessor object.
	 * 
	 * @param Expression $expression The QTI Data Model Expression to be processed.
	 */
	public function __construct(Expression $expression) {
		$this->setExpression($expression);
		$this->setState(new State());
	}
	
	/**
	 * Set the QTI Data Model Expression to be processed.
	 * 
	 * @param Expression $expression A QTI Data Model Expression object.
	 */
	public function setExpression(Expression $expression) {
		$this->expression = $expression;
	}
	
	/**
	 * Get the QTI Data Model Expression to be processed.
	 * 
	 * @return Expression A QTI Data Model Expression object.
	 */
	public function getExpression() {
		return $this->expression;
	}
	
	/**
	 * Set the current State object.
	 * 
	 * @param State $state A State object.
	 */
	public function setState(State $state) {
		$this->state = $state;
	}
	
	/**
	 * Get the current State object.
	 * 
	 * @return State
	 */
	public function getState() {
		return $this->state;
	}
}