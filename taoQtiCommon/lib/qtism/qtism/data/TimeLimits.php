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

use qtism\common\datatypes\Duration;
use \InvalidArgumentException;

/**
 * In the context of a specific assessmentTest an item, or group of items, 
 * may be subject to a time constraint. This specification supports both 
 * minimum and maximum time constraints. The controlled time for a single 
 * item is simply the duration of the item session as defined by the builtin 
 * response variable duration. For assessmentSections, testParts and whole 
 * assessmentTests the time limits relate to the durations of all the item 
 * sessions plus any other time spent navigating that part of the test. 
 * In other words, the time includes time spent in states where no item 
 * is being interacted with, such as dedicated navigation screens.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TimeLimits extends QtiComponent {
	
	/**
	 * Minimum time.
	 * 
	 * From IMS QTI:
	 * 
	 * Minimum times are applicable to assessmentSections and assessmentItems only when
	 * linear navigation mode is in effect.
	 * 
	 * null = unlimited
	 * 
	 * @var Duration
	 * @qtism-bean-property
	 */
	private $minTime = null;
	
	/**
	 * Maximum time.
	 * 
	 * null = unlimited
	 * 
	 * @var Duration
	 * @qtism-bean-property
	 */
	private $maxTime = null;
	
	/**
	 * From IMS QTI:
	 * 
	 * The allowLateSubmission attribute regulates whether a candidate's response that is 
	 * beyond the maxTime should still be accepted.
	 * 
	 * @var boolean
	 * @qtism-bean-property
	 */
	private $allowLateSubmission = false;
	
	/**
	 * Create a new instance of TimeLimits.
	 * 
	 * @param Duration $minTime The minimum time. Give null if not defined.
	 * @param Duration $maxTime The maximum time. Give null if not defined.
	 * @param string $allowLateSubmission Wether it allows late submission of responses.
	 */
	public function __construct($minTime = null, $maxTime = null, $allowLateSubmission = false) {
		$this->setMinTime($minTime);
		$this->setMaxTime($maxTime);
		$this->setAllowLateSubmission($allowLateSubmission);
	}
	
	/**
	 * Get the minimum time.
	 * 
	 * @return Duration A Duration object or null if unlimited.
	 */
	public function getMinTime() {
		return $this->minTime;
	}
	
	/**
	 * Whether a minTime is defined.
	 * 
	 * @return boolean
	 */
	public function hasMinTime() {
		return !is_null($this->getMinTime());
	}
	
	/**
	 * Set the minimum time.
	 * 
	 * @param Duration $minTime A Duration object or null if unlimited.
	 */
	public function setMinTime(Duration $minTime = null) {
	    // Prevent to get 0s durations stored.
	    if (is_null($minTime) === false && $minTime->getSeconds(true) === 0) {
	        $minTime = null;
	    }
	    
	    $this->minTime = $minTime;
	}
	
	/**
	 * Get the maximum time. Returns null if unlimited
	 * 
	 * @return Duration A Duration object or null if unlimited.
	 */
	public function getMaxTime() {
		return $this->maxTime;
	}
	
	/**
	 * Whether a maxTime is defined.
	 * 
	 * @return boolean
	 */
	public function hasMaxTime() {
		return !is_null($this->getMaxTime());
	}
	
	/**
	 * Set the maximum time or null if unlimited.
	 * 
	 * @param Duration $maxTime A duration object or null if unlimited.
	 */
	public function setMaxTime(Duration $maxTime = null) {
	    // Prevent to get 0s durations stored.
	    if (is_null($maxTime) === false && $maxTime->getSeconds(true) === 0) {
	        $maxTime = null;
	    }
	    
	    $this->maxTime = $maxTime;
	}
	
	/**
	 * Wether a candidate's response that is beyond the maxTime should be still
	 * accepted.
	 * 
	 * @return boolean true if the candidate's response should still be accepted, false if not.
	 */
	public function doesAllowLateSubmission() {
		return $this->allowLateSubmission;
	}
	
	/**
	 * Set wether a candidate's response that is beyond the maxTime should be still
	 * accepted.
	 * 
	 * @param boolean $allowLateSubmission true if the candidate's response should still be accepted, false if not.
	 * @throws InvalidArgumentException If $allowLateSubmission is not a boolean.
	 */
	public function setAllowLateSubmission($allowLateSubmission) {
		if (is_bool($allowLateSubmission)) {
			$this->allowLateSubmission = $allowLateSubmission;
		}
		else {
			$msg = "AllowLateSubmission must be a boolean, '" . gettype($allowLateSubmission) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getQtiClassName() {
		return 'timeLimits';
	}
	
	public function getComponents() {
		return new QtiComponentCollection();
	}
}
