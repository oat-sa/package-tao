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

use qtism\data\content\FlowStaticCollection;
use qtism\data\content\BodyElement;
use \InvalidArgumentException;

/**
 * The prompt QTI class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Prompt extends BodyElement {
    
    /**
     * From IMS QTI:
     * 
     * A prompt must not contain any nested interactions.
     * 
     * @var FlowStaticCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new Prompt object.
     * 
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If any of the arguments is invalid.
     */
    public function __construct($id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setContent(new FlowStaticCollection());
    } 
    
    /**
     * Set the content of the prompt.
     * 
     * @param FlowStaticCollection $content A collection of FlowStatic objects.
     */
    public function setContent(FlowStaticCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Get the content of the prompt.
     * 
     * @return FlowStaticCollection A collection of FlowStatic objects.
     */
    public function getContent() {
        return $this->content;
    }
    
    public function getComponents() {
        return $this->getContent();
    }
    
    public function getQtiClassName() {
        return 'prompt';
    }
}