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
 * The item body contains the text, graphics, media objects and interactions that 
 * describe the item's content and information about how it is structured. The body 
 * is presented by combining it with stylesheet information, either explicitly or 
 * implicitly using the default style rules of the delivery or authoring system.
 * 
 * The body must be presented to the candidate when the associated item session 
 * is in the interacting state. In this state, the candidate must be able to 
 * interact with each of the visible interactions and therefore set or update 
 * the values of the associated response variables. The body may be presented 
 * to the candidate when the item session is in the closed or review state. 
 * In these states, although the candidate's responses should be visible, 
 * the interactions must be disabled so as to prevent the candidate from 
 * setting or updating the values of the associated response variables. 
 * Finally, the body may be presented to the candidate in the solution 
 * state, in which case the correct values of the response variables must 
 * be visible and the associated interactions disabled.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ItemBody extends BodyElement {
    
    /**
     * The blocks composing the itemBody.
     * 
     * @var BlockCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new ItemBody object.
     * 
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If one of the arguments is invalid.
     */
    public function __construct($id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setContent(new BlockCollection());
    }
    
    /**
     * Set the Block objects composing the ItemBody.
     * 
     * @param BlockCollection $content The collection of blocks composing the itemBody.
     */
    public function setContent(BlockCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Get the Block objects the ItemBody.
     * 
     * @return BlockCollection
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * Get the Block objects composing the ItemBody.
     * 
     * @return BlockCollection A collection of Block objects.
     */
    public function getComponents() {
        return $this->getContent();
    }
    
    public function getQtiClassName() {
        return 'itemBody';
    }
}