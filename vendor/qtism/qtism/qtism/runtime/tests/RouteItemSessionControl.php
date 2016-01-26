<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */

namespace qtism\runtime\tests;

use qtism\data\QtiComponent;
use qtism\data\ItemSessionControl;

/**
 * Represents the ItemSessionControl in force in the context of a RouteItem.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RouteItemSessionControl {
    
    /**
     * The owner of the ItemSessionControl component.
     * 
     * @var QtiComponent
     */
    private $owner;
    
    /**
     * The encapsulated ItemSessionControl object.
     * 
     * @var ItemSessionControl
     */
    private $itemSessionControl;
    
    /**
     * Create a new RouteItemSessionControl object.
     * 
     * @param ItemSessionControl $itemSessionControl The encapsulated ItemSessionControl object.
     * @param QtiComponent $owner The owner of the ItemSessionControl component.
     */
    public function __construct(ItemSessionControl $itemSessionControl, QtiComponent $owner) {
        $this->setItemSessionControl($itemSessionControl);
        $this->setOwner($owner);
    }
    
    /**
     * Get the owner component of the ItemSessionControl.
     * 
     * @return QtiComponent A QtiComponent object.
     */
    public function getOwner() {
        return $this->owner;
    }
    
    /**
     * Set the owner component of the ItemSessionControl.
     * 
     * @param QtiComponent $owner A QtiComponent object.
     */
    public function setOwner(QtiComponent $owner) {
        $this->owner = $owner;
    }
    
    /**
     * Get the encapsulated ItemSessionControl object.
     * 
     * @return ItemSessionControl
     */
    public function getItemSessionControl() {
        return $this->itemSessionControl;
    }
    
    /**
     * Set the encapsulated ItemSessionControl object.
     * 
     * @param ItemSessionControl $itemSessionControl
     */
    public function setItemSessionControl(ItemSessionControl $itemSessionControl) {
        $this->itemSessionControl = $itemSessionControl;
    }
    
    /**
     * Create a new RouteItemSessionControl object from an existing $itemSessionControl object with a given
     * $owner.
     * 
     * @param ItemSessionControl $itemSessionControl An existing ItemSessionControl object.
     * @param QtiComponent $owner The owner of the ItemSessionControl object.
     * @return RouteItemSessionControl A new RouteItemSessionControl object.
     */
    public static function createFromItemSessionControl(ItemSessionControl $itemSessionControl, QtiComponent $owner) {
        return new static($itemSessionControl, $owner);
    }
}