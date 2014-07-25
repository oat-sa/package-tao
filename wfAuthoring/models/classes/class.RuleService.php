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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *
 *
 */

/**
 * TAO - wfAuthoring/models/classes/class.RuleService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 26.10.2012, 11:17:14 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-30-1--78-7cfbed5f:13a9c4b075b:-8000:0000000000003BC4-includes begin
require_once('wfAuthoring/plugins/CapiXML/models/class.ConditionalTokenizer.php');
require_once('wfAuthoring/plugins/CapiImport/models/class.DescriptorFactory.php');
// section 10-30-1--78-7cfbed5f:13a9c4b075b:-8000:0000000000003BC4-includes end

/* user defined constants */
// section 10-30-1--78-7cfbed5f:13a9c4b075b:-8000:0000000000003BC4-constants begin
// section 10-30-1--78-7cfbed5f:13a9c4b075b:-8000:0000000000003BC4-constants end

/**
 * Short description of class wfAuthoring_models_classes_RuleService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage models_classes
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

        // section 10-30-1--78-7cfbed5f:13a9c4b075b:-8000:0000000000003BC9 begin
    	foreach ($xml->childNodes as $childNode) {
			foreach ($childNode->childNodes as $childOfChildNode) {
				if ($childOfChildNode->nodeName == "condition"){

					$conditionDescriptor = DescriptorFactory::getConditionDescriptor($childOfChildNode);
					$returnValue = $conditionDescriptor->import();//(3*(^var +  1) = 2 or ^var > 7) AND ^RRR
					break 2;//once is enough...

				}
			}
		}
        // section 10-30-1--78-7cfbed5f:13a9c4b075b:-8000:0000000000003BC9 end

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

        // section 10-30-1--78-7cfbed5f:13a9c4b075b:-8000:0000000000003BCC begin
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
		// section 10-30-1--78-7cfbed5f:13a9c4b075b:-8000:0000000000003BCC end

        return $returnValue;
    }

} /* end of class wfAuthoring_models_classes_RuleService */

?>