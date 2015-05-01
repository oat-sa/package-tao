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
 * 
 */
namespace oat\taoQtiItem\model\qti\response\interactionResponseProcessing;

use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\MatchCorrectTemplate;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\Template;

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
class MatchCorrectTemplate
    extends Template
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute CLASS_ID
     *
     * @access public
     * @var string
     */
    const CLASS_ID = 'correct';

    // --- OPERATIONS ---

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRule()
    {
        $returnValue = (string) '';

        
        $returnValue = 'if(match(null, '.
        	'getResponse("'.$this->getResponse()->getIdentifier().'"), '.
        	'getCorrect("'.$this->getResponse()->getIdentifier().'"))) '.
        	'setOutcomeValue("'.$this->getOutcome()->getIdentifier().'", 1); '.
        	'else setOutcomeValue("'.$this->getOutcome()->getIdentifier().'", 0);';
        

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        
        $returnValue = '<responseCondition>
		    <responseIf>
		        <match>
		            <variable identifier="'.$this->getResponse()->getIdentifier().'" />
		            <correct identifier="'.$this->getResponse()->getIdentifier().'" />
		        </match>
		        <setOutcomeValue identifier="'.$this->getOutcome()->getIdentifier().'">
	                <baseValue baseType="integer">1</baseValue>
		        </setOutcomeValue>
		    </responseIf>
		</responseCondition>';
        

        return (string) $returnValue;
    }

}

?>