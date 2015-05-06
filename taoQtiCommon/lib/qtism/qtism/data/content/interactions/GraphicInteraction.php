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
use \InvalidArgumentException;

/**
 * The QTI graphicInteraction class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class GraphicInteraction extends BlockInteraction {
    
    /**
     * From IMS QTI:
     * 
     * Each graphical interaction has an associated image which is given as an 
     * object that must be of an image type, as specified by the type attribute.
     * 
     * @var Object
     * @qtism-bean-property
     */
    private $object;
    
    
    /**
     * Create a GraphicInteraction object.
     * 
     * @param string $responseIdentifier The identifier of the associated response.
     * @param Object $object The associated image as an Object object.
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
     * Set the associated image given as an object.
     * 
     * @param Object $object An Object object.
     */
    public function setObject(Object $object) {
        $this->object = $object;
    }
    
    /**
     * Get the associated image given as an object.
     * 
     * @return Object An Object object.
     */
    public function getObject() {
        return $this->object;
    }
    
}