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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\common\datatypes\files\FileSystemFileManager;
use qtism\data\IAssessmentItem;
use qtism\data\state\ResponseDeclaration;
use qtism\data\state\OutcomeDeclaration;
use qtism\runtime\pci\json\Unmarshaller as PciJsonUnmarshaller;
use qtism\runtime\pci\json\UnmarshallingException as PciJsonUnmarshallingException;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\common\OrderedContainer;

/**
 * The PciVariableFiller aims at providing the necessary tools to client-code that enables
 * to fill variables of a given item with values coming in a PCI JSON representation.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiCommon_helpers_PciVariableFiller extends taoQtiCommon_helpers_AbstractVariableFiller {

    /**
     * Create a new PciVariableFiller object.
     *
     * @param IAssessmentItem $itemRef The item the variables to deal with belong to.
     */
    public function __construct(IAssessmentItem $itemRef) {
        parent::__construct($itemRef);
    }

    /**
     * Fill the variable $variableName with a correctly transformed $clientSideValue.
     *
     * @param string $variableName The variable identifier you want to fill.
     * @param array $clientSideValue The value received from the client-side representing the value of the variable with identifier $variableName.
     * @return Variable A Variable object filled with a correctly transformed $clientSideValue.
     * @throws OutOfBoundsException If no variable with $variableName is described in the item.
     * @throws OutOfRangeException If the $clientSideValue does not fit the target variable's baseType.
     */
    public function fill($variableName, $clientSideValue) {
        $variableDeclaration = $this->findVariableDeclaration($variableName);

        if ($variableDeclaration === false) {
            $itemId = $this->getItemRef()->getIdentifier();
            $msg = "Variable declaration with identifier '${variableName}' not found in item '${itemId}'.";
            throw new \OutOfBoundsException($msg);
        }

        // Create Runtime Variable from Data Model.
        $runtimeVar = ($variableDeclaration instanceof ResponseDeclaration) ? ResponseVariable::createFromDataModel($variableDeclaration) : OutcomeVariable::createFromDataModel($variableDeclaration);

        // Set the data into the runtime variable thanks to the PCI JSON Unmarshaller
        // from QTISM.
        try {
            $unmarshaller = new PciJsonUnmarshaller(taoQtiCommon_helpers_Utils::getFileDatatypeManager());
            $value = $unmarshaller->unmarshall($clientSideValue);

            // Dev's note:
            // The PCI JSON Representation format does make the difference between multiple and ordered containers.
            // We then have to juggle a bit if the target variable has ordered cardinality.
            if ($value !== null && $value->getCardinality() === Cardinality::MULTIPLE && $variableDeclaration->getCardinality() === Cardinality::ORDERED) {
                $value = new OrderedContainer($value->getBaseType(), $value->getArrayCopy());
            }

            $runtimeVar->setValue($value);
        }
        catch (PciJsonUnmarshallingException $e) {
            $strClientSideValue = mb_substr(var_export($clientSideValue, true), 0, 50, TAO_DEFAULT_ENCODING);
            $msg = "Unable to put value '${strClientSideValue}' into variable '${variableName}'.";
            throw new \OutOfRangeException($msg, 0, $e);
        }

        return $runtimeVar;
    }

    /**
     * Get the OutcomeDeclaration/ResponseDeclaration with identifier $variableIdentifier from
     * the item.
     *
     * @param string $variableIdentifier A QTI identifier.
     * @return \qtism\data\state\VariableDeclaration|boolean The variable with identifier $variableIdentifier or false if it could not be found.
     */
    protected function findVariableDeclaration($variableIdentifier) {
        $responseDeclarations = $this->getItemRef()->getResponseDeclarations();

        if (isset($responseDeclarations[$variableIdentifier]) === true) {
            return $responseDeclarations[$variableIdentifier];
        }
        else {
            $outcomeDeclarations = $this->getItemRef()->getOutcomeDeclarations();

            if (isset($outcomeDeclarations[$variableIdentifier]) === true) {
                return $outcomeDeclarations[$variableIdentifier];
            }
            else {
                // Variable $variableIdentifier not found.
                return false;
            }
        }
    }
}