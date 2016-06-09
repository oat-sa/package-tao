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

use qtism\data\content\xhtml\Object;
use qtism\data\QtiComponentCollection;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The drawing interaction allows the candidate to use a common set of drawing tools
 * to modify a given graphical image (the canvas). It must be bound to a response 
 * variable with base-type file and single cardinality. The result is a file in the 
 * same format as the original image.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class DrawingInteraction extends BlockInteraction {
    
    /**
     * From IMS QTI:
     * 
     * The image that acts as the canvas on which the drawing takes 
     * place is given as an object which must be of an image type, as 
     * specified by the type attribute.
     * 
     * @var Object
     * @qtism-bean-property
     */
    private $object;
    
    /**
     * Create a new DrawingInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the associated response variable.
     * @param Object $object The image that acts as a canvas for drawing.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If any argument is invalid.
     */
    public function __construct($responseIdentifier, Object $object, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $id, $class, $lang, $label);
        $this->setObject($object);
    }
    
    /**
     * Set the image that acts as a canvas for drawing.
     * 
     * @param Object $object An Object object representing an image.
     */
    public function setObject(Object $object) {
        $this->object = $object;
    }
    
    /**
     * Get the image that acts as a canvas for drawing.
     * 
     * @return Object An Object object representing an image.
     */
    public function getObject() {
        return $this->object;
    }
    
    public function getComponents() {
        $parentComponents = parent::getComponents();
        return new QtiComponentCollection(array_merge($parentComponents->getArrayCopy(), array($this->getObject())));
    }
    
    public function getQtiClassName() {
        return 'drawingInteraction';
    }
}