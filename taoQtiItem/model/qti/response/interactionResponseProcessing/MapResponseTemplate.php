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

use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\MapResponseTemplate;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\Template;

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
class MapResponseTemplate
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
    const CLASS_ID = 'map';

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

        
        $returnValue = 'if(isNull(null, getResponse("'.$this->getResponse()->getIdentifier().'"))) { '.
        	'setOutcomeValue("'.$this->getOutcome()->getIdentifier().'", 0); } else { '.
        	'setOutcomeValue("'.$this->getOutcome()->getIdentifier().'", '.
        		'mapResponse(null, getMap("'.$this->getResponse()->getIdentifier().'"), getResponse("'.$this->getResponse()->getIdentifier().'"))); };';
        

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
		        <not>
		            <isNull>
		                <variable identifier="'.$this->getResponse()->getIdentifier().'" />
		            </isNull>
		        </not>
		        <setOutcomeValue identifier="'.$this->getOutcome()->getIdentifier().'">
	                <mapResponse identifier="'.$this->getResponse()->getIdentifier().'" />
		        </setOutcomeValue>
		    </responseIf>
		</responseCondition>';
        

        return (string) $returnValue;
    }

}

?>