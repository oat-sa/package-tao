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
namespace qtism\common\datatypes;

use oat\dtms\DateInterval;
use oat\dtms\DateTime;
use DateTimeZone;
use Exception;
use InvalidArgumentException;
use qtism\common\Comparable;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;

/**
 * Implementation of the QTI duration datatype.
 * 
 * The duration datatype enables you to express time duration as specified
 * by ISO8601.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Duration implements Comparable, QtiDatatype {
	
    /**
     * Internally, the Duration class always
     * uses the UTC reference time zone.
     * 
     * @var string
     */
    const TIMEZONE = 'UTC';
    
    private $refDate;
    
    /**
     * 
     * @var DateInterval
     */
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
		if (gettype($intervalSpec) === 'string' && $intervalSpec !== '') {
			try {
			    $tz = new DateTimeZone(self::TIMEZONE);
				$d1 = new DateTime('now', $tz);
				$d2 = clone $d1;

				$d2->add(new DateInterval($intervalSpec));
				$interval = $d2->diff($d1);
				$interval->invert = ($interval->invert === 1) ? 0 : 1;
				$this->interval = $interval;
				$this->refDate = new DateTime('@0', $tz);
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
	
	static public function createFromDateInterval(\DateInterval $interval) {
	    $duration = new Duration('PT0S');
	    $duration->setInterval($interval);
	    return $duration;
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
	protected function setInterval(\DateInterval $interval) {
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
	 * @param int $total Whether to get the total amount of seconds, as a single integer, that represents the complete duration.
	 * @return int The value of the total duration in seconds.
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

	/**
	 * Get the number of microseconds.
	 *
	 * @param bool|false $total Whether to get the total amount of microseconds, as a single integer, that represents the complete duration.
	 * @return int The value of the total duration in microseconds.
	 */
	public function getMicroseconds($total = false)
	{
		if (!property_exists($this->getInterval(), 'u')) {
			return 0;
		}

		if ($total === false) {
			return $this->getInterval()->u;
		}

		return $this->getSeconds(true) * 1e6 + $this->getMicroseconds();
	}
	
	public function __toString() {
		$string = 'P';
		
		if ($this->interval->y > 0) {
			$string .= $this->interval->y . 'Y';
		}
		
		if ($this->interval->m > 0) {
			$string .= $this->interval->m . 'M';
		}
		
		if ($this->interval->d > 0) {
			$string .= $this->interval->d . 'D';
		}
		
		if ($this->interval->h > 0
			|| $this->interval->i > 0
			|| $this->interval->s > 0
			|| (property_exists($this->interval, 'u') && $this->interval->u > 0)
		) {
			$string .= 'T';
			
			if ($this->interval->h > 0) {
				$string .= $this->interval->h . 'H';
			}
			
			if ($this->interval->i > 0) {
				$string .= $this->interval->i . 'M';
			}
			
			if ($this->getSeconds() > 0) {
				$string .= $this->interval->s . 'S';
			}

			if (property_exists($this->interval, 'u') && $this->getMicroseconds() > 0 ) {
				$u = str_pad($this->interval->u, 6, 0, STR_PAD_LEFT);
				if ($this->getSeconds() != 0) {
					$string = str_replace('S', '.' . $u . 'S', $string);
				} else {
					$string .= '0.' . $u . 'S';
				}

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
		return (
			is_object($obj)
			&& $obj instanceof self
			&& '' . $this === '' . $obj
		);
	}

	public function round()
	{
		$seconds = round($this->getMicroseconds() / 1e6, 0, PHP_ROUND_HALF_UP);

		$this->getInterval()->u = 0;
		$this->getInterval()->s += $seconds;

		return $this;
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
		else if ($this->getMicroseconds() < $duration->getMicroseconds()) {
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
		else if ($this->getMicroseconds() < $duration->getMicroseconds()) {
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
		$d1 = $this->refDate; 
		$d2 = clone $d1;
		
		if ($duration instanceof Duration) {
		    $toAdd = $duration;
		} else {
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
	        $this->setInterval(new DateInterval('PT0.0S'));
	    } else {
	        $refStrDate = '@0';
	        $tz = new DateTimeZone(self::TIMEZONE);
	        $d1 = new DateTime($refStrDate, $tz);
			$d2 = clone $d1;

	        $d1->add(new DateInterval($this->__toString()));
	        $d2->add(new DateInterval($duration->__toString()));
	        
	        $interval = $d1->diff($d2);
	        $this->setInterval($interval);
	    }
	}
	
	public function __clone() {
		// ... :'( ... https://bugs.php.net/bug.php?id=50559
		$tz = new DateTimeZone(self::TIMEZONE);
		$d1 = new DateTime('now', $tz);
		$d2 = clone $d1;
		$d2->add(new DateInterval($this->__toString()));
		$interval = $d2->diff($d1);
		$interval->invert = ($interval->invert === 1) ? 0 : 1;
		$this->setInterval($interval);
	}
	
	public function isNegative() {
	    return $this->interval->invert === 0;
	}
	
	public function getBaseType() {
	    return BaseType::DURATION;
	}
	
	public function getCardinality() {
	    return Cardinality::SINGLE;
	}
}
