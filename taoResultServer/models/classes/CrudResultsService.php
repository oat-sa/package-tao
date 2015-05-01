<?php

/*
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
 * 
 */

namespace oat\taoResultServer\models\classes;
use oat\taoOutcomeUi\model\ResultsService;

/**
 * .Crud services implements basic CRUD services, orginally intended for REST controllers/ HTTP exception handlers
 *  Consequently the signatures and behaviors is closer to REST and throwing HTTP like exceptions
 *  
 *
 * 
 */
class CrudResultsService extends \tao_models_classes_CrudService {

    protected $resultClass = null;
    protected $resultService = null;

    public function __construct() {
        parent::__construct();
        $this->resultClass = new \core_kernel_classes_Class(TAO_DELIVERY_RESULT);
        $this->resultService = ResultsService::singleton();
    }

    public function getRootClass() {
        return $this->resultClass;
    }

    public function get($uri) {
        $returnData = array();
        $deliveryExecution = \taoDelivery_models_classes_execution_ServiceProxy::singleton()->getDeliveryExecution($uri);
        $delivery = $deliveryExecution->getDelivery();

        $implementation = $this->getImplementationClass($delivery);

        foreach($implementation->getRelatedItemCallIds($uri) as $callId){
            $results = $implementation->getVariables($callId);
            $resource = array();
                foreach($results as $result){
                    $result = array_pop($result);
                    if(isset($result->variable)){
                        $resource['value'] = $result->variable->getValue();
                        $resource['identifier'] = $result->variable->getIdentifier();
                        if(get_class($result->variable) === CLASS_RESPONSE_VARIABLE){
                            $type = "http://www.tao.lu/Ontologies/TAOResult.rdf#ResponseVariable";
                        }
                        else{
                            $type = "http://www.tao.lu/Ontologies/TAOResult.rdf#OutcomeVariable";
                        }
                        $resource['type'] = new \core_kernel_classes_Class($type);
                        $resource['epoch'] = $result->variable->getEpoch();
                        $resource['cardinality'] = $result->variable->getCardinality();
                        $resource['basetype'] = $result->variable->getBaseType();
                    }

                    $returnData[$uri][] = $resource;
                }
        }
        return $returnData;
    }

    public function getAll()
    {
        $resources = array();
        $deliveryService = \taoDelivery_models_classes_DeliveryAssemblyService::singleton();
        foreach ($deliveryService->getAllAssemblies() as $assembly) {
            // delivery uri
            $delivery = $assembly->getUri();

            $implementation = $this->getImplementationClass($assembly);

            // get delivery executions

            //get all info
            foreach($implementation->getResultByDelivery(array($delivery)) as $result){
                $result = array_merge($result, array(RDFS_LABEL => $assembly->getLabel()));
                $properties = array();
                foreach($result as $key => $value){
                    $property = array();
                    $type = 'resource';
                    switch($key){
                        case 'deliveryResultIdentifier':
                            $property['predicateUri'] = "http://www.tao.lu/Ontologies/TAOResult.rdf#Identifier";
                            break;
                        case 'testTakerIdentifier':
                            $property['predicateUri'] = "http://www.tao.lu/Ontologies/TAOResult.rdf#resultOfSubject";
                            break;
                        case 'deliveryIdentifier':
                            $property['predicateUri'] = "http://www.tao.lu/Ontologies/TAOResult.rdf#resultOfDelivery";
                            break;
                        default:
                            $property['predicateUri'] = $key;
                            $type = 'literal';
                            break;
                    }
                    $property['values'] = array('valueType' => $type, 'value' => $value);

                    $properties[] = $property;

                }
                $resources[] = array(
                    'uri'           => $result['deliveryResultIdentifier'],
                    'properties'    => $properties
                );
            }
        }
        return $resources;
    }



    public function delete($resource) {
       throw new \common_exception_NoImplementation();
    }

    public function deleteAll() {
        throw new \common_exception_NoImplementation();
    }

   

    public function update($uri = null, $propertiesValues = array()) {
        throw new \common_exception_NoImplementation();
    }

    public function isInScope($uri)
    {
        return true;
    }

    /**
     *
     * @author Patrick Plichart, patrick@taotesting.com
     * return tao_models_classes_ClassService
     */
    protected function getClassService()
    {
        // TODO: Implement getClassService() method.
    }


    private function getImplementationClass($delivery){

        if(is_null($delivery)){
            throw new \common_exception_Error(__('This delivery doesn\'t exists'));
        }

        $deliveryResultServer = $delivery->getOnePropertyValue(new \core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));

        if(is_null($deliveryResultServer)){
            throw new \common_exception_Error(__('This delivery has no Result Server'));
        }
        $resultServerModel = $deliveryResultServer->getPropertyValues(new \core_kernel_classes_Property(TAO_RESULTSERVER_MODEL_PROP));

        if(is_null($resultServerModel)){
            throw new \common_exception_Error(__('This delivery has no readable Result Server'));
        }

        foreach($resultServerModel as $model){
            $model = new \core_kernel_classes_Class($model);

            /** @var $implementationClass \core_kernel_classes_Literal*/
            $implementationClass = $model->getOnePropertyValue(new \core_kernel_classes_Property(TAO_RESULTSERVER_MODEL_IMPL_PROP));


            if (!is_null($implementationClass)
                && class_exists($implementationClass->literal) && in_array('taoResultServer_models_classes_ReadableResultStorage',class_implements($implementationClass->literal))) {
                $className = $implementationClass->literal;
                if (!class_exists($className)) {
                    throw new \common_exception_Error('readable resultinterface implementation '.$className.' not found');
                }
                return new $className();
            }
        }

        throw new \common_exception_Error(__('This delivery has no readable Result Server'));

    }
}

?>
