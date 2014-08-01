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

namespace qtism\runtime\tests;

use qtism\data\QtiComponent;
use qtism\data\TimeLimits;

/**
 * A TimeLimits involved in a Route by its association to 
 * a RouteItem object.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RouteTimeLimits {
    
    /**
     * Create a new RouteTimeLimts object.
     * 
     * @param TimeLimits $timeLimits
     * @param QtiComponent $owner The owner component of the TimeLimits to be represented.
     */
    public function __construct(TimeLimits $timeLimits, QtiComponent $owner) {
        $this->setTimeLimits($timeLimits);
        $this->setOwner($owner);
    }
    
    /**
     * The owner component of the TimeLimits.
     *  
     * @var QtiComponent
     */
    private $owner;
    
    /**
     * The encapsulated TimeLimits object.
     * 
     * @var TimeLimits
     */
    private $timeLimits;
    
    /**
     * Get the owner component object of the TimeLimits.
     * 
     * @return QtiComponent A QtiComponent object.
     */
    public function getOwner() {
        return $this->owner;
    }
    
    /**
     * Set the owner component object of the TimeLimits.
     * 
     * @param QtiComponent $owner A QtiComponent object.
     */
    public function setOwner(QtiComponent $owner) {
        $this->owner = $owner;
    }
    
    /**
     * Get the encapsulated TimeLimits object.
     * 
     * @return TimeLimits
     */
    public function getTimeLimits() {
        return $this->timeLimits;
    }
    
    /**
     * Set the encapsulated TimeLimits object.
     * 
     * @param TimeLimits $timeLimits
     */
    public function setTimeLimits(TimeLimits $timeLimits) {
        $this->timeLimits = $timeLimits;
    }
    
    /**
     * Create new RouteTimeLimits object from a base TimeLimits object
     * and its owner component.
     * 
     * @param TimeLimits $timeLimits A TimeLimits object.
     * @param QtiComponent $owner The owner component of $timeLimits.
     * @return RouteTimeLimits A new RouteTimeLimits object.
     */
    public static function createFromTimeLimits(TimeLimits $timeLimits, QtiComponent $owner) {
        return new static($timeLimits, $owner);
    }
}