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
 * 
 */

namespace oat\taoQtiItem\helpers;

use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\Variable;
use qtism\runtime\tests\AssessmentItemSession;
use \tao_models_classes_service_StorageDirectory;

/**
 * Qti Item Runner helper
 *
 * @author Somsack Sipasseuth <sam@taotesting.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @package taoQtiItem
 *
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class QtiRunner
{

    /**
     * Get the intrinsic values of a given QTI $variable.
     * 
     * @param Variable $variable
     * @return array
     */
    public static function getVariableValues(Variable $variable) {

        $returnValue = array();

        $baseType = $variable->getBaseType();
        $cardinalityType = $variable->getCardinality();
        $value = $variable->getValue();
        
        // This only works if the variable has a value ;)
        if ($value !== null) {
            
            if ($baseType === BaseType::IDENTIFIER) {
            
                if ($cardinalityType === Cardinality::SINGLE) {
            
                    $returnValue[] = $value->getValue();
                }
                else if($cardinalityType === Cardinality::MULTIPLE) {
            
                    foreach($variable->getValue() as $value) {
            
                        $returnValue[] = $value->getValue();
                    }
                }
            }
        }

        return $returnValue;
    }

    /**
     * Get the absolute path to the compilation folder described by $directory.
     * 
     * @param tao_models_classes_service_StorageDirectory $director The root directory resource where the item is stored.
     * @return string The local path to the private folder with a trailing directory separator.
     */
    public static function getPrivateFolderPath(tao_models_classes_service_StorageDirectory $directory) {
        $lang = \common_session_SessionManager::getSession()->getDataLanguage();
        $basepath = $directory->getPath();
        
        if (!file_exists($basepath . $lang) && file_exists($basepath . DEFAULT_LANG)) {
            $lang = DEFAULT_LANG;
        }
        
        return $basepath . $lang . DIRECTORY_SEPARATOR;
    }
    
    /**
     * Get the JSON QTI Model representing the elements (A.K.A. components) that vary over time for
     * the item stored in $directory.
     * 
     * @param tao_models_classes_service_StorageDirectory $directory
     * @return array A JSON decoded array.
     */
    public static function getContentVariableElements(tao_models_classes_service_StorageDirectory $directory) {
        $jsonFile = self::getPrivateFolderPath($directory) . 'variableElements.json';
        $elements = file_get_contents($jsonFile);
        return json_decode($elements, true);
    }
    
    /**
     * Get rubric block visible by the given "view"
     * 
     * @param tao_models_classes_service_StorageDirectory $directory
     * @param type $view
     * @return array
     */
    public static function getRubricBlocks(tao_models_classes_service_StorageDirectory $directory, $view) {
        
        $returnValue = array();
        
        $elements = self::getContentVariableElements($directory);
        
        foreach ($elements as $serial => $data) {
        
            if (isset($data['qtiClass']) && $data['qtiClass'] == 'rubricBlock') {
        
                if (!empty($data['attributes']) && is_array($data['attributes']['view']) && in_array($view, $data['attributes']['view'])) {
                        $returnValue[$serial] = $data;
                }
            }
        }
        
        return $returnValue;
    }
    
    /**
     * Get the feedback to be displayed on an AssessmentItemSession
     * 
     * @param tao_models_classes_service_StorageDirectory $directory
     * @param \qtism\runtime\tests\AssessmentItemSession $itemSession
     * @return array 
     */
    public static function getFeedbacks(tao_models_classes_service_StorageDirectory $directory, AssessmentItemSession $itemSession) {
        
        $returnValue = array();
        
        $feedbackClasses = array('modalFeedback', 'feedbackInline', 'feedbackBlock');
        $elements = self::getContentVariableElements($directory);
        
        $outcomes = array();
        foreach ($elements as $data) {
            if (empty($data['qtiClass']) === false && in_array($data['qtiClass'], $feedbackClasses)) {
        
                $feedbackIdentifier = $data['attributes']['identifier'];
                $outcomeIdentifier = $data['attributes']['outcomeIdentifier'];
        
                if (!isset($outcomes[$outcomeIdentifier])) {
                    $outcomes[$outcomeIdentifier] = array();
                }
        
                $outcomes[$outcomeIdentifier][$feedbackIdentifier] = $data;
            }
        }
        
        foreach ($itemSession->getAllVariables() as $var) {
        
            $identifier = $var->getIdentifier();
        
            if (isset($outcomes[$identifier])) {
        
                $feedbacks = $outcomes[$identifier];
                $feedbackIds = QtiRunner::getVariableValues($var);
        
                foreach($feedbackIds as $feedbackId) {
        
                    if (isset($feedbacks[$feedbackId])) {
                        $data = $feedbacks[$feedbackId];
                        $returnValue[$data['serial']] = $data;
                    }
                }
        
            }
        }
        
        return $returnValue;
    }
}