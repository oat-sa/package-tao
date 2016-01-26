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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 * 
 */

/**
 * Short description of class core_kernel_rules_Term
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package generis
 
 */
class core_kernel_rules_Term
    extends core_kernel_classes_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method evaluate
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array $variable
     * @return mixed
     */
    public function evaluate($variable = array())
    {
        
      	common_Logger::i('Evaluating Term uri : '. $this->getUri(), array('Generis Term'));
      	common_Logger::i('Evaluating Term name : '. $this->getLabel(), array('Generis Term'));
		$termType = $this->getUniquePropertyValue(new core_kernel_classes_Property(RDF_TYPE));
		common_Logger::d('Term s type : '. $termType->getUri(), array('Generis Term'));
		switch($termType->getUri()) {
    		case CLASS_TERM : {
				throw new common_Exception("Forbidden Type of Term");
				
    			break;
    		}
    		case CLASS_TERM_SUJET_PREDICATE_X : {
    				$returnValue = $this->evaluateSPX($variable);
       			break;
    		}
		   case CLASS_TERM_X_PREDICATE_OBJECT : {
		   			$returnValue = $this->evaluateXPO();
				break;
    		}
    		case CLASS_CONSTRUCTED_SET : {
    				$returnValue = $this->evaluateSet();
    			break;
    		}
    	   	case CLASS_TERM_CONST : {
    	   			$returnValue = $this->evaluateConst();
    	   		break;
    		}
    		case CLASS_OPERATION : {
    				$returnValue = $this->evaluateOperation($variable);
      			break;
    		}
    		default :
    			throw new common_Exception('problem evaluating Term');
    	}
    	
		return $returnValue;
        
    }


    /**
     * Short description of method evalutateSetOperation
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource $setOperator
     * @param  Collection $actualSet
     * @param  ContainerCollection $newSet
     * @return core_kernel_classes_ContainerCollection
     */
    public function evalutateSetOperation( core_kernel_classes_Resource $setOperator,  common_Collection $actualSet,  core_kernel_classes_ContainerCollection $newSet)
    {
        $returnValue = null;

        
    	if($setOperator->getUri() == INSTANCE_OPERATOR_UNION) {
			$returnValue = $actualSet->union($newSet);
    	}
    	else if($setOperator->getUri() == INSTANCE_OPERATOR_INTERSECT) {
    		$returnValue =  $actualSet->intersect($newSet);
    	}
    	else {
    		throw new common_Exception('unknow set operator');
		}
        

        return $returnValue;
    }

    /**
     * Short description of method evaluateSPX
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @param  array $variable
     * @return mixed
     */
    protected function evaluateSPX($variable = array())
    {
        
    	common_Logger::d('SPX TYPE', array('Generis Term evaluateSPX'));
    	$resource = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TERM_SPX_SUBJET));
    	if($resource instanceof core_kernel_classes_Resource){
    		if(array_key_exists($resource->getUri(),$variable)) {
    			common_Logger::d('Variable uri : ' .  $resource->getUri() . ' found', array('Generis Term evaluateSPX'));
    			common_Logger::d('Variable name : ' .  $resource->getLabel() . ' found', array('Generis Term evaluateSPX'));
    			$resource = new core_kernel_classes_Resource($variable[$resource->getUri()]);
    			common_Logger::d('Variable repaced uri : ' .  $resource->getUri(), array('Generis Term evaluateSPX'));
    			common_Logger::d('Variable repaced name : ' .  $resource->getLabel(), array('Generis Term evaluateSPX'));
    		}
    		
    		try
    		{
    			$propertyInstance = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TERM_SPX_PREDICATE));
    		}
    		catch (common_Exception $e)
    		{
    			echo $e;
    			var_dump($this);
    			die('unable to get property value in Term');
    		}
//    		if(array_key_exists($propertyInstance->getUri(),$variable)) {
//				$logger->debug('Variable uri : ' .  $propertyInstance->getUri() . ' found' , __FILE__, __LINE__);
//    			$logger->debug('Variable name : ' .  $propertyInstance->getLabel() . ' found' , __FILE__, __LINE__);
//				$propertyInstance = new core_kernel_classes_Resource($variable[$resource->getUri()]);
//    			$logger->debug('Variable repaced uri : ' .  $propertyInstance->getUri() , __FILE__, __LINE__);
//    			$logger->debug('Variable repaced name : ' .  $propertyInstance->getLabel() , __FILE__, __LINE__);
//    	    }
    		$property = new core_kernel_classes_Property($propertyInstance->getUri());
    		common_Logger::d('Property uri ' . $property->getUri(), array('Generis Term evaluateSPX'));
    		common_Logger::d('Property name ' . $property->getLabel(), array('Generis Term evaluateSPX'));
       		$returnValue = $resource->getPropertyValuesCollection($property);
       		common_Logger::d($returnValue->count() . ' values returned ', array('Generis Term evaluateSPX'));

       		if($returnValue->isEmpty()) {
       			$newEmptyTerm = new core_kernel_rules_Term(INSTANCE_TERM_IS_NULL,__METHOD__);
       			common_Logger::d('Empty Term Created', array('Generis Term evaluateSPX'));
       			$property = new core_kernel_classes_Property(PROPERTY_TERM_VALUE);
       			$returnValue = $newEmptyTerm->getUniquePropertyValue($property);	
       		}
       		else {
				if($returnValue->count() == 1 ) {
       					$returnValue = $returnValue->get(0);
       			}
       		}

       		
    	}
    	return $returnValue;
        
    }

    /**
     * Short description of method evaluateXPO
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    protected function evaluateXPO()
    {
        
        common_Logger::d('XPO TYPE', array('Generis Term evaluateXPO'));
		$classTerm = new core_kernel_classes_Class(CLASS_TERM_X_PREDICATE_OBJECT);
		$obj = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TERM_XPO_OBJECT));
		$pred = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TERM_XPO_PREDICATE));
		if($obj instanceof core_kernel_classes_Literal) {
			$objValue = $obj->literal;
		}
   		if($obj instanceof core_kernel_classes_Resource) {
			$objValue = $pred->getUri();
		}
		
		$returnValue = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
		$terms = $classTerm->searchInstances(array($pred->getUri() => $objValue), array('like' => false));
		foreach($terms as $term){
			$returnValue->add($term);
		}
    	return $returnValue;
        
    }

    /**
     * Short description of method evaluateSet
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    protected function evaluateSet()
    {
        
        common_Logger::d('Constructed Set TYPE', array('Generis Term evaluateSet'));
    	$operator = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_SET_OPERATOR));
    	$subSets = $this->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_SUBSET));
    	$returnValue = new core_kernel_classes_ContainerCollection($this);

		foreach ($subSets->getIterator() as $aSet) {
    		
    		if ($aSet instanceof core_kernel_classes_Resource ) {
    			$newSet = new core_kernel_rules_Term($aSet->getUri());
    			$resultSet = $newSet->evaluate();
    			if ($resultSet instanceof core_kernel_classes_ContainerCollection  ) {
    				$returnValue = $this->evalutateSetOperation($operator,$returnValue,$resultSet);
				}
    			else {
					$collection = new core_kernel_classes_ContainerCollection($this);
    				$collection->add($resultSet);
    				$returnValue = $this->evalutateSetOperation($operator,$returnValue,$collection);
    			}	
    		}
    		else {
    			throw new common_Exception('Bad Type , waiting for a Resource ');
    		}
    	   
    	}    		
    	
    	return $returnValue;
        
    }

    /**
     * Short description of method evaluateConst
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    protected function evaluateConst()
    {
        
        common_Logger::d('CONSTANTE TYPE', array('Generis Term evaluateConst'));
	    $property = new core_kernel_classes_Property(PROPERTY_TERM_VALUE);
	    return $this->getUniquePropertyValue($property); 
        
    }

    /**
     * Short description of method evaluateOperation
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @param  array variable
     * @return mixed
     */
    protected function evaluateOperation($variable = array())
    {
        
        common_Logger::d('OPERATION TYPE', array('Generis Term evaluateOperation'));
    	return $this->evaluateArithmOperation($variable);
        
    }

    /**
     * Short description of method evaluateArtihmOperation
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array $variable
     * @return mixed
     */
    public function evaluateArithmOperation($variable = array())
    {
        
        $operation = new core_kernel_rules_Operation($this->getUri(), __METHOD__);
    	return  $operation->evaluate($variable);
        
    }

}