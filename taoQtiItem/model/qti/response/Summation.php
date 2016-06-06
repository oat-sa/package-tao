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

namespace oat\taoQtiItem\model\qti\response;

use oat\taoQtiItem\model\qti\response\Summation;
use oat\taoQtiItem\model\qti\response\Composite;
use oat\taoQtiItem\model\qti\expression\CommonExpression;
use oat\taoQtiItem\model\qti\response\SetOutcomeVariable;

/**
 * Short description of class oat\taoQtiItem\model\qti\response\Summation
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
class Summation
    extends Composite
{

    /**
     * Short description of method getCompositionRules
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getCompositionRules()
    {
        $subExpressions = array();
        foreach ($this->components as $irp) {
        	$subExpressions[] = new CommonExpression('variable', array('identifier' => $irp->getOutcome()->getIdentifier()));
        }
        $sum = new CommonExpression('sum', array());
        $sum->setSubExpressions($subExpressions);
        $summationRule = new SetOutcomeVariable($this->outcomeIdentifier, $sum);
        
        $returnValue = array($summationRule);

        return (array) $returnValue;
    }

    /**
     * Short description of method getCompositionQTI
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getCompositionQTI()
    {
        $returnValue = (string) '';

        $returnValue .= '<setOutcomeValue identifier="'.$this->outcomeIdentifier.'"><sum>';
        foreach ($this->components as $irp) {
        	$returnValue .= '<variable identifier="'.$irp->getOutcome()->getIdentifier().'" />';
        }
        $returnValue .= '</sum></setOutcomeValue>';

        return (string) $returnValue;
    }

}