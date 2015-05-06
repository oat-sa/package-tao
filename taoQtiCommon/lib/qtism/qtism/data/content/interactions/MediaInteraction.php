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

namespace qtism\data\content\interactions;

use qtism\data\QtiComponentCollection;
use qtism\data\content\xhtml\Object;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The media interaction allows more control over the way the candidate interacts with a 
 * time-based media object and allows the number of times the media object was experienced 
 * to be reported in the value of the associated response variable, which must be of 
 * base-type integer and single cardinality.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class MediaInteraction extends BlockInteraction {
    
    /**
     * From IMS QTI:
     * 
     * The autostart attribute determines if the media object should begin as soon as the 
     * candidate starts the attempt (true) or if the media object should be started under 
     * the control of the candidate (false).
     * 
     * @var boolean
     * @qtism-bean-property
     */
    private $autostart;
    
    /**
     * From IMS QTI:
     * 
     * The minPlays attribute indicates that the media object should be played a minimum 
     * number of times by the candidate. The techniques required to enforce this will vary 
     * from system to system, in some systems it may not be possible at all. By default 
     * there is no minimum. Failure to play the media object the minimum number of 
     * times constitutes an invalid response.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $minPlays = 0;
    
    /**
     * From IMS QTI:
     * 
     * The maxPlays attribute indicates that the media object can be played at most maxPlays 
     * times - it must not be possible for the candidate to play the media object more than 
     * maxPlay times. A value of 0 (the default) indicates that there is no limit.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $maxPlays = 0;
    
    /**
     * From IMS QTI:
     * 
     * The loop attribute is used to set continuous play mode. In continuous play mode, 
     * once the media object has started to play it should play continuously (subject to maxPlays).
     * 
     * @var boolean
     * @qtism-bean-property
     */
    private $loop = false;
    
    /**
     * From IMS QTI:
     * 
     * The media object itself.
     * 
     * @var Object
     * @qtism-bean-property
     */
    private $object;
    
    /**
     * Create a new MediaInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the response variable associated with the interaction.
     * @param boolean $autostart Whether the media has to be played immediately after the begining of the attempt.
     * @param Object $object The media object itself.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     */
    public function __construct($responseIdentifier, $autostart, Object $object, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $id, $class, $lang, $label);
        $this->setAutostart($autostart);
        $this->setObject($object);
    }
    
    /**
     * Set whether or not the media must start as soon as the candidate starts the attempt.
     * 
     * @param boolean $autostart
     * @throws InvalidArgumentException If $autostart is not a boolean value.
     */
    public function setAutostart($autostart) {
        if (is_bool($autostart) === true) {
            $this->autostart = $autostart;
        }
        else {
            $msg = "The 'autostart' argument must be a boolean value, '" . gettype($autostart) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Whether the media must start as soon as the candidate starts the attempt.
     * 
     * @return boolean
     */
    public function mustAutostart() {
        return $this->autostart;
    }
    
    /**
     * Set the minimum number of times the media must be played.
     * 
     * @param integer $minPlays A positive (>= 0) integer.
     * @throws InvalidArgumentException If $minPlays is not a positive integer.
     */
    public function setMinPlays($minPlays) {
        if (is_int($minPlays) === true && $minPlays >= 0) {
            $this->minPlays = $minPlays;
        }
        else {
            $msg = "The 'minPlays' argument must be a positive (>= 0) integer, '" . gettype($minPlays) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the minimum number of times the media must be played.
     * 
     * @return integer A positive (> 0) integer.
     */
    public function getMinPlays() {
        return $this->minPlays;
    }
    
    /**
     * Whether a value is defined for the minPlays argument.
     * 
     * @return boolean
     */
    public function hasMinPlays() {
        return $this->getMinPlays() !== 0;
    }
    
    /**
     * Set the maximum number of times the media can be played.
     * 
     * @param integer $maxPlays A positive (>= 0) integer.
     * @throws InvalidArgumentException If $maxPlays is not a positive integer.
     */
    public function setMaxPlays($maxPlays) {
        if (is_int($maxPlays) && $maxPlays >= 0) {
            $this->maxPlays = $maxPlays;
        }
        else {
            $msg = "The 'maxPlays' argument must be a positive (>= 0) integer, '" . gettype($maxPlays) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the maximum number of times the media can be played.
     * 
     * @return integer A positive (>= 0) integer. 
     */
    public function getMaxPlays() {
        return $this->maxPlays;
    }
    
    /**
     * Whether a value is defined for the maxPlays argument.
     * 
     * @return boolean
     */
    public function hasMaxPlays() {
        return $this->getMaxPlays() !== 0;
    }
    
    /**
     * Set whether or not the continuous play (subject to maxPlays) of the media is in force.
     * 
     * @param boolean $loop
     * @throws InvalidArgumentException If $loop is not a boolean value.
     */
    public function setLoop($loop) {
        if (is_bool($loop) === true) {
            $this->loop = $loop;
        }
        else {
            $msg = "The 'loop' argument must be a boolean value, '" . gettype($loop) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Whether the conitnuous play (subject to maxPlays) is in force.
     * 
     * @return boolean
     */
    public function mustLoop() {
        return $this->loop;
    }
    
    /**
     * Set the media object itself.
     * 
     * @param Object $object
     */
    public function setObject(Object $object) {
        $this->object = $object;
    }
    
    /**
     * Get the media object itself.
     * 
     * @return Object
     */
    public function getObject() {
        return $this->object;
    }
    
    public function getComponents() {
        $parentComponent = parent::getComponents();
        return new QtiComponentCollection(array_merge($parentComponent->getArrayCopy(), array($this->getObject())));
    }
    
    public function getQtiClassName() {
        return 'mediaInteraction';
    }
}