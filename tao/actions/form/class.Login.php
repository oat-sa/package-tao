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
 * This container initialize the login form.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E56-includes begin
// section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E56-includes end

/* user defined constants */
// section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E56-constants begin
// section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E56-constants end

/**
 * This container initialize the login form.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */
class tao_actions_form_Login
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        // section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E57 begin
		
		$this->form = tao_helpers_form_FormFactory::getForm('loginForm');
		
		$connectElt = tao_helpers_form_FormFactory::getElement('connect', 'Submit');
		$connectElt->setValue(__('Log in'));
		$this->form->setActions(array($connectElt), 'bottom');
		
        // section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E57 end
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        // section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E59 begin
		
    	if (isset($this->data['redirect']) && !empty($this->data['redirect'])) {
			$hiddenElt = tao_helpers_form_FormFactory::getElement('redirect', 'Hidden');
			$hiddenElt->setValue($this->data['redirect']);
			$this->form->addElement($hiddenElt);
    	}
    	$loginElt = tao_helpers_form_FormFactory::getElement('login', 'Textbox');
		$loginElt->setDescription(__('Login'));
		$loginElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		$this->form->addElement($loginElt);
		
		$passElt = tao_helpers_form_FormFactory::getElement('password', 'Hiddenbox');
		$passElt->setDescription(__('Password'));
		$passElt->addValidator(
			tao_helpers_form_FormFactory::getValidator('NotEmpty')
		);
		$this->form->addElement($passElt);
		
        // section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E59 end
    }

} /* end of class tao_actions_form_Login */

?>