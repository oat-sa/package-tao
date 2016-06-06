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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use qtism\data\IAssessmentItem;

/**
 * The VariableFiller provides a way to fill a QtiSm Runtime Variable with
 * a value coming from the client side. These values are always transmitted as
 * plain strings. However, we need a way to transform these string values as
 * QtiSm compliant variable values. This is the goal of this class.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class taoQtiCommon_helpers_AbstractVariableFiller {

    /**
     * The description of the item the variables belong to.
     *
     * @var IAssessmentItem
     */
    private $itemRef;

    /**
     * Create a new VariableFiller object.
     *
     * @param IAssessmentItem $itemRef The item the variables you want to fill belong to.
     */
    public function __construct(IAssessmentItem $itemRef) {
        $this->setItemRef($itemRef);
    }

    /**
     * Get the item reference the variables you want to fill belong to.
     *
     * @return IAssessmentItem An ExtendedAssessmentItemRef object.
     */
    protected function getItemRef() {
        return $this->itemRef;
    }

    /**
     * Set the item reference the variables you want to fill belong to.
     *
     * @param IAssessmentItem $itemRef An IAssessmentItem object.
     */
    protected function setItemRef(IAssessmentItem $itemRef) {
        $this->itemRef = $itemRef;
    }

    /**
     * Fill the variable $variableName with a correctly transformed $clientSideValue.
     *
     * @param string $variableName The variable identifier you want to fill.
     * @param mixed $clientSideValue The value received from the client-side representing the value of the variable with identifier $variableName.
     * @return Variable A Variable object filled with a correctly transformed $clientSideValue.
     * @throws OutOfBoundsException If no variable with $variableName is described in the item.
     * @throws OutOfRangeException If the $clientSideValue does not fit the target variable's baseType.
     */
    abstract public function fill($variableName, $clientSideValue);
}