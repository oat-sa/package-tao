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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoCe\scripts\update;

use \core_kernel_classes_Resource;
use \common_ext_ExtensionsManager;
use \common_Logger;

/**
 * TAO Community Edition Updater.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class Updater extends \common_ext_ExtensionUpdater
{
    
    const RDFTYPEPROPERTY = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type';
    const GENERISTRUEINSTANCEURI = 'http://www.tao.lu/Ontologies/generis.rdf#True';
    const DELIVERYRESULTCLASSURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#DeliveryResult';
    const DELIVERYIDENTIFIERPROPERTYURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#Identifier';
    const RESULTOFSUBJECTPROPERTYURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#resultOfSubject';
    const RESULTOFDELIVERYPROPERTYURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#resultOfDelivery';
    const ITEMRESULTCLASSURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#ItemResult';
    const ITEMIDENTIFIERPROPERTYURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#Identifier';
    const RELATEDITEMPROPERTYURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#RelatedItem';
    const RELATEDTESTPROPERTYURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#RelatedTest';
    const RELATEDDELIVERYRESULTURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#relatedDeliveryResult';
    const VARIABLECLASSURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#Variable';
    const RESPONSEVARIABLECLASSURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#ResponseVariable';
    const OUTCOMEVARIABLECLASSURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#OutcomeVariable';
    const VARIABLEIDENTIFIERPROPERTYURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#Identifier';
    const VARIABLECARDINALITYPROPERTYURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#cardinality';
    const VARIABLEBASETYPEPROPERTYURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#baseType';
    const VARIABLECORRECTRESPONSEPROPERTYURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#correctResponse';
    const VARIABLEVALUEPROPERTYURI = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value';
    const VARIABLEPOCHPROPERTYURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#variableEpoch';
    const VARIABLENORMALMAXIMUMPROPERTYURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#normalMaximum';
    const VARIABLENORMALMINIMUMPROPERTYURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#normalMinimum';
    const RELATEDITEMRESULTPROPERTYURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#relatedItemResult';
    const RDFRESULTSERVERINSTANCEURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#taoResultServer';
    const RDFRESULTSERVERMODELINSTANCEURI = 'http://www.tao.lu/Ontologies/TAOResult.rdf#taoResultServerModel';
    const DELIVERYCLASSURI = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery';
    const DELIVERYRESULTSERVERPROPERTYURI = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer';
    const RDSRESULTSERVERINSTANCEURI = 'http://www.tao.lu/Ontologies/taoOutcomeRds.rdf#RdsResultStorage';
    
    /**
     * Perform update from $currentVersion to $versionUpdatedTo.
     * 
     * @param string $currentVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) 
    {
        
        $currentVersion = $initialVersion;

        // migrate from 1.0.0 to 1.1.0
        // old taoResults extension gets uninistalled and taoOutcomeRds becomes
        // the new default result storage mechanism.
        if ($currentVersion == '1.0.0') {
            self::migrateFrom100To110();
            $currentVersion = '1.1.0';
        }
        
        // migrate from 1.1.0 to 1.1.1
        // RDF based result server removal and assignment of deliveries
        // to RDS implementation.
        if ($currentVersion == '1.1.0') {
            self::migrateFrom110to111();
            $currentVersion = '1.1.1';
        }
        
        return $currentVersion;
    }
    
    /**
     * Implementation of migration from taoCe 1.0.0 to 1.1.0.
     * 
     * Migrating from 1.0.0 to 1.1.0 consists of transforming
     * old RDF based results into RDBMS based results using
     * the 'taoOutcomeRds' extension.
     */
    static private function migrateFrom100To110()
    {
        // 1. Unregister old taoResults extension.
        $extManager = common_ext_ExtensionsManager::singleton();
        
        if ($extManager->isInstalled('taoResults') === true) {
            // The extension cannot be trully uninistalled
            // because it is missing the 'uninstall' entry
            // in its manifest.
            common_Logger::i("Unregistering extension 'taoResults'...");
        
            $taoResults = $extManager->getExtensionById('taoResults');
            $extManager->unregisterExtension($taoResults);
        
            common_Logger::i("Extension 'taoResults' unregistered.");
        }
        
        // 2. Migrate results to outcomeRds.
        common_Logger::i("Migrating old 'taoResults' result data to 'taoOutcomeRds'...");
        self::resultsMigration();
        common_Logger::i("Migration of old 'taoResults' result data to 'taoOutcomeRds' done.");
    }
    
    /**
     * Implementation of migration from taoCe 1.1.0 to 1.1.1.
     * 
     * Migrating from 1.1.0 to 1.1.1 consists of removing old
     * RDF based result servers/models and re-assign existing
     * deliveries to the 'taoOutcomeRds' result server implementation.
     */
    static private function migrateFrom110to111()
    {
        // 1. Remove old references from outcomeRdf.
        common_Logger::i("Disabling old Result Server Implementations...");
        self::disableRdfResultServers();
        common_Logger::i("Old Result Server Implementations disabled.");
        
        // 2. Assigning existing Deliveries to OutcomeRds Result Server.
        common_Logger::i("Assigning Deliveries to OutcomeRds Result Server.");
        self::assignDeliveriesToRds();
        common_Logger::i("Deliveries assigned to OutcomeRds Result Server.");
    }
    
    /**
     * Implementation of old 'taoResults' results to 'taoOutcomeRds' results.
     */
    static private function resultsMigration()
    {
        $outcomeRds = call_user_func(array('oat\taoOutcomeRds\model\RdsResultStorage', 'singleton'));
        
        $deliveryResultClass = new \core_kernel_classes_Class(self::DELIVERYRESULTCLASSURI);
        $itemResultClass = new \core_kernel_classes_Class(self::ITEMRESULTCLASSURI);
        $variableClass = new \core_kernel_classes_Class(self::VARIABLECLASSURI);
        
        // Retrieve all Delivery Results. Batch by 1.
        $batch = 1;
        $offset = 0;
        $limit = $batch;
        
        $deliveryResults = $deliveryResultClass->getInstances(false, array('offset' => $offset, 'limit' => $limit));
        
        while (count($deliveryResults) > 0) {
        
            common_Logger::i("- Migrating Delivery Results from offset ${offset} to limit ${limit}...");
        
            foreach ($deliveryResults as $deliveryResultUri => $deliveryResult) {
                $properties = array(
                                self::DELIVERYIDENTIFIERPROPERTYURI,
                                self::RESULTOFSUBJECTPROPERTYURI,
                                self::RESULTOFDELIVERYPROPERTYURI
                );
        
                $deliveryResultValues = $deliveryResult->getPropertiesValues($properties);
        
                $noIdentifier = empty($deliveryResultValues[self::DELIVERYIDENTIFIERPROPERTYURI]);
                $noSubject = empty($deliveryResultValues[self::RESULTOFSUBJECTPROPERTYURI]);
                $noDelivery = empty($deliveryResultValues[self::RESULTOFDELIVERYPROPERTYURI]);
        
                if ($noIdentifier === false && $noSubject === false && $noDelivery === false) {
        
                    $newResultIdentifier = current($deliveryResultValues[self::DELIVERYIDENTIFIERPROPERTYURI])->getUri();
                    $newResultSubject = current($deliveryResultValues[self::RESULTOFSUBJECTPROPERTYURI])->getUri();
                    $newResultDelivery = current($deliveryResultValues[self::RESULTOFDELIVERYPROPERTYURI])->getUri();
        
                    common_Logger::i("-- Migrating Delivery Result with Identifier '${newResultIdentifier}'...");
        
                    // We have a Delivery Result to migrate.
        
                    $outcomeRds->storeRelatedDelivery($newResultIdentifier, $newResultDelivery);
                    $outcomeRds->storeRelatedTestTaker($newResultIdentifier, $newResultSubject);
        
                    // Retrieve all Item Results related to DeliveryResult.
                    $itemResultsPropertyFilters = array(self::RELATEDDELIVERYRESULTURI => $deliveryResultUri);
                    $itemResultsOptions = array('recursive' => false, 'like' => false);
                    $itemResults = $itemResultClass->searchInstances($itemResultsPropertyFilters, $itemResultsOptions);
        
                    foreach ($itemResults as $itemResultUri => $itemResult) {
        
                        $itemResultProperties = array(
                                        self::ITEMIDENTIFIERPROPERTYURI,
                                        self::RELATEDITEMPROPERTYURI,
                                        self::RELATEDTESTPROPERTYURI
                        );
        
                        $itemResultValues = $itemResult->getPropertiesValues($itemResultProperties);
        
                        $noIdentifier = empty($itemResultValues[self::ITEMIDENTIFIERPROPERTYURI]);
                        $noRelatedItem = empty($itemResultValues[self::RELATEDITEMPROPERTYURI]);
                        $noRelatedTest = empty($itemResultValues[self::RELATEDTESTPROPERTYURI]);
        
                        if ($noIdentifier === false && $noRelatedItem === false && $noRelatedTest === false) {
        
                            $newItemResultIdentifier = current($itemResultValues[self::ITEMIDENTIFIERPROPERTYURI]);
                            $newItemResultRelatedItem = current($itemResultValues[self::RELATEDITEMPROPERTYURI])->getUri();
                            $newItemResultRelatedTest = current($itemResultValues[self::RELATEDTESTPROPERTYURI])->getUri();
        
                            common_Logger::i("--- Migrating Item Result with Identifier '${newItemResultIdentifier}'...");
        
                            // Get all Variables related to this Item Result.
                            $variablePropertyFilters = array(self::RELATEDITEMRESULTPROPERTYURI => $itemResultUri);
                            $variableOptions = array('recursive' => true, 'like' => false);
                            $variables = $variableClass->searchInstances($variablePropertyFilters, $variableOptions);
        
                            foreach ($variables as $variableUri => $variable) {
        
                                $newVariable = self::createVariableObject($variable);
                                if ($newVariable !== false) {
                                    $outcomeRds->storeItemVariable($newResultIdentifier, $newItemResultRelatedTest, $newItemResultRelatedItem, $newVariable, $newItemResultIdentifier);
                                }
                            }
        
                        } else {
                            common_Logger::i("Skipping Item Result Result with URI '${itemResultUri}'. Malformed Item Result.");
                        }
                    }    
        
                    // Now let's find test Variables.
                    $variablePropertyFilters = array(self::RELATEDDELIVERYRESULTURI => $deliveryResultUri);
                    $variableOptions = array('recursive' => true, 'like' => false);
                    $variables = $variableClass->searchInstances($variablePropertyFilters, $variableOptions);
        
                    // If we can infer the related test...
                    if (isset($newItemResultRelatedTest) === true) {
                    
                        foreach ($variables as $variableUri => $variable) {
                            $newVariable = self::createVariableObject($variable);
                            if ($newVariable !== false) {
                                $outcomeRds->storeTestVariable($newResultIdentifier, $newItemResultRelatedTest, $newVariable, $newResultIdentifier);
                            }
                        }
                    }
        
                } else {
                    common_Logger::i("Skipping Delivery Result with URI '${deliveryResultUri}'. Malformed Delivery Result.");
                }
            }
        
            // Retrieve next batch of Delivery Results.
            $limit += $batch;
            $offset += $batch;
            $deliveryResults = $deliveryResultClass->getInstances(false, array('offset' => $offset, 'limit' => $limit));
        }
    }
    
    /**
     * Create Response/OutcomeVariable object from an Ontology $variable.
     * 
     * @param core_kernel_classes_Resource $variable
     * @return \taoResultServer_models_classes_ResponseVariable|taoResultServer_models_classes_OutcomeVariable|boolean
     */
    static private function createVariableObject(core_kernel_classes_Resource $variable)
    {
        $newVariable = false;
        $variableUri = $variable->getUri();
        
        $variableProperties = array(
                        self::VARIABLEIDENTIFIERPROPERTYURI,
                        self::VARIABLECARDINALITYPROPERTYURI,
                        self::VARIABLEBASETYPEPROPERTYURI,
                        self::VARIABLECORRECTRESPONSEPROPERTYURI,
                        self::VARIABLEVALUEPROPERTYURI,
                        self::VARIABLEPOCHPROPERTYURI,
                        self::VARIABLENORMALMAXIMUMPROPERTYURI,
                        self::VARIABLENORMALMINIMUMPROPERTYURI,
                        self::RDFTYPEPROPERTY
        );
        
        $variableValues = $variable->getPropertiesValues($variableProperties);
        
        $noIdentifier = empty($variableValues[self::VARIABLEIDENTIFIERPROPERTYURI]);
        $noCardinality = empty($variableValues[self::VARIABLECARDINALITYPROPERTYURI]) || current($variableValues[self::VARIABLECARDINALITYPROPERTYURI])->__toString() == '';
        $noBasetype = empty($variableValues[self::VARIABLEBASETYPEPROPERTYURI]) || current($variableValues[self::VARIABLEBASETYPEPROPERTYURI])->__toString() == '';
        $noCorrectResponse = empty($variableValues[self::VARIABLECORRECTRESPONSEPROPERTYURI]) || current($variableValues[self::VARIABLECORRECTRESPONSEPROPERTYURI])->__toString() == '';
        $noValue = empty($variableValues[self::VARIABLEVALUEPROPERTYURI]) || current($variableValues[self::VARIABLEVALUEPROPERTYURI])->__toString() == '';
        $noEpoch = empty($variableValues[self::VARIABLEPOCHPROPERTYURI]) || current($variableValues[self::VARIABLEPOCHPROPERTYURI])->__toString() == '';
        $noVariableType = empty($variableValues[self::RDFTYPEPROPERTY]) || !current($variableValues[self::RDFTYPEPROPERTY]) instanceof \core_kernel_classes_Resource;
        $noNormalMaximum = empty($variableValues[self::VARIABLENORMALMAXIMUMPROPERTYURI]) || current($variableValues[self::VARIABLENORMALMAXIMUMPROPERTYURI])->__toString() == '';
        $noNormalMinimum = empty($variableValues[self::VARIABLENORMALMINIMUMPROPERTYURI]) || current($variableValues[self::VARIABLENORMALMINIMUMPROPERTYURI])->__toString() == '';
        
        if ($noIdentifier === false && $noEpoch === false && $noVariableType === false) {
            $newVariableIdentifier = current($variableValues[self::VARIABLEIDENTIFIERPROPERTYURI]);
            $newVariableType = current($variableValues[self::RDFTYPEPROPERTY])->getUri();
            $newVariableEpoch = current($variableValues[self::VARIABLEPOCHPROPERTYURI])->__toString();
            $newVariableCardinality = ($noCardinality === true) ? null : current($variableValues[self::VARIABLECARDINALITYPROPERTYURI])->__toString();
            $newVariableBasetype = ($noBasetype === true) ? null : current($variableValues[self::VARIABLEBASETYPEPROPERTYURI])->__toString();
            $newVariableCorrectResponse = ($noCorrectResponse === true) ? null : (current($variableValues[self::VARIABLECORRECTRESPONSEPROPERTYURI])->getUri() === self::GENERISTRUEINSTANCEURI) ? true : false;
            $newVariableValue = ($noValue === true) ? null : base64_decode(current($variableValues[self::VARIABLEVALUEPROPERTYURI])->__toString());
            $newNormalMaximum = ($noNormalMaximum === true) ? null : floatval(current($variableValues[self::VARIABLENORMALMAXIMUMPROPERTYURI])->__toString());
            $newNormalMinimum = ($noNormalMinimum === true) ? null : floatval(current($variableValues[self::VARIABLENORMALMINIMUMPROPERTYURI])->__toString());
        
            if ($newVariableType === self::RESPONSEVARIABLECLASSURI || $newVariableType === self::OUTCOMEVARIABLECLASSURI) {
        
                // Let's infer whether it's a Response or Outcome Variable.
                $newVariable = ($newVariableType === self::RESPONSEVARIABLECLASSURI) ? new \taoResultServer_models_classes_ResponseVariable() : new \taoResultServer_models_classes_OutcomeVariable();
                $newVariable->setIdentifier($newVariableIdentifier);
                $newVariable->setEpoch($newVariableEpoch);
        
                if ($newVariableCardinality !== null) {
                    $newVariable->setCardinality($newVariableCardinality);
                }
        
                if ($newVariableBasetype !== null) {
                    $newVariable->setBaseType($newVariableBasetype);
                }
        
                if ($newVariableCorrectResponse !== null && $newVariable instanceof \taoResultServer_models_classes_ResponseVariable) {
                    $newVariable->setCorrectResponse($newVariableCorrectResponse);
                }
        
                if ($newVariableValue !== null) {
                    if ($newVariable instanceof \taoResultServer_models_classes_ResponseVariable) {
                        $newVariable->setCandidateResponse($newVariableValue);
                    } else {
                        $newVariable->setValue($newVariableValue);
                    }
                }
        
                if ($newNormalMaximum !== null && $newVariable instanceof \taoResultServer_models_classes_OutcomeVariable) {
                    $newVariable->setNormalMaximum($newNormalMaximum);
                }
        
                if ($newNormalMinimum !== null && $newVariable instanceof \taoResultServer_models_classes_OutcomeVariable) {
                    $newVariable->setNormalMinimum($normalMinimum);
                }
            }
        }
        
        return $newVariable;
    }
    
    /**
     * Remove Old RDF Result Server from Ontology.
     */
    static private function disableRdfResultServers()
    {
        $rdfResultServerResource = new core_kernel_classes_Resource(self::RDFRESULTSERVERINSTANCEURI);
        $rdfResultServerModelResource = new core_kernel_classes_Resource(self::RDFRESULTSERVERMODELINSTANCEURI);
        
        // Remove RDF Delivery Server.
        if ($rdfResultServerResource->exists() === true) {
            $rdfResultServerResource->delete();
        }
        
        // Remove RDF Delivery Server Model.
        if ($rdfResultServerModelResource->exists() === true) {
            $rdfResultServerModelResource->delete();
        }
    }
    
    /**
     * Re-assign existing Deliveries to RDS Result Server.
     */
    static private function assignDeliveriesToRds()
    {
        // Retrieve all deliveries.
        $deliveryClass = new \core_kernel_classes_Class(self::DELIVERYCLASSURI);
        $deliveryResultServerProperty = new \core_kernel_classes_Property(self::DELIVERYRESULTSERVERPROPERTYURI);
        foreach ($deliveryClass->getInstances(true) as $delivery) {
            $delivery->editPropertyValues($deliveryResultServerProperty, self::RDSRESULTSERVERINSTANCEURI);
        }
    }
}
