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

use qtism\data\QtiComponent;

/**
 * The simpleMatchSet QTI class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SimpleMatchSet extends QtiComponent {
    
    /**
     * From IMS QTI:
     * 
     * An ordered set of choices for the set.
     * 
     * @var SimpleAssociableChoiceCollection
     * @qtism-bean-property
     */
    private $simpleAssociableChoices;
    
    /**
     * Create a new SimpleMatchSet object.
     * 
     * @param SimpleAssociableChoiceCollection $simpleAssociableChoices The ordered set of choices for the set.
     */
    public function __construct(SimpleAssociableChoiceCollection $simpleAssociableChoices = null) {
        $this->setSimpleAssociableChoices((is_null($simpleAssociableChoices) === true) ? new SimpleAssociableChoiceCollection() : $simpleAssociableChoices);
    }
    
    /**
     * Set the ordered set of choices for the set.
     * 
     * @param SimpleAssociableChoiceCollection $simpleAssociableChoices
     */
    public function setSimpleAssociableChoices(SimpleAssociableChoiceCollection $simpleAssociableChoices) {
        $this->simpleAssociableChoices = $simpleAssociableChoices;
    }
    
    /**
     * Get the ordered set of choices for the set.
     * 
     * @return SimpleAssociableChoiceCollection
     */
    public function getSimpleAssociableChoices() {
        return $this->simpleAssociableChoices;
    }
    
    public function getComponents() {
        return $this->getSimpleAssociableChoices();
    }
    
    public function getQtiClassName() {
        return 'simpleMatchSet';
    }
}