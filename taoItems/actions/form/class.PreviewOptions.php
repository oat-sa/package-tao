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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/actions/form/class.PreviewOptions.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 29.11.2010, 16:18:56 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand CHEVRIER, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Bertrand CHEVRIER, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 127-0-1-1--983698e:12c9833a275:-8000:000000000000274A-includes begin
// section 127-0-1-1--983698e:12c9833a275:-8000:000000000000274A-includes end

/* user defined constants */
// section 127-0-1-1--983698e:12c9833a275:-8000:000000000000274A-constants begin
// section 127-0-1-1--983698e:12c9833a275:-8000:000000000000274A-constants end

/**
 * Short description of class taoItems_actions_form_PreviewOptions
 *
 * @access public
 * @author Bertrand CHEVRIER, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */
class taoItems_actions_form_PreviewOptions
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Bertrand CHEVRIER, <bertrand.chevrier@tudor.lu>
     * @return mixed
     * 
     */
    public function initForm()
    {
        // section 127-0-1-1--983698e:12c9833a275:-8000:000000000000274B begin
        
    	$this->form = tao_helpers_form_FormFactory::getForm('preview_options_form');
    	
    	$this->form->setActions(array(), 'top');
    	$this->form->setActions(tao_helpers_form_FormFactory::getCommonActions('bottom', true), 'bottom');
    	
        // section 127-0-1-1--983698e:12c9833a275:-8000:000000000000274B end
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Bertrand CHEVRIER, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        // section 127-0-1-1--983698e:12c9833a275:-8000:000000000000274D begin
        
    	//recovery context element
    	$ctxElt = tao_helpers_form_FormFactory::getElement('context', 'Combobox');
    	$ctxElt->setDescription(__('Recovery context'));
    	$ctxElt->setOptions(array(
    		0		=> __('Disabled'),
    		1		=> __('Enabled')
    	));
    	if(isset($this->data['context'])){
   			$ctxElt->setValue($this->data['context']);
    	}
    	$this->form->addElement($ctxElt);
    	
    	//matching on client or server side
    	$matchElt = tao_helpers_form_FormFactory::getElement('match', 'Combobox');
    	$matchElt->setDescription(__('Matching side'));
    	$matchElt->setOptions(array(
    		'client'		=> __('Client'),
    		'server'		=> __('Server')
    	));
    	$matchElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
    	if(isset($this->data['match'])){
    		if($this->data['match'] == 'server'){
    			$matchElt->setValue('server');
    		}
    		else{
    			$matchElt->setValue('client');
    		}
    	}
    	$this->form->addElement($matchElt);
    	
    	//recovery context element
    	$debugElt = tao_helpers_form_FormFactory::getElement('debug', 'Combobox');
    	$debugElt->setDescription(__('Debug Mode'));
    	$debugElt->setOptions(array(
    		0		=> __('Disabled'),
    		1		=> __('Enabled')
    	));
    	if(isset($debugElt->data['debug'])){
   			$debugElt->setValue($this->data['debug']);
    	}
    	$this->form->addElement($debugElt);
    	
		//add an hidden elt for the instance Uri
		$uriElt = tao_helpers_form_FormFactory::getElement('uri', 'Hidden');
		$uriElt->setValue($this->data['uri']);
		$this->form->addElement($uriElt);
    	
    	
        // section 127-0-1-1--983698e:12c9833a275:-8000:000000000000274D end
    }

} /* end of class taoItems_actions_form_PreviewOptions */

?>