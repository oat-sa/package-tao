<?php
/**
 *   
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

/**
 * Short description of class wfEngine_actions_form_Login
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 
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
        
        
    	$this->form = tao_helpers_form_FormFactory::getForm('loginForm');
		
		$connectElt = tao_helpers_form_FormFactory::getElement('connect', 'Submit');
		$connectElt->setValue(__('Log in'));
		$this->form->setActions(array($connectElt), 'bottom');
    	
        
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
		
        
    }

} /* end of class wfEngine_actions_form_Login */

?>