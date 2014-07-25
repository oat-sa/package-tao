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

use qtism\data\content\BodyElement;
use \InvalidArgumentException;

/**
 * The QTI simpleInline abstract class which contains inline QTI components
 * only.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class SimpleInline extends BodyElement implements FlowStatic, InlineStatic {
    
    /**
     * The Base URI.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $xmlBase = '';
    
    /**
     * The Inline components contained within the SimpleInline.
     * 
     * @var InlineCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new SimpleInline object.
     * 
     * @param string $id A QTI identifier.
     * @param string $class One or more class names separated by spaces.
     * @param string $lang An RFC3066 language.
     * @param string $label A label that does not exceed 256 characters.
     * @throws InvalidArgumentException If any of the arguments is invalid.
     */
    public function __construct($id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setContent(new InlineCollection());
    }
    
    /**
     * Get the Inline components contained by the SimpleInline object.
     * 
     * @return InlineCollection A collection of Inline components.
     */
    public function getComponents() {
        return $this->getContent();
    }
    
    /**
     * Set the inline components contained by the SimpleInline object.
     * 
     * @param QtiComponentCollection $content A collection of Inline components.
     */
    public function setContent(InlineCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Get the inline components contained by the SimpleInline object.
     * 
     * @return InlineCollection
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * Set the base URI of the SimpleInline.
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
     * Get the base URI of the SimpleInline.
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