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
 * 
 * The atomicBlock QTI class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
abstract class AtomicBlock extends BodyElement implements BlockStatic, FlowStatic {
    
    /**
     * The base URI of the AtomicBlock.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $xmlBase = '';
    
    /**
     * The collection of Inline components contained by the AtomicBlock.
     * 
     * @var InlineCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new AtomicBlock object.
     *
     * @param string $id A QTI identifier.
     * @param string $class One or more class names separated by spaces.
     * @param string $lang An RFC3066 language.
     * @param string $label A label that does not exceed 256 characters.
     */
    public function __construct($id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setContent(new InlineCollection());
    }
    
    /**
     * Set the collection of Inline components composing the AtomicBlock.
     * 
     * @param InlineCollection $content A collection of Inline objects.
     */
    public function setContent(InlineCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Get the collection of Inline components composing the AtomicBlock.
     * 
     * @return InlineCollection
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * Get the collection of Inline components composing the AtomicBlock.
     * 
     * @return InlineCollection A collection of Inline objects.
     */
    public function getComponents() {
        return $this->getContent();
    }
    
    /**
     * Set the base URI of the AtomicBlock.
     *
     * @param string $xmlBase A URI.
     * @throws InvalidArgumentException if $base is not a valid URI nor an empty string.
     */
    public function setXmlBase($xmlBase = '') {
        if (is_string($xmlBase) && (empty($xmlBase) || Format::isUri($xmlBase))) {
            $this->xmlBase = $xmlBase;
        }
        else {
            $msg = "The 'xmlBase' argument must be an empty string or a valid URI, '" . $xmlBase . "' given";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the base URI of the AtomicBlock.
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