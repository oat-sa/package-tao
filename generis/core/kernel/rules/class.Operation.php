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
 * Short description of class core_kernel_rules_Operation
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_rules
 */
class core_kernel_rules_Operation
    extends core_kernel_rules_Term
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute firstOperation
     *
     * @access private
     * @var Term
     */
    private $firstOperation = null;

    /**
     * Short description of attribute secondOperation
     *
     * @access private
     * @var Term
     */
    private $secondOperation = null;

    /**
     * Short description of attribute arithmeticOperator
     *
     * @access private
     * @var Resource
     */
    private $arithmeticOperator = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getFirstOperation
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_rules_Term
     */
    public function getFirstOperation()
    {
        $returnValue = null;

        // section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000E23 begin
        if(empty($this->firstOperation)){
        	$property = new core_kernel_classes_Property(PROPERTY_OPERATION_FIRST_OP);
        	$resource = $this->getUniquePropertyValue($property);
        	$this->firstOperation = new core_kernel_rules_Term($resource->getUri());
        }
        $returnValue = $this->firstOperation;
        // section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000E23 end

        return $returnValue;
    }

    /**
     * Short description of method getSecondOperation
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_rules_Term
     */
    public function getSecondOperation()
    {
        $returnValue = null;

        // section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000E25 begin
        if(empty($this->secondOperation)){
        	$property = new core_kernel_classes_Property(PROPERTY_OPERATION_SECND_OP);
        	$resource = $this->getUniquePropertyValue($property);
        	$this->secondOperation = new core_kernel_rules_Term($resource->getUri());
        }
        $returnValue = $this->secondOperation;
        // section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000E25 end

        return $returnValue;
    }

    /**
     * Short description of method getOperator
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public function getOperator()
    {
        $returnValue = null;

        // section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000E27 begin
        if(empty($this->arithmeticOperator)){
        	$property = new core_kernel_classes_Property(PROPERTY_OPERATION_OPERATOR);
        	$this->arithmeticOperator = $this->getUniquePropertyValue($property);
        }
        $returnValue = $this->arithmeticOperator;
        // section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000E27 end

        return $returnValue;
    }

    /**
     * Short description of method evaluate
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array variable
     * @return mixed
     */
    public function evaluate($variable = array())
    {
        // section 10-13-1--99-1a35566c:11edfbb4d4b:-8000:0000000000000F3A begin
        common_Logger::i('Evaluating Operation uri : '. $this->getUri(), array('Generis Operation'));
        common_Logger::i('Evaluating Operation name : '. $this->getLabel(), array('Generis Operation'));
        
        $operator = $this->getOperator();
        common_Logger::d('Operator uri: '. $operator->getUri(), array('Generis Operation'));
        common_Logger::d('Operator name: '. $operator->getLabel(), array('Generis Operation'));         

	    $firstPart = $this->getFirstOperation()->evaluate($variable);
	    $secondPart = $this->getSecondOperation()->evaluate($variable);


		if($firstPart instanceof core_kernel_classes_ContainerCollection ) {
			//if we have more than one result we only take the Literal label
			$nbLiteral = 0;
			$iterator = $firstPart->getIterator();
			foreach ($iterator as $first) {
				if ($first instanceof core_kernel_classes_Literal ) {
					$firstPart = $first;
					$nbLiteral++;
				}
				
			}
			if ($nbLiteral != 1){
				var_dump($iterator);
				throw new common_Exception('more than one Literal Retreive during  evaluation');
			}
    	}


   		
    	if($secondPart instanceof core_kernel_classes_ContainerCollection ) {
    		//if we have more than one result we only take the resource label
			$nbLiteral = 0;
			$iterator = $secondPart->getIterator();
			foreach ($secondPart->getIterator() as $second) {
				if ($second instanceof core_kernel_classes_Literal) {
					$secondPart = $second;
					$nbLiteral++;
				}

			}
			if ($nbLiteral != 1){
				var_dump($iterator);
				throw new common_Exception('more than one Literal Retreive during evaluation');
			}
    	}

    	common_Logger::d('First Part : ', array('Generis Operation'));
    	common_Logger::d('Second Part : '. $secondPart, array('Generis Operation'));
    	$returnValue = $this->evaluateRecursiveOperation($firstPart,$secondPart,$operator);
    	common_Logger::i('Operation value: '. $returnValue, array('Generis Operation'));
    	
    	return $returnValue;
        // section 10-13-1--99-1a35566c:11edfbb4d4b:-8000:0000000000000F3A end
    }

    /**
     * Short description of method evaluateRecursiveOperation
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Literal first
     * @param  Literal second
     * @param  Resource operator
     * @return mixed
     */
    public function evaluateRecursiveOperation( core_kernel_classes_Literal $first,  core_kernel_classes_Literal $second,  core_kernel_classes_Resource $operator)
    {
        // section 10-13-1--99-1a35566c:11edfbb4d4b:-8000:0000000000000F43 begin
        
        switch ($operator->getUri()) {
        	case INSTANCE_OPERATOR_ADD: {
        		$returnValue = new core_kernel_classes_Literal($first->literal + $second->literal);
        		break;
        	}
             case INSTANCE_OPERATOR_MINUS: {
        		$returnValue = new core_kernel_classes_Literal($first->literal - $second->literal);
        		break;
        	}
            case INSTANCE_OPERATOR_MULTIPLY: {
        		$returnValue = new core_kernel_classes_Literal($first->literal * $second->literal);
        		break;
        	}
        	case INSTANCE_OPERATOR_DIVISION: {
        		$returnValue = new core_kernel_classes_Literal($first->literal / $second->literal);
        		break;
        	}
        	case INSTANCE_OPERATOR_CONCAT: {
        		// FIXME Hotfix for the concat operator. Can't find why traling spaces are not
        		// kept intact when using concat.
        		// ex: 'february ' CONCAT '2008' -> 'february2008' instead of 'february 2008'.
        		$returnValue = new core_kernel_classes_Literal($first->literal . ' ' . $second->literal);
        		break;
        	}
        	             	
        	default : {
        		throw new common_Exception('problem evaluating operation, operator do not match with operands');
        	}
        		
        }
        $returnValue->debug = __METHOD__;
        return $returnValue;
        // section 10-13-1--99-1a35566c:11edfbb4d4b:-8000:0000000000000F43 end
    }

} /* end of class core_kernel_rules_Operation */

?>