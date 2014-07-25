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
 * TAO - wfAuthoring/actions/form/class.InstanceProcessVariable.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 29.10.2012, 09:56:47 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Create a form from a  resource of your ontology. 
 * Each property will be a field, regarding it's widget.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/actions/form/class.Instance.php');

/* user defined includes */
// section 127-0-1-1--1c42fdef:133c68cec06:-8000:000000000000341C-includes begin
// section 127-0-1-1--1c42fdef:133c68cec06:-8000:000000000000341C-includes end

/* user defined constants */
// section 127-0-1-1--1c42fdef:133c68cec06:-8000:000000000000341C-constants begin
// section 127-0-1-1--1c42fdef:133c68cec06:-8000:000000000000341C-constants end

/**
 * Short description of class wfAuthoring_actions_form_InstanceProcessVariable
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage actions_form
 */
class wfAuthoring_actions_form_InstanceProcessVariable
    extends tao_actions_form_Instance
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getTopClazz
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Class
     */
    public function getTopClazz()
    {
        $returnValue = null;

        // section 127-0-1-1--1c42fdef:133c68cec06:-8000:000000000000341D begin
		if(!is_null($this->topClazz)){
        	$returnValue = $this->topClazz;
        }
        else{
        	$returnValue = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
        }
        // section 127-0-1-1--1c42fdef:133c68cec06:-8000:000000000000341D end

        return $returnValue;
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        // section 127-0-1-1--193aa0be:133cfb90ad2:-8000:000000000000342F begin
		parent::initElements();
		$codeElt = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_PROCESSVARIABLES_CODE));
		$codeElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		$codeElt->addValidator(new wfAuthoring_actions_form_validators_VariableCode(array('uri'=>$this->getInstance()->getUri())));
        // section 127-0-1-1--193aa0be:133cfb90ad2:-8000:000000000000342F end
    }

} /* end of class wfAuthoring_actions_form_InstanceProcessVariable */

?>