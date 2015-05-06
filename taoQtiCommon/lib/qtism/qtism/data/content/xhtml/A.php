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

namespace qtism\data\content\xhtml;

use qtism\common\utils\Format;
use qtism\data\content\SimpleInline;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * Although a inherits from simpleInline it must not contain, either directly or 
 * indirectly, another a.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class A extends SimpleInline {
    
    /**
     * The href attribute.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $href;
    
    /**
     * The type attribute (mime-type).
     * 
     * @var string
     * @qtism-bean-property
     */
    private $type = '';
    
    /**
     * Create a new A object.
     * 
     * @param string $href The href attribute.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     */
    public function __construct($href, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setHref($href);
        $this->setType('');
    }
    
    /**
     * Set the href attribute.
     * 
     * @param string $href A URI (Uniform Resource Identifier).
     * @throws InvalidArgumentException If $href is not a URI.
     */
    public function setHref($href) {
        if (Format::isUri($href) === true) {
            $this->href = $href;
        }
        else {
            $msg = "The 'href' argument must be a URI, '" . $href . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the href attribute.
     * 
     * @return string A URI (Uniform Resource Identifier).
     */
    public function getHref() {
        return $this->href;
    }
    
    /**
     * Set the type attribute (mime-type). Give an empty string
     * to indicate there is no mime-type.
     * 
     * @param string $type A mime-type.
     * @throws InvalidArgumentException If $type is not a string value.
     */
    public function setType($type) {
        if (is_string($type) === true) {
            $this->type = $type;
        }
        else {
            $msg = "The 'type' argument must be a non-empty string representing a mime-type, '" . gettype($type) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the type attribute (mime-type). Returns an empty string
     * if no type is indicated.
     * 
     * @return string A mime-type.
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * Whether a value is defined for the type attribute.
     * 
     * @return boolean
     */
    public function hasType() {
        return $this->getType() !== '';
    }
    
    public function getQtiClassName() {
        return 'a';
    }
}