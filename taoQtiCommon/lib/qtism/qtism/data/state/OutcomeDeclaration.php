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


namespace qtism\data\state;

use qtism\data\QtiComponentCollection;
use qtism\data\ViewCollection;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * Outcome variables are declared by outcome declarations. Their value is set either from a 
 * default given in the declaration itself or by a responseRule during responseProcessing.
 * 
 * Items that declare a numeric outcome variable representing the candidate's overall 
 * performance on the item should use the outcome name 'SCORE' for the variable. SCORE 
 * needs to be a float.
 * 
 * Items that declare a maximum score (in multiple response choice interactions, for example) 
 * should do so by declaring the 'MAXSCORE' variable. MAXSCORE needs to be a float.
 * 
 * Items or tests that want to make the fact that the candidate scored above a predefined 
 * treshold available as a variable should use the 'PASSED' variable. PASSED needs to be a 
 * boolean.
 * 
 * At runtime, outcome variables are instantiated as part of an item session. 
 * Their values may be initialized with a default value and/or set during responseProcessing. 
 * If no default value is given in the declaration then the outcome variable is initialized to 
 * NULL unless the outcome is of a numeric type (integer or float) in which case it is 
 * initialized to 0.
 * 
 * For Non-adaptive Items, the values of the outcome variables are reset to their default 
 * values prior to each invocation of responseProcessing. For Adaptive Items the outcome 
 * variables retain the values that were assigned to them during the previous invocation of 
 * response processing. For more information, see Response Processing.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OutcomeDeclaration extends VariableDeclaration {
	
	/**
	 * From IMS QTI:
	 * 
	 * The intended audience for an outcome variable can be set with the view attribute. 
	 * If no view is specified the outcome is treated as relevant to all views. 
	 * Complex items, such as adaptive items or complex templates, may declare 
	 * outcomes that are of no interest to the candidate at all, but are merely 
	 * used to hold intermediate values or other information useful during the 
	 * item or test session. Such variables should be declared with a view of 
	 * author (for item outcomes) or testConstructor (for test outcomes). 
	 * Systems may exclude outcomes from result reports on the basis of their 
	 * declared view if appropriate. Where more than one class of user should 
	 * be able to view an outcome variable the view attribute should contain a 
	 * comma delimited list.
	 * 
	 * @var Viewcollection
	 * @qtism-bean-property
	 */
	private $views;
	
	/**
	 * From IMS QTI:
	 * 
	 * A human interpretation of the variable's value.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $interpretation = '';
	
	/**
	 * From IMS QTI:
	 * 
	 * An optional link (URI) to an extended interpretation of the outcome variable's value.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $longInterpretation = '';
	
	/**
	 * Normal Maximum. If false, means it was not specified.
	 * 
	 * From IMS QTI:
	 * 
	 * The normalMaximum attribute optionally defines the maximum magnitude of numeric outcome 
	 * variables, it must be a positive value. If given, the outcome's value can be divided 
	 * by normalMaximum and then truncated (if necessary) to obtain a normalized score in 
	 * the range [-1.0,1.0]. normalMaximum has no affect on responseProcessing or the values 
	 * that the outcome variable itself can take.
	 * 
	 * @var boolean|number
	 * @qtism-bean-property
	 */
	private $normalMaximum = false;
	
	/**
	 * Normal Minimum. If false, means it was not specified.
	 * 
	 * From IMS QTI:
	 * 
	 * The normalMinimum attribute optionally defines the minimum value of numeric outcome 
	 * variables, it may be negative.
	 * 
	 * @var boolean|number
	 * @qtism-bean-property
	 */
	private $normalMinimum = false;
	
	/**
	 * Mastery Value. If false, means it was not specified.
	 * 
	 * From IMS QTI:
	 * 
	 * The masteryValue attribute optionally defines a value for numeric outcome variables 
	 * above which the aspect being measured is considered to have been mastered by the candidate.
	 * 
	 * @var boolean|number
	 * @qtism-bean-property
	 */
	private $masteryValue = false;
	
	/**
	 * The lookup table. See LookupTable for more information.
	 * 
	 * @var LookupTable
	 * @qtism-bean-property
	 */
	private $lookupTable = null;
	
	/**
	 * Create a new instanceof OutcomeDeclaration.
	 * 
	 * @param string $identifier A QTI identifier.
	 * @param int $baseType A value from the BaseType enumeration.
	 * @param int $cardinality A value from the Cardinality enumeration.
	 * @param DefaultValue $defaultValue A DefaultValue object.
	 * @throws InvalidArgumentException If one or more of the arguments are invalid.
	 */
	public function __construct($identifier, $baseType = -1, $cardinality = Cardinality::SINGLE, DefaultValue $defaultValue = null) {
		parent::__construct($identifier, $baseType, $cardinality, $defaultValue);
		$this->setViews(new ViewCollection());
	}
	
	/**
	 * Get the intented audience for this Outcome Declaration. If the returned
	 * collection is empty, it means that the outcomeDeclaration is relevant to
	 * all views.
	 * 
	 * @return Viewcollection A Collection of values that are values from the View enumeration.
	 */
	public function getViews() {
		return $this->views;
	}
	
	/**
	 * Set the intended audience for this Outcome Declaration. If the given collection
	 * is empty, it means that the outcomeDeclaration is relevant to all views.
	 * 
	 * @param ViewCollection $views A collection of values that are values from the View enumeration.
	 */
	public function setViews(ViewCollection $views) {
		$this->views = $views;
	}
	
	/**
	 * Get the human interpretation of the outcome variable's value. Returns an empty
	 * string if not set.
	 * 
	 * @return string A string.
	 */
	public function getInterpretation() {
		return $this->interpretation;
	}
	
	/**
	 * Set the human interpretation of the outcome variable's value.
	 * 
	 * @param string $interpretation A string.
	 * @throws InvalidArgumentException If $interpretation is not a string.
	 */
	public function setInterpretation($interpretation) {
		if (gettype($interpretation) === 'string') {
			$this->interpretation = $interpretation;
		}
		else {
			$msg = "Interpretation must be a string, '" . gettype($interpretation) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get a link (URI) to an extended interpretation of the ouctome variable's value.
	 * 
	 * @return string A URI.
	 */
	public function getLongInterpretation() {
		return $this->longInterpretation;
	}
	
	/**
	 * Set a link (URI) to an extended interpretation of the outcome variable's value.
	 * 
	 * @param string $longInterpretation A string.
	 * @throws InvalidArgumentException If $longInterpretation is not a string.
	 */
	public function setLongInterpretation($longInterpretation) {
		if (gettype($longInterpretation) === 'string') {
			$this->longInterpretation = $longInterpretation;
		}
		else {
			$msg = "LongInterpretation must be a string, '" . gettype($longInterpretation) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the normal minimum. Returns false if not specifed.
	 * 
	 * @return boolean|float A numeric value.
	 */
	public function getNormalMinimum() {
		return $this->normalMinimum;
	}
	
	/**
	 * Set the normal minimum.
	 * 
	 * @param boolean|numeric $normalMinimum A numeric value.
	 * @throws InvalidArgumentException If $normalMinimum is not numeric nor false.
	 */
	public function setNormalMinimum($normalMinimum) {
		if (is_numeric($normalMinimum) || (is_bool($normalMinimum) && $normalMinimum === false)) {
			$this->normalMinimum = $normalMinimum;
		}
		else {
			$msg = "NormalMinimum must be a number or (boolean) false, '" . gettype($normalMinimum) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the normal maximum. false if not specfied.
	 * 
	 * @return boolean|number A numeric value or false.
	 */
	public function getNormalMaximum() {
		return $this->normalMaximum;
	}
	
	/**
	 * Set the normal maximum.
	 * 
	 * @param boolean|number $normalMaximum A numeric value.
	 * @throws InvalidArgumentException If $normalMaximum is not a numeric value nor false.
	 */
	public function setNormalMaximum($normalMaximum) {
		if (is_numeric($normalMaximum) || (is_bool($normalMaximum) && $normalMaximum === false)) {
			$this->normalMaximum = $normalMaximum;
		}
		else {
			$msg = "NormalMaximum must be a number or (boolean) false, '" . gettype($normalMaximum) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the mastery value. Returns false if not specified.
	 * 
	 * @return boolean|number A numeric value or false.
	 */
	public function getMasteryValue() {
		return $this->masteryValue;
	}
	
	/**
	 * Set the mastery value. Set to false if not specified.
	 * 
	 * @param boolean|number $masteryValue A numeric value or false.
	 * @throws InvalidArgumentException If $masteryValue is not numeric nor false.
	 */
	public function setMasteryValue($masteryValue) {
		if (is_numeric($masteryValue) || (is_bool($masteryValue) && $masteryValue === false)) {
			$this->masteryValue = $masteryValue;
		}
		else {
			$msg = "MasteryValue must be a number or (boolean) false, '" . gettype($masteryValue) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the LookupTable. Returns null value if no LookupTable was specified.
	 * 
	 * @return LookupTable A LookupTable or null value if not specified.
	 */
	public function getLookupTable() {
		return $this->lookupTable;
	}
	
	/**
	 * Set the LookupTable.
	 * 
	 * @param LookupTable $lookupTable A LookupTable object.
	 */
	public function setLookupTable(LookupTable $lookupTable = null) {
		$this->lookupTable = $lookupTable;
	}
	
	public function getQtiClassName() {
		return 'outcomeDeclaration';
	}
	
	public function getComponents() {
		$comp = parent::getComponents()->getArrayCopy();
		
		if ($this->getLookupTable() !== null) {
			$comp[] = $this->getLookupTable();
		}
		
		return new QtiComponentCollection($comp);
	}
}
