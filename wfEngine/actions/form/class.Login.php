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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/actions/form/class.Login.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 29.10.2012, 10:01:51 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023BF-includes begin
// section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023BF-includes end

/* user defined constants */
// section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023BF-constants begin
// section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023BF-constants end

/**
 * Short description of class wfEngine_actions_form_Login
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 * @subpackage actions_form
 */
class wfEngine_actions_form_Login
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        // section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023C0 begin
        
    	$this->form = tao_helpers_form_FormFactory::getForm('loginForm');
		
		$connectElt = tao_helpers_form_FormFactory::getElement('connect', 'Submit');
		$connectElt->setValue(__('Log in'));
		$this->form->setActions(array($connectElt), 'bottom');
    	
        // section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023C0 end
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
        // section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023C2 begin
        
    	$loginElt = tao_helpers_form_FormFactory::getElement('login', 'Textbox');
		$loginElt->setDescription(__('Login'));
		$loginElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		$this->form->addElement($loginElt);
		
		$passElt = tao_helpers_form_FormFactory::getElement('password', 'Hiddenbox');
		$passElt->setDescription(__('Password'));
		$passElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		$this->form->addElement($passElt);
    	
		//add 2 hidden inputs to post requested process and activity executions:
		$processElt = tao_helpers_form_FormFactory::getElement('processUri', 'Hidden');
		$this->form->addElement($processElt);
		
		$activityElt = tao_helpers_form_FormFactory::getElement('activityUri', 'Hidden');
		$this->form->addElement($activityElt);
		
        // section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023C2 end
    }

} /* end of class wfEngine_actions_form_Login */

?>