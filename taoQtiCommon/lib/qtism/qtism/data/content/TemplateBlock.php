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

use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The templateBlock QTI class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TemplateBlock extends TemplateElement implements FlowStatic, BlockStatic {
    
    /**
     * The base URI.
     *
     * @var string
     * @qtism-bean-property
     */
    private $xmlBase = '';
    
    /**
     * The content of the TemplateBlock.
     * 
     * @var FlowStaticCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new TemplateBlock object.
     * 
     * @param string $identifier The identifier of the templateBlock.
     * @param string $templateIdentifier The identifier of the associated template variable.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If any of the argument is invalid.
     */
    public function __construct($identifier, $templateIdentifier, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($identifier, $templateIdentifier, $id, $class, $lang, $label);
        $this->setContent(new FlowStaticCollection());
    }
    
    /**
     * Set the content of the templateBlock.
     * 
     * @param FlowStaticCollection $content A collection of BlockStatic objects.
     */
    public function setContent(FlowStaticCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Get the content of the templateBlock.
     * 
     * @return FlowStaticCollection A collection of BlockStatic objects.
     */
    public function getContent() {
        return $this->content;
    }
    
    public function getComponents() {
        return $this->getContent();
    }
    
    public function getQtiClassName() {
        return 'templateBlock';
    }
    
    /**
     * Set the base URI of the TemplateBlock.
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