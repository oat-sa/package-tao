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
use qtism\common\datatypes\Point;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The position object interaction consists of a single image which must be positioned on 
 * another graphic image (the stage) by the candidate. Like selectPointInteraction, the 
 * associated response may have an areaMapping that scores the response on the basis of 
 * comparing it against predefined areas but the delivery engine must not indicate these 
 * areas of the stage. Only the actual position(s) selected by the candidate shall be indicated.
 * 
 * The position object interaction must be bound to a response variable with a baseType 
 * of point and single or multiple cardinality. The point records the coordinates, 
 * with respect to the stage, of the centre point of the image being positioned.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PositionObjectInteraction extends Interaction {
    
    /**
     * From IMS QTI: 
     * 
     * The centerPoint attribute defines the point on the image being positioned 
     * that is to be treated as the centre as an offset from the top-left corner 
     * of the image in horizontal, vertical order. By default this is the centre 
     * of the image's bounding rectangle.
     * 
     * The stage on which the image is to be positioned may be shared amongst 
     * several position object interactions and is therefore defined in a class 
     * of its own: positionObjectStage.
     * 
     * @var Point
     * @qtism-bean-property
     */
    private $centerPoint = null;
    
    /**
     * From IMS QTI:
     * 
     * The maximum number of positions (on the stage) that the image can be placed.
     * If matchChoices is 0 there is no limit. If maxChoices is greater than 1 (or 0)
     * then the interaction must be bound to a response with multiple cardinality.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $maxChoices = 1;
    
    /**
     * From IMS QTI:
     * 
     * The minimum number of positions that the image must be placed to form a valid 
     * response to the interaction. If specified, minChoices must be 1 or greater 
     * but must not exceed the limit imposed by maxChoices.
     * 
     * 
     * If $minChoices is negative it means that no minChoices is specifed.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $minChoices = -1;
    
    /**
     * The image to be positioned on the stage by the candidate.
     * 
     * @var Object
     * @qtism-bean-property
     */
    private $object;
    
    /**
     * Create a new PositionObjectInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the associated response.
     * @param Object $object An image as an Object object.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If one of the argument is invalid.
     */
    public function __construct($responseIdentifier, Object $object, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $id, $class, $lang, $label);
        $this->setObject($object);
    }
    
    /**
     * Set the centerPoint attribute. Give the null value if there is no centerPoint
     * specified.
     * 
     * @param Point $centerPoint A Point object or null.
     */
    public function setCenterPoint(Point $centerPoint = null) {
        $this->centerPoint = $centerPoint;
    }
    
    /**
     * Get the centerPoint attribute. The null value is returned if there is no centerPoint
     * specified.
     * 
     * @return Point A Point object or null.
     */
    public function getCenterPoint() {
        return $this->centerPoint;
    }
    
    /**
     * Whether the PositionObjectInteraction has a centerPoint.
     * 
     * @return boolean
     */
    public function hasCenterPoint() {
        return $this->getCenterPoint() !== null;
    }
    
    /**
     * Set the maximum number of positions (on the stage) that the image can be placed.
     * 
     * @param integer $maxChoices A positive (>= 0) integer.
     * @throws InvalidArgumentException If $maxChoices is not a positive integer.
     */
    public function setMaxChoices($maxChoices) {
        if (is_int($maxChoices) === true && $maxChoices >= 0) {
            $this->maxChoices = $maxChoices;
        }
        else {
            $msg = "The 'maxChoices' argument must be a positive (>= 0) integer, '" . gettype($maxChoices) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the maximum number of positions (on the stage) that the image can be placed.
     * 
     * @return integer A positive (>= 0) integer.
     */
    public function getMaxChoices() {
        return $this->maxChoices;
    }
    
    /**
     * Set the minimum number of positions that the image must be placed to form a valid response
     * to the interaction.
     * 
     * @param integer $minChoices A strictly positive (> 0) integer that respects the limits imposed by 'maxChoices' or a negative integer to specify there is no 'minChoices'.
     * @throws InvalidArgumentException If $minChoices is not a strictly positive integer of if it does not respect the limits imposed by 'maxChoices'.
     */
    public function setMinChoices($minChoices) {
        if (is_int($minChoices) && ($minChoices > 0 || $minChoices === -1)) {
            
            if ($minChoices > $this->getMaxChoices()) {
                $msg = "The 'minChoices' argument must be less than or equal to the limits imposed by 'maxChoices'.";
                throw new InvalidArgumentException($msg);
            }
            
            $this->minChoices = $minChoices;
        } 
        else {
            $msg = "The 'minChoices' argument must be a strictly positive (> 0) integer or a negative integer, '" . gettype($minChoices) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the minimum number of positions that the image must be placed to form a valid response
     * to the interaction.
     * 
     * @return integer A strictly positive integer or a negative integer which specifies there is no 'minChoices'.
     */
    public function getMinChoices() {
        return $this->minChoices;
    }
    
    public function hasMinChoices() {
        return $this->getMinChoices() > 0;
    }
    
    /**
     * Set the image to be positioned on the stage by the candidate.
     * 
     * @param Object $object An image as an Object object.
     */
    public function setObject(Object $object) {
        $this->object = $object;
    }
    
    /**
     * Get the image to be positioned on the stage by the candidate.
     * 
     * @return Object An image as an Object object.
     */
    public function getObject() {
        return $this->object;
    }
    
    public function getComponents() {
        return new QtiComponentCollection(array($this->getObject()));
    }
    
    public function getQtiClassName() {
        return 'positionObjectInteraction';
    }
}