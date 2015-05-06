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

namespace qtism\data\content;

/**
 * The simpleBlock QTI abstract class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class SimpleBlock extends BodyElement implements BlockStatic, FlowStatic {
    
    /**
     * The base URI.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $xmlBase = '';
    
    /**
     * The Block components composing the SimpleBlock object.
     * 
     * @var BlockCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new SimpleBlock object.
     * 
     * @param string $id A QTI identifier.
     * @param string $class One or more class names separated by spaces.
     * @param string $lang An RFC3066 language.
     * @param string $label A label that does not exceed 256 characters.
     */
    public function __construct($id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setContent(new BlockCollection());
    }
    
    /**
     * Get the collection of Block objects composing the Simpleblock.
     * 
     * @return BlockCollection A collection of Block objects.
     */
    public function getComponents() {
        return $this->getContent();
    }
    
    /**
     * Set the collection of Block objects composing the SimpleBlock.
     * 
     * @param BlockCollection $content A collection of Block objects.
     */
    public function setContent(BlockCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Get the content of Block objects composing the Simpleblock.
     * 
     * @return BlockCollection
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * Set the base URI of the SimpleBlock.
     *
     * @param string $xmlBase A URI.
     * @throws InvalidArgumentException if $base is not a valid URI nor an empty string.
     */
    public function setXmlBase($xmlBase = '') {
        if (is_string($xmlBase) && (empty($xmlBase) || Format::isUri($xmlBase))) {
            $this->xmlBase = $xmlBase;
        }
        else {
            $msg = "The 'base' argument must be an empty string or a valid URI, '" . $xmlBase . "' given";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the base URI of the SimpleBlock.
     *
     * @return string An empty string or a URI.
     */
    public function getXmlBase() {
        return $this->xmlBase;
    }
    
    public function hasXmlBase() {
        return $this->getXmlBase() !== '';
    }
}