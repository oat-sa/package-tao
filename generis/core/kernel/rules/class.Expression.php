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
 * Short description of class core_kernel_rules_Expression
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package generis
 
 */
class core_kernel_rules_Expression
    extends core_kernel_classes_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute logicalOperator
     *
     * @access private
     * @var Resource
     */
    private $logicalOperator = null;

    /**
     * Short description of attribute firstExpression
     *
     * @access private
     * @var Expression
     */
    private $firstExpression = null;

    /**
     * Short description of attribute secondExpression
     *
     * @access private
     * @var Expression
     */
    private $secondExpression = null;

    // --- OPERATIONS ---

    /**
     * Short description of method evaluate
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array variable
     * @return boolean
     */
    public function evaluate($variable = array())
    {
        $returnValue = (bool) false;

        
        
        common_Logger::i('Evaluating Expression uri: '. $this->getUri(), array('Generis Expression'));
        common_Logger::i('Evaluating Expression name: '. $this->getLabel(), array('Generis Expression'));
		if ($this->getUri() == INSTANCE_EXPRESSION_TRUE) {
			return true;
		}
    	if ($this->getUri() == INSTANCE_EXPRESSION_FALSE) {
			return false;
		}
		$returnValue = $this->expEval($variable);
		$logValue = $returnValue ? ' TRUE ' : ' FALSE ';
		common_Logger::i('Value : '. $logValue, array('Generis Expression'));
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string uri
     * @param  string debug
     * @return void
     */
    public function __construct($uri, $debug = '')
    {
        
        parent::__construct($uri);
        $this->debug = $debug;

        
    }

    /**
     * Short description of method getLogicalOperator
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_classes_Resource
     */
    public function getLogicalOperator()
    {
        $returnValue = null;

        
         if(empty($this->logicalOperator)){
         	$property = new core_kernel_classes_Property(PROPERTY_HASLOGICALOPERATOR);
			$this->logicalOperator = $this->getUniquePropertyValue($property);
         }
         $returnValue = $this->logicalOperator;
        

        return $returnValue;
    }

    /**
     * Short description of method getTerminalExpression
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_rules_Term
     */
    public function getTerminalExpression()
    {
        $returnValue = null;

        
        $property = new core_kernel_classes_Property(PROPERTY_TERMINAL_EXPRESSION);
        $propertyValue = $this->getUniquePropertyValue($property);
        if ($propertyValue instanceof core_kernel_classes_Resource ) {
       		$returnValue = new core_kernel_rules_Term($propertyValue->getUri() );
        	$returnValue->debug = __METHOD__;	
        }
        else {
        	throw new common_Exception('property retrieve should be a Resource');
        }
        

        return $returnValue;
    }

    /**
     * Short description of method getFirstExpression
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_rules_Expression
     */
    public function getFirstExpression()
    {
        $returnValue = null;

        
        if(empty($this->firstExpression)){
        	$property = new core_kernel_classes_Property(PROPERTY_FIRST_EXPRESSION);
			$this->firstExpression = new core_kernel_rules_Expression($this->getUniquePropertyValue($property)->getUri());
        }
		$returnValue = $this->firstExpression;
        

        return $returnValue;
    }

    /**
     * Short description of method getSecondExpression
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_rules_Expression
     */
    public function getSecondExpression()
    {
        $returnValue = null;

        
        if(empty($this->secondExpression)){
	        $property = new core_kernel_classes_Property(PROPERTY_SECOND_EXPRESSION);
			$this->secondExpression = new core_kernel_rules_Expression($this->getUniquePropertyValue($property)->getUri());
        }
        $returnValue = $this->secondExpression;
        

        return $returnValue;
    }

    /**
     * Short description of method expEval
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array variable
     * @return mixed
     */
    public function expEval($variable = array())
    {
        
		$terminalExpression = $this->getTerminalExpression();
		

    	if ($terminalExpression->getUri() == INSTANCE_EMPTY_TERM_URI){
			$firstPart = $this->getFirstExpression()->expEval($variable) ;
			
			if($this->getLogicalOperator()->getUri() == INSTANCE_AND_OPERATOR) {
				if ($firstPart == false) {
					common_Logger::i('CUT : first Expression == FALSE and OPERATOR = AND', array('Generis Expression'));
					return false;
				}
			}
			if($this->getLogicalOperator()->getUri() == INSTANCE_OR_OPERATOR) {

				if ($firstPart == true) {
					common_Logger::i('CUT : first Expression == TRUE and OPERATOR = OR', array('Generis Expression'));
					return true;
				}
			}
			$secondPart = $this->getSecondExpression()->expEval($variable);

			//if both part are simple value
			if($firstPart instanceof core_kernel_classes_Container 
				&& $secondPart instanceof core_kernel_classes_Container) {
					common_Logger::d('Both Part are Container', array('Generis Expression'));
					$returnValue = $this->operatorEval($firstPart,$secondPart);	

			}
			
			//both are vector
			else if ($firstPart instanceof core_kernel_classes_ContainerCollection 
					&& $secondPart instanceof core_kernel_classes_ContainerCollection ) {
						$returnValue = false;
						foreach ($firstPart->getIterator() as $subLeftPart)
						{
							//analyse left collection and remove any container in it which is not literal
							if (!($subLeftPart instanceof core_kernel_classes_Resource))
							{
								foreach ($secondPart->getIterator() as $subRightPart)
								{
									 //analyse right collection and remove any container in it which is not literal
									if (!($subRightPart instanceof core_kernel_classes_Resource))
									//print_r($subLeftPart);print_r($subRightPart);
									{
										$returnValue = $returnValue || $this->operatorEval($subLeftPart,$subRightPart);
									}
								}
							}
						}
						//die("the evaluation is ". $returnValue);
						
						//throw new common_Exception('not implemented yet', __FILE__,__LINE__);
			}
			// first is a vector second is a value
			else if (($firstPart instanceof core_kernel_classes_ContainerCollection) 
					&& ($secondPart instanceof core_kernel_classes_Container )) {
				$tempResult = false;
				foreach ($firstPart->getIterator() as $container) {
					common_Logger::d('FirstPart Part is ContainerCollection Second is Container', array('Generis Expression'));
					//TODO For now consider that if only  one value of the table return true, 
					
					//TODO exist unique need to be added
					
					if ($this->getLogicalOperator()->getUri() != INSTANCE_DIFFERENT_OPERATOR_URI)
					{
						$tempResult = $tempResult || $this->operatorEval($container,$secondPart);
					}
					else
					{
						if ($this->operatorEval($container,$secondPart))
						{
							$tempResult = true;
						}
						else
						{
							break;
							$tempResult = false;
						}
					}
				}
				$returnValue = $tempResult;
			}
			// first is a value second is a vector
			else if (($firstPart instanceof core_kernel_classes_Container) 
					&& ($secondPart instanceof core_kernel_classes_ContainerCollection )) {
				foreach ($secondPart->getIterator() as $container) {
					common_Logger::d('FirstPart Part Container is  Second is ContainerCollection', array('Generis Expression'));
					
					//TODO For now consider that all value of the table need to be equal to return true, , 
					//TODO exist unique need to be added
					$tempResult = $tempResult && $this->operatorEval($firstPart,$container);
						
				}
				$returnValue = $tempResult;
			}
			//case we compare boolean
			else {
				common_Logger::d('Both part are boolean', array('Generis Expression'));
			
				switch($this->getLogicalOperator()->getUri()) {
					case INSTANCE_OR_OPERATOR : {
						$returnValue = $firstPart || $secondPart ;
						
						break;
					}
					case INSTANCE_AND_OPERATOR : {
						$returnValue = $firstPart &&  $secondPart ;
						break;
					}
					default : {
						var_dump($this);
						throw new common_Exception('Expression ' . $this->getLabel() . ' do not have knowm operator');
					}
				}
			}
		}
		else {
			if ($terminalExpression != null) {
				common_Logger::d('Evaluating Terminal Expression', array('Generis Expression'));
				$returnValue = $terminalExpression->evaluate($variable);
				common_Logger::d('Result : ' . $returnValue, array('Generis Expression'));
			}
			else {
				throw new common_Exception('terminal expression is null');
			}
			
		}
		return $returnValue;
        
    }

    /**
     * Short description of method evalEquals
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Literal first
     * @param  Literal second
     * @return boolean
     */
    public function evalEquals( core_kernel_classes_Literal $first,  core_kernel_classes_Literal $second)
    {
        $returnValue = (bool) false;

        
        if($first instanceof core_kernel_classes_Literal && $second instanceof core_kernel_classes_Literal)  {
    		$returnValue = $first->literal == $second->literal;
        }
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method evalDifferent
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Literal first
     * @param  Literal second
     * @return boolean
     */
    public function evalDifferent( core_kernel_classes_Literal $first,  core_kernel_classes_Literal $second)
    {
        $returnValue = (bool) false;

        
        if($first instanceof core_kernel_classes_Literal && $second instanceof core_kernel_classes_Literal)  {
    		$returnValue = $first->literal != $second->literal;
        }
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method evalInfEquals
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Literal first
     * @param  Literal second
     * @return boolean
     */
    public function evalInfEquals( core_kernel_classes_Literal $first,  core_kernel_classes_Literal $second)
    {
        $returnValue = (bool) false;

        
        if($first instanceof core_kernel_classes_Literal && $second instanceof core_kernel_classes_Literal)  {
        	$returnValue = $first->literal <= $second->literal;
        }
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method evalInf
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Literal first
     * @param  Literal second
     * @return boolean
     */
    public function evalInf( core_kernel_classes_Literal $first,  core_kernel_classes_Literal $second)
    {
        $returnValue = (bool) false;

        
         if($first instanceof core_kernel_classes_Literal && $second instanceof core_kernel_classes_Literal)  {
     		$returnValue = $first->literal < $second->literal;
         }
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method evalSup
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Literal first
     * @param  Literal second
     * @return boolean
     */
    public function evalSup( core_kernel_classes_Literal $first,  core_kernel_classes_Literal $second)
    {
        $returnValue = (bool) false;

        
         if($first instanceof core_kernel_classes_Literal && $second instanceof core_kernel_classes_Literal)  {
             $returnValue = $first->literal > $second->literal;       
         }
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method evalSupEquals
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Literal first
     * @param  Literal second
     * @return boolean
     */
    public function evalSupEquals( core_kernel_classes_Literal $first,  core_kernel_classes_Literal $second)
    {
        $returnValue = (bool) false;

        
         if($first instanceof core_kernel_classes_Literal && $second instanceof core_kernel_classes_Literal)  {
	        $returnValue = $first->literal >= $second->literal;
         }
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method operatorEval
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Container firstPart
     * @param  Container secondPart
     * @return boolean
     */
    public function operatorEval( core_kernel_classes_Container $firstPart,  core_kernel_classes_Container $secondPart)
    {
        $returnValue = (bool) false;

        
        if ($firstPart instanceof core_kernel_classes_Resource ) {
        	$firstPart = new core_kernel_classes_Literal($firstPart->getUri());
		}
        if ($secondPart instanceof core_kernel_classes_Resource ) {
       		$secondPart = new core_kernel_classes_Literal($secondPart->getUri());
        }
        common_Logger::d('First Value : '. $firstPart->literal, array('Generis Expression'));
        common_Logger::d('Second Value : '. $secondPart->literal, array('Generis Expression'));
        common_Logger::d('Operator : '. $this->getLogicalOperator()->getLabel(), array('Generis Expression'));
        
        switch($this->getLogicalOperator()->getUri()) {
			case INSTANCE_EQUALS_OPERATOR_URI : {
				$returnValue = $this->evalEquals($firstPart,$secondPart);
				break;
			}
			case INSTANCE_DIFFERENT_OPERATOR_URI : {
				$returnValue = $this->evalDifferent($firstPart,$secondPart);
				break;
			}

			case INSTANCE_SUP_EQ_OPERATOR_URI : {
				$returnValue = $this->evalSupEquals($firstPart,$secondPart);
				break;
			}
			case INSTANCE_INF_EQ_OPERATOR_URI : {
				$returnValue = $this->evalInfEquals($firstPart,$secondPart);
				break;
			}
			case INSTANCE_SUP_OPERATOR_URI : {
				$returnValue = $this->evalSup($firstPart,$secondPart);				
				break;			
			}
			case INSTANCE_INF_OPERATOR_URI : {
				$returnValue = $this->evalInf($firstPart,$secondPart);
				break;
			}
			
			default: {
				var_dump($this);
				throw new common_Exception('Expression ' . $this->getLabel() . ' do not have knowm operator');
			}
		}
		
		$logValue = $returnValue ? ' TRUE ' : ' FALSE ';
		common_Logger::d('Expression Value : '. $logValue, array('Generis Expression'));
        

        return (bool) $returnValue;
    }

}