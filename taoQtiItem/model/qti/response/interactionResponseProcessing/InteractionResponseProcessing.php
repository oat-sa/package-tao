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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\taoQtiItem\model\qti\response\interactionResponseProcessing;

use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\InteractionResponseProcessing;
use oat\taoQtiItem\model\qti\response\Rule;
use oat\taoQtiItem\model\qti\ResponseDeclaration;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\None;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\MatchCorrectTemplate;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\MapResponseTemplate;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\MapResponsePointTemplate;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\Custom;
use oat\taoQtiItem\model\qti\OutcomeDeclaration;
use \common_Exception;
use \common_exception_Error;

/**
 * The response processing of a single interaction
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
abstract class InteractionResponseProcessing implements Rule
{
    /**
     * Short description of attribute SCORE_PREFIX
     *
     * @access private
     * @var string
     */

    const SCORE_PREFIX = 'SCORE_';

    /**
     * Short description of attribute response
     *
     * @access public
     * @var Response
     */
    public $response = null;

    /**
     * Short description of attribute outcome
     *
     * @access public
     * @var Outcome
     */
    public $outcome = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRule(){
        $returnValue = (string) '';

        throw new common_Exception('Missing getRule implementation for '.get_class($this), array('TAOITEMS', 'QTI', 'HARD'));

        return (string) $returnValue;
    }

    /**
     * Short description of method create
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int classID
     * @param  Response response
     * @param  Item item
     * @return oat\taoQtiItem\model\qti\response\interactionResponseProcessing\InteractionResponseProcessing
     */
    public static function create($classID, ResponseDeclaration $response, Item $item){
        switch($classID){
            case None::CLASS_ID :
                $className = 'oat\\taoQtiItem\\model\\qti\\response\\interactionResponseProcessing\\None';
                break;
            case MatchCorrectTemplate::CLASS_ID :
                $className = 'oat\\taoQtiItem\\model\\qti\\response\\interactionResponseProcessing\\MatchCorrectTemplate';
                break;
            case MapResponseTemplate::CLASS_ID :
                $className = 'oat\\taoQtiItem\\model\\qti\\response\\interactionResponseProcessing\\MapResponseTemplate';
                break;
            case MapResponsePointTemplate::CLASS_ID :
                $className = 'oat\\taoQtiItem\\model\\qti\\response\\interactionResponseProcessing\\MapResponsePointTemplate';
                break;
            case Custom::CLASS_ID :
                $className = 'oat\\taoQtiItem\\model\\qti\\response\\interactionResponseProcessing\\Custom';
                break;
            default :
                throw new common_exception_Error('Unknown InteractionResponseProcessing Class ID "'.$classID.'"');
        }
        $outcome = self::generateOutcomeDefinition();
        $outcomes = $item->getOutcomes();
        $outcomes[] = $outcome;
        $item->setOutcomes($outcomes);
        $returnValue = new $className($response, $outcome);

        return $returnValue;
    }

    /**
     * Short description of method generateOutcomeDefinition
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return oat\taoQtiItem\model\qti\OutcomeDeclaration
     */
    public static function generateOutcomeDefinition(){
        return new OutcomeDeclaration(array('baseType' => 'integer', 'cardinality' => 'single'));
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Response response
     * @param  Outcome outcome
     * @return mixed
     */
    public function __construct(ResponseDeclaration $response, OutcomeDeclaration $outcome){
        $this->response = $response;
        $this->outcome = $outcome;
    }

    /**
     * Short description of method getResponse
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return oat\taoQtiItem\model\qti\ResponseDeclaration
     */
    public function getResponse(){
        return $this->response;
    }

    /**
     * Short description of method getOutcome
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return oat\taoQtiItem\model\qti\OutcomeDeclaration
     */
    public function getOutcome(){
        return $this->outcome;
    }

    /**
     * Short description of method getIdentifier
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getIdentifier(){
        $returnValue = $this->getResponse()->getIdentifier().'_rp';
        return (string) $returnValue;
    }

}