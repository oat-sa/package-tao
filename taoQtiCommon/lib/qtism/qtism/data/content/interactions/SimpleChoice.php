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
use \InvalidArgumentException;

class SimpleChoice extends Choice {
    
    /**
     * The components composing the simpleChoice.
     * 
     * @var FlowStaticCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new SimpleChoice object.
     * 
     * @param string $identifier The identifier of the choice.
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If one of the argument is invalid.
     */
    public function __construct($identifier, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($identifier, $id, $class, $lang, $label);
        $this->setContent(new FlowStaticCollection());
    }
    
    /**
     * Get the components composing the simpleChoice.
     * 
     * @return FlowStaticCollection A collection of FlowStatic objects.
     */
    public function getComponents() {
        return $this->getContent();
    }
    
    /**
     * Set the components composing the simpleChoice.
     * 
     * @param FlowStaticCollection $content A collection of FlowStatic objects.
     */
    public function setContent(FlowStaticCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Get the components composing the simpleChoice.
     * 
     * @return FlowStaticCollection
     */
    public function getContent() {
        return $this->content;
    }
    
    public function getQtiClassName() {
        return 'simpleChoice';
    }
}