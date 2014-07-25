<?php

/**
 * 
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



/**
 * Short description of class wfAuthoring_models_classes_RuleService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 
 */
class wfAuthoring_models_classes_RuleService
	extends wfEngine_models_classes_RuleService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method createConditionExpressionFromXML
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  DomDocument xml
     * @return core_kernel_rules_Expression
     */
    public function createConditionExpressionFromXML( DomDocument $xml)
    {
        $returnValue = null;

        
    	foreach ($xml->childNodes as $childNode) {
			foreach ($childNode->childNodes as $childOfChildNode) {
				if ($childOfChildNode->nodeName == "condition"){

					$conditionDescriptor = DescriptorFactory::getConditionDescriptor($childOfChildNode);
					$returnValue = $conditionDescriptor->import();//(3*(^var +  1) = 2 or ^var > 7) AND ^RRR
					break 2;//once is enough...

				}
			}
		}
        

        return $returnValue;
    }

    /**
     * Short description of method createConditionExpressionFromString
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string string
     * @return core_kernel_rules_Expression
     */
    public function createConditionExpressionFromString($string)
    {
        $returnValue = null;

        
		//test: "IF    (11+B_Q01a*3)>=2 AND (B_Q01c=2 OR B_Q01c=7)    	THEN ^variable := 2*(B_Q01a+7)-^variable";

		$question = "if ".$string;
        // str_replace taken from the MsReader class
		$question = str_replace("�", "'", $question); // utf8...
		$question = str_replace("�", "'", $question); // utf8...
		$question = str_replace("�", "\"", $question);
		$question = str_replace("�", "\"", $question);
		try {
			$analyser = new Analyser();
			common_Logger::i('analysing expression \''.$question.'\'');
			$tokens = $analyser->analyse($question);
		} catch(Exception $e) {
			throw new common_Exception("CapiXML error: {$e->getMessage()}");
		}
		$returnValue = $this->createConditionExpressionFromXML($tokens->getXml());
		

        return $returnValue;
    }

} /* end of class wfAuthoring_models_classes_RuleService */

?>