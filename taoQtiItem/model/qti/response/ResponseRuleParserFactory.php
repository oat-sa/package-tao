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

namespace oat\taoQtiItem\model\qti\response;

use oat\taoQtiItem\model\qti\response\ResponseRuleParserFactory;
use oat\taoQtiItem\model\qti\response\ExitResponse;
use oat\taoQtiItem\model\qti\expression\ExpressionParserFactory;
use oat\taoQtiItem\model\qti\response\SetOutcomeVariable;
use oat\taoQtiItem\model\qti\exception\ParsingException;
use oat\taoQtiItem\model\qti\response\ResponseCondition;
use \SimpleXMLElement;

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
class ResponseRuleParserFactory
{

    /**
     * Short description of method buildResponseRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  SimpleXMLElement data
     * @return oat\taoQtiItem\model\qti\response\ResponseRule
     */
    public static function buildResponseRule( SimpleXMLElement $data)
    {
        $returnValue = null;

	    switch ($data->getName()) {
			case 'exitResponse' : $returnValue = new ExitResponse();
				break;
			case 'setOutcomeValue' :
				$identifier = (string)$data['identifier'];
				$children = array();
				foreach ($data->children() as $child){
					$children[] = $child;
				}
				$expression = ExpressionParserFactory::build(array_shift($children));
				$returnValue = new SetOutcomeVariable($identifier, $expression);
				break;
			case 'responseCondition' :
				$returnValue = self::buildResponseCondition($data);
				break;
			default :
				throw new ParsingException('unknwown element '.$data->getName().' in ResponseProcessing');
		}

        return $returnValue;
    }

    /**
     * Short description of method buildResponseCondition
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  SimpleXMLElement data
     * @return oat\taoQtiItem\model\qti\response\ResponseCondition
     */
    private static function buildResponseCondition( SimpleXMLElement $data)
    {
        $responseCondition = new ResponseCondition();
		
		foreach ($data->children() as $child) {
			switch ($child->getName()) {
				case 'responseIf' :
				case 'responseElseIf' :
					$subchildren = null;
					foreach ($child->children() as $subchild){
						$subchildren[] = $subchild;
					}
		
					// first node is condition
					$conditionNode = array_shift($subchildren);
					$condition = ExpressionParserFactory::build($conditionNode);
					
					// all the other nodes are action
					$responseRules = array();
					foreach ($subchildren as $responseRule){
						$responseRules[] = self::buildResponseRule($responseRule);
					}
					$responseCondition->addResponseIf($condition, $responseRules);
							
					break;
					
				case 'responseElse' :
					$responseRules = array();
					foreach ($child->children() as $responseRule) {
						$responseRules[] = self::buildResponseRule($responseRule);
					}
					$responseCondition->setResponseElse($responseRules);
					break;
					
				default:
					throw new ParsingException('unknown node in ResponseCondition');
			}
		}

		$returnValue = $responseCondition; 

        return $returnValue;
    }

}