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

use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\None;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\InteractionResponseProcessing;

/**
 * no response processing
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
class None
    extends InteractionResponseProcessing
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute CLASS_ID
     *
     * @access public
     * @var string
     */
    const CLASS_ID = 'none';

    /**
     * Short description of attribute default
     *
     * @access protected
     * @var string
     */
    protected $default = '';

    // --- OPERATIONS ---

    /**
     * Short description of method getDefaultValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getDefaultValue()
    {
        $returnValue = (string) '';

        
        return $this->default;
        

        return (string) $returnValue;
    }

    /**
     * Short description of method setDefaultValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string value
     * @return mixed
     */
    public function setDefaultValue($value)
    {
        
        $this->default = $value;
        
    }

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
        	'setOutcomeValue("'.$this->getOutcome()->getIdentifier().'", '.$this->getDefaultValue().'); };';
        

        return (string) $returnValue;
    }

    /**
     * although no ResponseRules are nescessary to have no responseProcessing,
     * add some rules to associate the interaction response with a sepcific
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
	            <isNull>
	                <variable identifier="'.$this->getResponse()->getIdentifier().'" />
	            </isNull>
		        <setOutcomeValue identifier="'.$this->getOutcome()->getIdentifier().'">
		        	<baseValue baseType="'.$this->getOutcome()->getAttributeValue('baseType').'">0</baseValue>
		        </setOutcomeValue>
		    </responseIf>
		</responseCondition>';
        

        return (string) $returnValue;
    }

}

?>