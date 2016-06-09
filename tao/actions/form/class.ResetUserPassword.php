<?php

/**  
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *               
 */

use \oat\generis\model\user\PasswordConstraintsService;

/**
 * This container initialize the password reset form.
 *
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 * @package tao
 */
class tao_actions_form_ResetUserPassword extends tao_helpers_form_FormContainer
{

    /**
     * Initialize password reset form
     *
     * @access public
     * @author Aleh Hutnikau <hutnikau@1pt.com>
     * @return mixed
     */
    public function initForm()
    {
        $this->form = tao_helpers_form_FormFactory::getForm('resetUserPassword');

        $connectElt = tao_helpers_form_FormFactory::getElement('reset', 'Submit');
        $connectElt->setValue(__('Update'));
        $connectElt->setAttribute('class', 'btn-success small');
        $this->form->setActions(array($connectElt), 'bottom');
    }

    /**
     * Initialiaze password reset form elements
     *
     * @access public
     * @author Aleh Hutnikau <hutnikau@1pt.com>
     * @return mixed
     */
    public function initElements()
    {
        $tokenElement = tao_helpers_form_FormFactory::getElement('token', 'Hidden');
        $this->form->addElement($tokenElement);
        
        $pass1Element = tao_helpers_form_FormFactory::getElement('newpassword', 'Hiddenbox');
        $pass1Element->setDescription(__('New password'));
        $pass1Element->addValidators(PasswordConstraintsService::singleton()->getValidators());
        $pass1Element->setBreakOnFirstError(false);
        $this->form->addElement($pass1Element);

        $pass2Element = tao_helpers_form_FormFactory::getElement('newpassword2', 'Hiddenbox');
        $pass2Element->setDescription(__('Repeat new password'));
        $pass2Element->addValidators(array(
            tao_helpers_form_FormFactory::getValidator('Password', array('password2_ref' => $pass1Element)),
        ));
        $this->form->addElement($pass2Element);
    }
}