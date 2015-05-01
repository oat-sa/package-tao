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

namespace oat\taoOutcomeRds\model;

use oat\taoResultServer\models\classes\ResultManagement;
use \common_Logger;
use \core_kernel_classes_Resource;
use \core_kernel_classes_Property;
use qtism\common\datatypes\Float;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\OutcomeVariable;

/**
 * Implements tao results storage using the configured persistency "taoOutcomeRds"
 *
 */
class RdsResultStorage extends \tao_models_classes_GenerisService
    implements \taoResultServer_models_classes_WritableResultStorage, \taoResultServer_models_classes_ReadableResultStorage, ResultManagement
{
    /**
     * Constants for the database creation and data access
     *
     */
    const RESULTS_TABLENAME = "results_storage";
    const RESULTS_TABLE_ID = 'result_id';
    const TEST_TAKER_COLUMN = 'test_taker';
    const DELIVERY_COLUMN = 'delivery';


    const VARIABLES_TABLENAME = "variables_storage";
    const VARIABLES_TABLE_ID = "variable_id";
    const CALL_ID_ITEM_COLUMN = "call_id_item";
    const CALL_ID_TEST_COLUMN = "call_id_test";
    const TEST_COLUMN = "test";
    const ITEM_COLUMN = "item";
    const VARIABLE_IDENTIFIER = "identifier";
    const VARIABLE_CLASS = "class";
    const VARIABLES_FK_COLUMN = "results_result_id";
    const VARIABLES_FK_NAME = "fk_variables_results";

    const RESULT_KEY_VALUE_TABLE_NAME = "results_kv_storage";
    const KEY_COLUMN = "result_key";
    const VALUE_COLUMN = "result_value";
    const RESULTSKV_FK_COLUMN = "variables_variable_id";
    const RESULTSKV_FK_NAME = "fk_resultsKv_variables";


    /**
     * SQL persistence to use
     *
     * @var common_persistence_SqlPersistence
     */
    private $persistence;

    public function __construct()
    {
        parent::__construct();
        $this->persistence = $this->getPersistence();
    }

    private function getPersistence()
    {
        return \common_persistence_Manager::getPersistence('default');
    }

    /**
     * Store in the table all value corresponding to a key
     * @param \taoResultServer_models_classes_Variable $variable
     */
    private function storeKeysValues($variableId, \taoResultServer_models_classes_Variable $variable)
    {
        $basetype = $variable->getBaseType();
        foreach (array_keys((array)$variable) as $key) {
            $getter = 'get' . ucfirst($key);
            $value = null;
            if (method_exists($variable, $getter)) {
                $value = $variable->$getter();
                if ($key == 'value' || $key == 'candidateResponse') {
                    $value = base64_encode($value);
                }
            }
            if ($key == 'epoch' && !$variable->isSetEpoch()) {
                $value = microtime();
            }
            $this->persistence->insert(
                self::RESULT_KEY_VALUE_TABLE_NAME,
                array(
                    self::RESULTSKV_FK_COLUMN => $variableId,
                    self::KEY_COLUMN => $key,
                    self::VALUE_COLUMN => $value
                )
            );
        }
    }

    public function spawnResult()
    {
        \common_Logger::w('Unsupported function');
    }

    /**
     * Store the test variable in table and its value in key/value storage
     *
     * @param type $deliveryResultIdentifier
     *            lis_result_sourcedid
     * @param type $test
     *            ignored
     * @param \taoResultServer_models_classes_Variable $testVariable
     * @param type $callIdTest
     *            ignored
     */
    public function storeTestVariable(
        $deliveryResultIdentifier,
        $test,
        \taoResultServer_models_classes_Variable $testVariable,
        $callIdTest
    ) {
        $sql = 'SELECT COUNT(*) FROM ' . self::VARIABLES_TABLENAME .
            ' WHERE ' . self::VARIABLES_FK_COLUMN . ' = ? AND ' . self::TEST_COLUMN . ' = ?
            AND ' . self::VARIABLE_IDENTIFIER . ' = ?';
        $params = array($deliveryResultIdentifier, $test, $testVariable->getIdentifier());

        // if there is already a record for this item we update it
        if ($this->persistence->query($sql, $params)->fetchColumn() > 0) {
            $sqlUpdate = 'UPDATE ' . self::VARIABLES_TABLENAME . ' SET ' . self::CALL_ID_TEST_COLUMN . ' = ?
            WHERE ' . self::VARIABLES_FK_COLUMN . ' = ? AND ' . self::TEST_COLUMN . ' = ? AND ' . self::VARIABLE_IDENTIFIER . ' = ?';
            $paramsUpdate = array($callIdTest, $deliveryResultIdentifier, $test, $testVariable->getIdentifier());
            $this->persistence->exec($sqlUpdate, $paramsUpdate);
        } else {
            $variableClass = get_class($testVariable);

            $this->persistence->insert(
                self::VARIABLES_TABLENAME,
                array(
                    self::VARIABLES_FK_COLUMN => $deliveryResultIdentifier,
                    self::TEST_COLUMN => $test,
                    self::CALL_ID_TEST_COLUMN => $callIdTest,
                    self::VARIABLE_CLASS => $variableClass,
                    self::VARIABLE_IDENTIFIER => $testVariable->getIdentifier()
                )
            );

            $variableId = $this->persistence->lastInsertId(self::VARIABLES_TABLENAME);
            $this->storeKeysValues($variableId, $testVariable);
        }
    }

    /**
     * Store the item in table and its value in key/value storage
     * @param $deliveryResultIdentifier
     * @param $test
     * @param $item
     * @param \taoResultServer_models_classes_Variable $itemVariable
     * @param $callIdItem
     */
    public function storeItemVariable(
        $deliveryResultIdentifier,
        $test,
        $item,
        \taoResultServer_models_classes_Variable $itemVariable,
        $callIdItem
    ) {
        //store value in all case
        $variableClass = get_class($itemVariable);

        $this->persistence->insert(
            self::VARIABLES_TABLENAME,
            array(
                self::VARIABLES_FK_COLUMN => $deliveryResultIdentifier,
                self::TEST_COLUMN => $test,
                self::ITEM_COLUMN => $item,
                self::CALL_ID_ITEM_COLUMN => $callIdItem,
                self::VARIABLE_CLASS => $variableClass,
                self::VARIABLE_IDENTIFIER => $itemVariable->getIdentifier()
            )
        );

        $variableId = $this->persistence->lastInsertId(self::VARIABLES_TABLENAME);

        $this->storeKeysValues($variableId, $itemVariable);
    }

    /*
     * retrieve specific parameters from the resultserver to configure the storage
     */
    public function configure(core_kernel_classes_Resource $resultserver, $callOptions = array())
    {
        \common_Logger::d('configure  RdsResultStorage with options : ' . implode(" ", $callOptions));
    }

    /**
     * Store test-taker doing the test
     * @param $deliveryResultIdentifier
     * @param $testTakerIdentifier
     */
    public function storeRelatedTestTaker($deliveryResultIdentifier, $testTakerIdentifier)
    {
        $sql = 'SELECT COUNT(*) FROM ' . self::RESULTS_TABLENAME .
            ' WHERE ' . self::RESULTS_TABLE_ID . ' = ?';
        $params = array($deliveryResultIdentifier);
        if ($this->persistence->query($sql, $params)->fetchColumn() == 0) {
            $this->persistence->insert(
                self::RESULTS_TABLENAME,
                array(
                    self::TEST_TAKER_COLUMN => $testTakerIdentifier,
                    self::RESULTS_TABLE_ID => $deliveryResultIdentifier
                )
            );
        } else {
            $sqlUpdate = 'UPDATE ' . self::RESULTS_TABLENAME . ' SET ' . self::TEST_TAKER_COLUMN . ' = ? WHERE ' . self::RESULTS_TABLE_ID . ' = ?';
            $paramsUpdate = array($testTakerIdentifier, $deliveryResultIdentifier);
            $this->persistence->exec($sqlUpdate, $paramsUpdate);
        }
    }

    /**
     * Store Delivery corresponding to the current test
     * @param $deliveryResultIdentifier
     * @param $deliveryIdentifier
     */
    public function storeRelatedDelivery($deliveryResultIdentifier, $deliveryIdentifier)
    {
        $sql = 'SELECT COUNT(*) FROM ' . self::RESULTS_TABLENAME .
            ' WHERE ' . self::RESULTS_TABLE_ID . ' = ?';
        $params = array($deliveryResultIdentifier);
        if ($this->persistence->query($sql, $params)->fetchColumn() == 0) {
            $this->persistence->insert(
                self::RESULTS_TABLENAME,
                array(self::DELIVERY_COLUMN => $deliveryIdentifier, self::RESULTS_TABLE_ID => $deliveryResultIdentifier)
            );
        } else {
            $sqlUpdate = 'UPDATE ' . self::RESULTS_TABLENAME . ' SET ' . self::DELIVERY_COLUMN . ' = ? WHERE ' . self::RESULTS_TABLE_ID . ' = ?';
            $paramsUpdate = array($deliveryIdentifier, $deliveryResultIdentifier);
            $this->persistence->exec($sqlUpdate, $paramsUpdate);
        }
    }


    /**
     * @param string $callId
     * @return array
     */
    public function getVariables($callId)
    {
        $sql = 'SELECT * FROM ' . self::VARIABLES_TABLENAME . ', ' . self::RESULT_KEY_VALUE_TABLE_NAME . '
        WHERE (' . self::CALL_ID_ITEM_COLUMN . ' = ? OR ' . self::CALL_ID_TEST_COLUMN . ' = ?) AND ' . self::VARIABLES_TABLE_ID . ' = ' . self::RESULTSKV_FK_COLUMN;
        $params = array($callId, $callId);
        $variables = $this->persistence->query($sql, $params);

        $returnValue = array();

        // for each variable we construct the array
        $lastVariable = array();


        foreach ($variables as $variable) {

            if (empty($lastVariable)) {
                $lastVariable = $variable;
                if (class_exists($lastVariable[self::VARIABLE_CLASS])) {
                    $resultVariable = new $lastVariable[self::VARIABLE_CLASS]();
                } else {
                    $resultVariable = new \taoResultServer_models_classes_OutcomeVariable();
                }
            }

            // store variable from 0 to n-1
            if ($lastVariable[self::VARIABLES_TABLE_ID] != $variable[self::VARIABLES_TABLE_ID]) {
                $object = new \stdClass();
                $object->uri = $lastVariable[self::VARIABLES_TABLE_ID];
                $object->class = $lastVariable[self::VARIABLE_CLASS];
                $object->deliveryResultIdentifier = $lastVariable[self::VARIABLES_FK_COLUMN];
                $object->callIdItem = $lastVariable[self::CALL_ID_ITEM_COLUMN];
                $object->callIdTest = $lastVariable[self::CALL_ID_TEST_COLUMN];
                $object->test = $lastVariable[self::TEST_COLUMN];
                $object->item = $lastVariable[self::ITEM_COLUMN];
                $object->variable = clone $resultVariable;
                $returnValue[$lastVariable[self::VARIABLES_TABLE_ID]][] = $object;
                $lastVariable = $variable;
                if (class_exists($lastVariable[self::VARIABLE_CLASS])) {
                    $resultVariable = new $lastVariable[self::VARIABLE_CLASS]();
                } else {
                    $resultVariable = new \taoResultServer_models_classes_OutcomeVariable();
                }
            }

            $setter = 'set' . ucfirst($variable[self::KEY_COLUMN]);
            $value = $variable[self::VALUE_COLUMN];

            if (method_exists($resultVariable, $setter) && !is_null($value)) {
                if ($variable[self::KEY_COLUMN] == 'value' || $variable[self::KEY_COLUMN] == 'candidateResponse') {
                    $value = base64_decode($value);
                }

                $resultVariable->$setter($value);
            }

        }

        if (count($variables) > 0 && !empty($lastVariable)) {
            // store the variable n
            $object = new \stdClass();
            $object->uri = $lastVariable[self::VARIABLES_TABLE_ID];
            $object->class = $lastVariable[self::VARIABLE_CLASS];
            $object->deliveryResultIdentifier = $lastVariable[self::VARIABLES_FK_COLUMN];
            $object->callIdItem = $lastVariable[self::CALL_ID_ITEM_COLUMN];
            $object->callIdTest = $lastVariable[self::CALL_ID_TEST_COLUMN];
            $object->test = $lastVariable[self::TEST_COLUMN];
            $object->item = $lastVariable[self::ITEM_COLUMN];
            $object->variable = clone $resultVariable;
            $returnValue[$lastVariable[self::VARIABLES_TABLE_ID]][] = $object;
        }


        return $returnValue;
    }

    /**
     * Get a variable from callId and Variable identifier
     * @param $callId
     * @param $variableIdentifier
     * @return array
     */
    public function getVariable($callId, $variableIdentifier)
    {
        $sql = 'SELECT * FROM ' . self::VARIABLES_TABLENAME . ', ' . self::RESULT_KEY_VALUE_TABLE_NAME . '
        WHERE (' . self::CALL_ID_ITEM_COLUMN . ' = ? OR ' . self::CALL_ID_TEST_COLUMN . ' = ?)
        AND ' . self::VARIABLES_TABLE_ID . ' = ' . self::RESULTSKV_FK_COLUMN . ' AND ' . self::VARIABLE_IDENTIFIER . ' = ?';

        $params = array($callId, $callId, $variableIdentifier);
        $variables = $this->persistence->query($sql, $params);

        $returnValue = array();

        // for each variable we construct the array
        $lastVariable = array();
        foreach ($variables as $variable) {
            if (empty($lastVariable)) {
                $lastVariable = $variable;
                if (class_exists($lastVariable[self::VARIABLE_CLASS])) {
                    $resultVariable = new $lastVariable[self::VARIABLE_CLASS]();
                } else {
                    $resultVariable = new \taoResultServer_models_classes_OutcomeVariable();
                }
            }
            if ($lastVariable[self::VARIABLES_TABLE_ID] != $variable[self::VARIABLES_TABLE_ID]) {
                $object = new \stdClass();
                $object->uri = $lastVariable[self::VARIABLES_TABLE_ID];
                $object->class = $lastVariable[self::VARIABLE_CLASS];
                $object->deliveryResultIdentifier = $lastVariable[self::VARIABLES_FK_COLUMN];
                $object->callIdItem = $lastVariable[self::CALL_ID_ITEM_COLUMN];
                $object->callIdTest = $lastVariable[self::CALL_ID_TEST_COLUMN];
                $object->test = $lastVariable[self::TEST_COLUMN];
                $object->item = $lastVariable[self::ITEM_COLUMN];
                $object->variable = clone $resultVariable;
                $returnValue[$lastVariable[self::VARIABLES_TABLE_ID]][] = $object;

                $lastVariable = $variable;
                if (class_exists($lastVariable[self::VARIABLE_CLASS])) {
                    $resultVariable = new $lastVariable[self::VARIABLE_CLASS]();
                } else {
                    $resultVariable = new \taoResultServer_models_classes_OutcomeVariable();
                }
            }

            $setter = 'set' . ucfirst($variable[self::KEY_COLUMN]);
            $value = $variable[self::VALUE_COLUMN];
            if (method_exists($resultVariable, $setter) && !is_null($value)) {
                if ($variable[self::KEY_COLUMN] == 'value' || $variable[self::KEY_COLUMN] == 'candidateResponse') {
                    $value = base64_decode($value);
                }

                $resultVariable->$setter($value);
            }

        }

        // store the variable n
        if (count($variables) > 0 && !empty($lastVariable)) {
            $object = new \stdClass();
            $object->uri = $lastVariable[self::VARIABLES_TABLE_ID];
            $object->class = $lastVariable[self::VARIABLE_CLASS];
            $object->deliveryResultIdentifier = $lastVariable[self::VARIABLES_FK_COLUMN];
            $object->callIdItem = $lastVariable[self::CALL_ID_ITEM_COLUMN];
            $object->callIdTest = $lastVariable[self::CALL_ID_TEST_COLUMN];
            $object->test = $lastVariable[self::TEST_COLUMN];
            $object->item = $lastVariable[self::ITEM_COLUMN];
            $object->variable = clone $resultVariable;
            $returnValue[$lastVariable[self::VARIABLES_TABLE_ID]][] = $object;
        }

        return $returnValue;

    }

    public function getVariableProperty($variableId, $property)
    {
        $sql = 'SELECT ' . self::VALUE_COLUMN . ' FROM ' . self::RESULT_KEY_VALUE_TABLE_NAME . '
        WHERE ' . self::RESULTSKV_FK_COLUMN . ' = ? AND ' . self::KEY_COLUMN . ' = ?';
        $params = array($variableId, $property);
        $value = $this->persistence->query($sql, $params)->fetchColumn();
        if(in_array($property, array('value', 'candidateResponse'))){
            return base64_decode($value);
        }
        return $value;

    }

    /**
     * get test-taker corresponding to a result
     * @param $deliveryResultIdentifier
     * @return mixed
     */
    public function getTestTaker($deliveryResultIdentifier)
    {
        $sql = 'SELECT ' . self::TEST_TAKER_COLUMN . ' FROM ' . self::RESULTS_TABLENAME . ' WHERE ' . self::RESULTS_TABLE_ID . ' = ?';
        $params = array($deliveryResultIdentifier);
        return $this->persistence->query($sql, $params)->fetchColumn();
    }

    /**
     * get delivery corresponding to a result
     * @param $deliveryResultIdentifier
     * @return mixed
     */
    public function getDelivery($deliveryResultIdentifier)
    {
        $sql = 'SELECT ' . self::DELIVERY_COLUMN . ' FROM ' . self::RESULTS_TABLENAME . ' WHERE ' . self::RESULTS_TABLE_ID . ' = ?';
        $params = array($deliveryResultIdentifier);
        return $this->persistence->query($sql, $params)->fetchColumn();
    }

    /**
     * @return array the list of item executions ids (across all results)
     * o(n) do not use real time (postprocessing)
     */
    public function getAllCallIds()
    {
        $returnValue = array();
        $sql = 'SELECT DISTINCT(' . self::CALL_ID_ITEM_COLUMN . '), ' . self::CALL_ID_TEST_COLUMN . ', ' . self::VARIABLES_FK_COLUMN . ' FROM ' . self::VARIABLES_TABLENAME;
        $results = $this->persistence->query($sql);
        foreach ($results as $value) {
            $returnValue[] = ($value[self::CALL_ID_ITEM_COLUMN] != "") ? $value[self::CALL_ID_ITEM_COLUMN] : $value[self::CALL_ID_TEST_COLUMN];
        }

        return $returnValue;
    }

    /**
     * @param $deliveryResultIdentifier
     * @return array the list of item executions ids related to a delivery result
     */
    public function getRelatedItemCallIds($deliveryResultIdentifier)
    {
        $returnValue = array();

        $sql = 'SELECT DISTINCT(' . self::CALL_ID_ITEM_COLUMN . ') FROM ' . self::VARIABLES_TABLENAME . '
        WHERE ' . self::VARIABLES_FK_COLUMN . ' = ? AND ' . self::CALL_ID_ITEM_COLUMN . ' <> \'\'';
        $params = array($deliveryResultIdentifier);
        $results = $this->persistence->query($sql, $params);
        foreach ($results as $value) {
            if(isset($value[self::CALL_ID_ITEM_COLUMN])){
                $returnValue[] = $value[self::CALL_ID_ITEM_COLUMN];
            }
        }

        return $returnValue;
    }

    public function getRelatedTestCallIds($deliveryResultIdentifier)
    {
        $returnValue = array();

        $sql = 'SELECT DISTINCT(' . self::CALL_ID_TEST_COLUMN . ') FROM ' . self::VARIABLES_TABLENAME . '
        WHERE ' . self::VARIABLES_FK_COLUMN . ' = ? AND ' . self::CALL_ID_TEST_COLUMN . ' <> \'\'';
        $params = array($deliveryResultIdentifier);
        $results = $this->persistence->query($sql, $params);
        foreach ($results as $value) {
            if(isset($value[self::CALL_ID_TEST_COLUMN])){
                $returnValue[] = $value[self::CALL_ID_TEST_COLUMN];
            }
        }

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see taoResultServer_models_classes_ReadableResultStorage::getAllTestTakerIds()
     */
    public function getAllTestTakerIds()
    {
        $returnValue = array();
        $sql = 'SELECT ' . self::RESULTS_TABLE_ID . ', ' . self::TEST_TAKER_COLUMN . ' FROM ' . self::RESULTS_TABLENAME;
        $results = $this->persistence->query($sql);
        foreach ($results as $value) {
            $returnValue[] = array(
                "deliveryResultIdentifier" => $value[self::RESULTS_TABLE_ID],
                "testTakerIdentifier" => $value[self::TEST_TAKER_COLUMN]
            );
        }
        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see taoResultServer_models_classes_ReadableResultStorage::getAllDeliveryIds()
     */
    public function getAllDeliveryIds()
    {
        $returnValue = array();
        $sql = 'SELECT ' . self::RESULTS_TABLE_ID . ', ' . self::DELIVERY_COLUMN . ' FROM ' . self::RESULTS_TABLENAME;
        $results = $this->persistence->query($sql);
        foreach ($results as $value) {
            $returnValue[] = array(
                "deliveryResultIdentifier" => $value[self::RESULTS_TABLE_ID],
                "deliveryIdentifier" => $value[self::DELIVERY_COLUMN]
            );
        }
        return $returnValue;
    }

    /**
     * order orderdir, offset, limit
     */
    public function getResultByDelivery($delivery, $options = array())
    {
        $returnValue = array();
        $sql = 'SELECT * FROM ' . self::RESULTS_TABLENAME;
        $params = array();


        if (count($delivery) > 0) {
            $sql .= ' WHERE ';
            $inQuery = implode(',', array_fill(0, count($delivery), '?'));
            $sql .= self::DELIVERY_COLUMN . ' IN (' . $inQuery . ')';
            $params = array_merge($params, $delivery);
        }


        if(isset($options['order'])){
            
            $sql .= ' ORDER BY ?';
            $params[] = $options['order'];
            if(isset($options['oderdir']) && (strtolower($options['orderdir']) === 'asc' || strtolower($options['orderdir']) === 'desc')) {
                $sql .= ' ?';
                $params[] = $options['orderdir'];
            }
        }
        if(isset($options['offset']) || isset($options['limit'])){
            $offset = (isset($options['offset']))?$options['offset']:0;
            $limit = (isset($options['limit']))?$options['limit']:1000;
            $this->persistence->getPlatForm()->limitStatement($sql, $limit, $offset);
        }
        $results = $this->persistence->query($sql, $params);
        foreach ($results as $value) {
            $returnValue[] = array(
                "deliveryResultIdentifier" => $value[self::RESULTS_TABLE_ID],
                "testTakerIdentifier" => $value[self::TEST_TAKER_COLUMN],
                "deliveryIdentifier" => $value[self::DELIVERY_COLUMN]
            );
        }
        return $returnValue;

    }

    public function countResultByDelivery($delivery){
        $sql = 'SELECT COUNT(*) FROM ' . self::RESULTS_TABLENAME;
        $params = array();


        if (count($delivery) > 0) {
            $sql .= ' WHERE ';
            $inQuery = implode(',', array_fill(0, count($delivery), '?'));
            $sql .= self::DELIVERY_COLUMN . ' IN (' . $inQuery . ')';
            $params = array_merge($params, $delivery);
        }

        return $this->persistence->query($sql, $params)->fetchColumn();
    }


    /**
     * Remove the result and all the related variables
     * @param $deliveryResultIdentifier
     * @return bool
     */
    public function deleteResult($deliveryResultIdentifier)
    {
        // get all the variables related to the result
        $sql = 'SELECT ' . self::VARIABLES_TABLE_ID . ' FROM ' . self::VARIABLES_TABLENAME . '
        WHERE ' . self::VARIABLES_FK_COLUMN . ' = ?';
        $variables = $this->persistence->query($sql, array($deliveryResultIdentifier));

        // delete key/value for each variable
        foreach ($variables as $variable) {
            $sql = 'DELETE FROM ' . self::RESULT_KEY_VALUE_TABLE_NAME . '
            WHERE ' . self::RESULTSKV_FK_COLUMN . ' = ?';

            if ($this->persistence->exec($sql, array($variable[self::VARIABLES_TABLE_ID])) === false) {
                return false;
            }
        }

        // remove variables
        $sql = 'DELETE FROM ' . self::VARIABLES_TABLENAME . '
            WHERE ' . self::VARIABLES_FK_COLUMN . ' = ?';

        if ($this->persistence->exec($sql, array($deliveryResultIdentifier)) === false) {
            return false;
        }

        // remove results
        $sql = 'DELETE FROM ' . self::RESULTS_TABLENAME . '
            WHERE ' . self::RESULTS_TABLE_ID . ' = ?';

        if ($this->persistence->exec($sql, array($deliveryResultIdentifier)) === false) {
            return false;
        }

        return true;
    }


    /**
     * 
     * @param unknown $a
     * @param unknown $b
     * @return number
     */
    public static function sortTimeStamps($a, $b) {
        list($usec, $sec) = explode(" ", $a);
        $floata = ((float) $usec + (float) $sec);
        list($usec, $sec) = explode(" ", $b);
        $floatb = ((float) $usec + (float) $sec);
        //common_Logger::i($a." ".$floata);
        //common_Logger::i($b. " ".$floatb);
        //the callback is expecting an int returned, for the case where the difference is of less than a second
        //intval(round(floatval($b) - floatval($a),1, PHP_ROUND_HALF_EVEN));
        if ((floatval($floata) - floatval($floatb)) > 0) {
            return 1;
        } elseif ((floatval($floata) - floatval($floatb)) < 0) {
            return -1;
        } else {
            return 0;
        }
    }
}
