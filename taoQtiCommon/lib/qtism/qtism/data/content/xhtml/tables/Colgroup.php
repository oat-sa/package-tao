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

use qtism\data\content\BodyElement;
use \InvalidArgumentException;

/**
 * The XHTML colgroup class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Colgroup extends BodyElement {
    
    /**
     * The collection of Col objects composing
     * the Colgroup.
     * 
     * @var ColCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * The span attribute.
     * 
     * @var integer
     * @qtism-bean-property
     */
    private $span = 1;
    
    /**
     * Create a new Colgroup object.
     * 
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If one of the arguments is invalid.
     */
    public function __construct($id = '', $class = '' , $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setContent(new ColCollection());
        $this->setSpan(1);
    }
    
    /**
     * Set the value for the span attribute.
     * 
     * @param integer $span A strictly positive (> 0) integer.
     * @throws InvalidArgumentException If $span is not a strictly positive integer.
     */
    public function setSpan($span) {
        if (is_int($span) && $span > 0) {
            $this->span = $span;
        }
        else {
            $msg = "The 'span' argument must be a strictly positive (> 0) integer, '" . gettype($span) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the value for the span attribute.
     * 
     * @return integer A strictly positive (> 0) integer.
     */
    public function getSpan() {
        return $this->span;
    }
    
    /**
     * Get the collection of Col objects composing the Colgroup.
     * 
     * @return ColCollection A collection of Col objects.
     */
    public function getComponents() {
        return $this->getContent();
    }
    
    /**
     * Set the collection of Col objects composing the ColGroup.
     * 
     * @param ColCollection $content A collection of Col objects.
     */
    public function setContent(ColCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Get the collection of Col objects composing the ColGroup.
     * 
     * @return ColCollection
     */
    public function getContent() {
        return $this->content;
    }
    
    public function getQtiClassName() {
        return 'colgroup';
    }
}