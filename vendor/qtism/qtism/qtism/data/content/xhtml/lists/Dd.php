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

namespace qtism\data\content\xhtml\lists;

use qtism\data\content\FlowCollection;
use \InvalidArgumentException;

/**
 * The XHTML dd class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Dd extends DlElement {
    
    /**
     * The Flow objects composing the Dd.
     * 
     * @var FlowCollection
     * @qtism-bean-property
     */
    private $content;
    
    /**
     * Create a new Dd object.
     * 
     * @param string $id The id of the bodyElement.
     * @param string $class The class of the bodyElement.
     * @param string $lang The language of the bodyElement.
     * @param string $label The label of the bodyElement.
     * @throws InvalidArgumentException If one of the arguments is invalid.
     */
    public function __construct($id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setContent(new FlowCollection());
    } 
    
    /**
     * Set the Flow objects composing the Dd.
     * 
     * @param FlowCollection $content A collection of Flow objects.
     */
    public function setContent(FlowCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Set the Flow objects composing the Dd.
     * 
     * @return FlowCollection
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * Get the Flow objects composing the Dd.
     * 
     * @return FlowCollection A collection of Flow objects.
     */
    public function getComponents() {
        return $this->getContent();
    }
    
    public function getQtiClassName() {
        return 'dd';
    }
}