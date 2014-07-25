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
namespace qtism\common\datatypes;

use qtism\common\Comparable;
use \DateInterval;
use \DateTime;
use \Exception;
use \InvalidArgumentException;

/**
 * Implementation of the QTI duration datatype.
 * 
 * The duration datatype enables you to express time duration as specified
 * by ISO8601.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Duration implements Comparable {
	
	private $interval;
	
	/**
	 * Create a new instance of Duration.
	 * 
	 * The $intervalSpec argument is a duration specification:
	 * 
	 * The format begins with <b>P</b> for "period". Each year of the period is represented
	 * by an integer value followed by the period symbol. If the duration contains timing elements,
	 * this portion of the specification is prefixed by letter <b>T</b>.
	 * 
	 * * Y -> Years
	 * * M -> Months
	 * * D -> Days
	 * * W -> Week. It will be converted to days. Then, you cannot combine it with D.
	 * * H -> Hours
	 * * M -> Minutes
	 * * S -> Seconds
	 * 
	 * Here are examples: 2 days will be <b>P2D</b>, 2 seconds will be <b>P2TS</b>,
	 * 6 years and 5 minutes will be <b>P6YT5M</b>.
	 * 
	 * Please note that this datatype does not support negative durations.
	 * 
	 * @param string $intervalSpec A duration as in ISO8601.
	 * @throws InvalidArgumentException If $intervalSpec is not a valid ISO8601 duration.
	 */
	public function __construct($intervalSpec) {
		if (gettype($intervalSpec) === 'string' && empty($intervalSpec) === false) {
			try {
				$d1 = new DateTime();
				$d2 = new DateTime();
				$d2->add(new DateInterval($intervalSpec));
				$interval = $d2->diff($d1);
				$interval->invert = ($interval->invert === 1) ? 0 : 1;
				$this->setInterval($interval);
			}
			catch (Exception $e) {
				$msg = "The specified interval specification cannot be processed as an ISO8601 duration.";
				throw new InvalidArgumentException($msg, 0, $e);
			}
		}
		else {
			$msg = "The intervalSpec argument must be a non-empty string.";
			throw new InvalidArgumentException($msg);
		}
		
	}
	
	/**
	 * Get the PHP DateInterval object corresponding to the duration.
	 * 
	 * @return DateInterval A DateInterval PHP object.
	 */
	protected function getInterval() {
		return $this->interval;
	}
	
	/**
	 * Set the PHP DateInterval object corresponding to the duration.
	 * 
	 * @param DateInterval $interval A DateInterval PHP object.
	 */
	protected function setInterval(DateInterval $interval) {
		$this->interval = $interval;
	}
	
	/**
	 * Get the number of years.
	 * 
	 * @return int
	 */
	public function getYears() {
		return $this->getInterval()->y;
	}
	
	/**
	 * Get the number of months.
	 * 
	 * @return int
	 */
	public function getMonths() {
		return $this->getInterval()->m;
	}
	
	/**
	 * Get the number of days.
	 * 
	 * @param boolean $total Wether the number of days must be the total of days or simply an offset (default).
	 * @return int
	 */
	public function getDays($total = false) {
		return ($total == true) ? $this->getInterval()->days : $this->getInterval()->d;
	}
	
	/**
	 * Get the number of hours.
	 * 
	 * @return int
	 */
	public function getHours() {
		return $this->getInterval()->h;
	}
	
	/**
	 * Get the number of minutes.
	 * 
	 * @return int
	 */
	public function getMinutes() {
		return $this->getInterval()->i;
	}
	
	/**
	 * Get the number of seconds.
	 * 
	 * @param $total Whether to get the total amount of seconds, as a single integer, that represents the complete duration.
	 * @return int
	 */
	public function getSeconds($total = false) {
	    if ($total === false) {
	        return $this->getInterval()->s;
	    }
		
	    $sYears = 31536000 * $this->getYears();
	    $sMonths = 30 * 24 * 3600 * $this->getMonths();
	    $sDays = 24 * 3600 * $this->getDays();
	    $sHours = 3600 * $this->getHours();
	    $sMinutes = 60 * $this->getMinutes();
	    $sSeconds = $this->getSeconds();
	    
	    return $sYears + $sMonths + $sDays + $sHours + $sMinutes + $sSeconds;
	}
	
	public function __toString() {
		$string = 'P';
		
		if ($this->getYears() > 0) {
			$string .= $this->getYears() . 'Y';
		}
		
		if ($this->getMonths() > 0) {
			$string .= $this->getMonths() . 'M';
		}
		
		if ($this->getDays() > 0) {
			$string .= $this->getDays() . 'D';
		}
		
		if ($this->getHours() > 0 || $this->getMinutes() > 0 || $this->getSeconds() > 0) {
			$string .= 'T';
			
			if ($this->getHours() > 0) {
				$string .= $this->getHours() . 'H';
			}
			
			if ($this->getMinutes() > 0) {
				$string .= $this->getMinutes() . 'M';
			}
			
			if ($this->getSeconds() > 0) {
				$string .= $this->getSeconds() . 'S';
			}
		}
		
	    if ($string === 'P') {
	        // Special case, the duration is 'nothing'.
	        return 'PT0S';
	    }
		
		return $string;
	}
	
	/**
	 * Whether a given $obj is equal to this Duration.
	 * 
	 * @param mixed $obj A given value.
	 * @return boolean Whether the equality is established.
	 */
	public function equals($obj) {
		return (gettype($obj) === 'object' &&
				$obj instanceof self &&
				'' . $obj === '' . $this);
	}
	
	/**
	 * Whether the duration described by this Duration object is shorter
	 * than the one described by $duration.
	 * 
	 * @param Duration $duration A Duration object to compare with this one.
	 * @return boolean
	 */
	public function shorterThan(Duration $duration) {
		if ($this->getYears() < $duration->getYears()) {
			return true;
		}
		else if ($this->getMonths() < $duration->getMonths()) {
			return true;
		}
		else if ($this->getDays() < $duration->getDays()) {
			return true;
		}
		else if ($this->getHours() < $duration->getHours()) {
			return true;
		}
		else if ($this->getMinutes() < $duration->getMinutes()) {
			return true;
		}
		else if ($this->getSeconds() < $duration->getSeconds()) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Whether the duration described by this Duration object is longer than or
	 * equal to the one described by $duration.
	 * 
	 * @param Duration $duration A Duration object to compare with this one.
	 * @return boolean
	 */
	public function longerThanOrEquals(Duration $duration) {
		if ($this->getYears() < $duration->getYears()) {
			return false;
		}
		else if ($this->getMonths() < $duration->getMonths()) {
			return false;
		}
		else if ($this->getDays() < $duration->getDays()) {
			return false;
		}
		else if ($this->getHours() < $duration->getHours()) {
			return false;
		}
		else if ($this->getMinutes() < $duration->getMinutes()) {
			return false;
		}
		else if ($this->getSeconds() < $duration->getSeconds()) {
			return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * Add a duration to this one.
	 * 
	 * For instance, PT1S + PT1S = PT2S.
	 * 
	 * @param Duration|DateInterval $duration A Duration or DateInterval object.
	 */
	public function add($duration) {
		$refStrDate = '19710101';
		$d1 = new DateTime($refStrDate);
		$d2 = new DateTime($refStrDate);
		
		if ($duration instanceof Duration) {
		    $toAdd = $duration;
		}
		else {
		    $toAdd = new Duration('PT0S');
		    $toAdd->setInterval($duration);
		}
		
		$d2->add(new DateInterval($this->__toString()));
		$d2->add(new DateInterval($toAdd->__toString()));
		
		$interval = $d2->diff($d1);
		$this->setInterval($interval);
	}
	
	/**
	 * Subtract a duration to this one. If $duration is greather than or equal to
	 * the current duration, a duration of 0 seconds is returned.
	 * 
	 * For instance P2S - P1S = P1S
	 */
	public function sub(Duration $duration) {
	    
	    if ($duration->longerThanOrEquals($this) === true) {
	        $this->setInterval(new DateInterval('PT0S'));
	    }
	    else {
	        $refStrDate = '19710101';
	        $d1 = new DateTime($refStrDate);
	        $d2 = new DateTime($refStrDate);
	        
	        $d1->add(new DateInterval($this->__toString()));
	        $d2->add(new DateInterval($duration->__toString()));
	        
	        $interval = $d1->diff($d2);
	        $this->setInterval($interval);
	    }
	}
	
	public function __clone() {
		// ... :'( ... https://bugs.php.net/bug.php?id=50559
		$d1 = new DateTime();
		$d2 = new DateTime();
		$d2->add(new DateInterval($this->__toString()));
		$interval = $d2->diff($d1);
		$interval->invert = ($interval->invert === 1) ? 0 : 1;
		$this->setInterval($interval);
	}
}