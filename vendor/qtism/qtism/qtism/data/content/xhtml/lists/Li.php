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
use qtism\data\content\BodyElement;
use \InvalidArgumentException;

/**
 * The XHTML li class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Li extends BodyElement {
    
    /**
     * The Flow objects composing the Li.
     * 
     * @var FlowCollection
     * @qtism-bean-property
     */
    private $content;
    
    public function __construct($id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($id, $class, $lang, $label);
        $this->setContent(new FlowCollection());
    }
    
    /**
     * Get the Flow objects composing the Li.
     * 
     * @return FlowCollection A collection of Flow objects.
     */
    public function getComponents() {
        return $this->getContent();
    }
    
    /**
     * Set the Flow objects composing the Li.
     * 
     * @param FlowCollection $content
     */
    public function setContent(FlowCollection $content) {
        $this->content = $content;
    }
    
    /**
     * Get the Flow objects composing the Li.
     * 
     * @return FlowCollection
     */
    public function getContent() {
        return $this->content;
    }
    
    public function getQtiClassName() {
        return 'li';
    }
}