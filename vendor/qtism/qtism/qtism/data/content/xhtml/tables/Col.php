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

namespace qtism\data\content\xhtml\tables;

use qtism\data\QtiComponentCollection;

use qtism\data\content\BodyElement;
use \InvalidArgumentException;

/**
 * The col XHTML class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Col extends BodyElement {
    
    /**
     * The span attribute.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $span = 1;
    
    /**
     * Create a new Col object.
     * 
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The language of the bodyElement.
     * @throws InvalidArgumentException If one of the argument is invalid.
     */
    public function __construct($id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setSpan(1);
    }
    
    /**
     * Set the span attribute.
     * 
     * @param integer $span A strictly positive integer.
     * @throws InvalidArgumentException If $span is not a positive integer.
     */
    public function setSpan($span) {
        if (is_int($span) === true && $span > 0) {
            $this->span = $span;
        }
        else {
            $msg = "The 'span' attribute must be a strictly positive (> 0) integer, '" . gettype($span) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the span attribute.
     * 
     * @return integer A strictly positive integer.
     */
    public function getSpan() {
        return $this->span;
    }
    
    public function getComponents() {
        return new QtiComponentCollection();
    }
    
    public function getQtiClassName() {
        return 'col';
    }
}