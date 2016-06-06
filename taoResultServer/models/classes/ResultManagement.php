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
namespace oat\taoResultServer\models\classes;

interface ResultManagement extends \taoResultServer_models_classes_ReadableResultStorage {

    /**
     * Get only one property from a variable
     * @param string $variableId on which we want the property
     * @param string $property to retrieve
     * @return int|string the property retrieved
     */
    public function getVariableProperty($variableId, $property);

    /**
     * Get all the ids of the callItem for a specific delivery execution
     * @param string $deliveryResultIdentifier The identifier of the delivery execution
     * @return array the list of call item ids (across all results)
     */
    public function getRelatedItemCallIds($deliveryResultIdentifier);

    /**
     * Get all the ids of the callTest for a specific delivery execution
     * @param string $deliveryResultIdentifier The identifier of the delivery execution
     * @return array the list of call test ids (across all results)
     */
    public function getRelatedTestCallIds($deliveryResultIdentifier);

    /**
     * Get the result information (test taker, delivery, delivery execution) from filters
     * @param array $delivery list of delivery to search : array('test','myValue')
     * @param array $options params to restrict results array(
     * "order"=> "deliveryResultIdentifier" || "testTakerIdentifier" || "deliveryIdentifier",
     * "orderdir"=>"ASC" || "DESC",
     * "offset"=> an int,
     * "limit"=> an int
     * )
     * @return array of results that match the filter : array(array('deliveryResultIdentifier' => '123', 'testTakerIdentifier' => '456', 'deliveryIdentifier' => '789'))
     */
    public function getResultByDelivery($delivery, $options = array());

    /**
     * Count the number of result that match the filter
     * @param array $delivery list of delivery to search : array('test','myValue')
     * @return int the number of results that match filter
     */
    public function countResultByDelivery($delivery);


    /**
     * Remove the result and all the related variables
     * @param string $deliveryResultIdentifier The identifier of the delivery execution
     * @return boolean if the deletion was successful or not
     */
    public function deleteResult($deliveryResultIdentifier);

}
?>