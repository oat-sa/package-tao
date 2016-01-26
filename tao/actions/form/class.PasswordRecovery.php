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

use oat\tao\helpers\Layout;

/**
 * This container initialize the password recovery form.
 *
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 * @package tao
 */
class tao_actions_form_PasswordRecovery extends tao_helpers_form_FormContainer
{
    /**
     * Initialize password recovery form
     *
     * @access public
     * @author Aleh Hutnikau <hutnikau@1pt.com>
     */
    public function initForm()
    {
        $this->form = tao_helpers_form_FormFactory::getForm('passwordRecoveryForm');

        $connectElt = tao_helpers_form_FormFactory::getElement('recovery', 'Submit');
        $connectElt->setValue(__('Email'));
        $connectElt->setAttribute('class', 'btn-success small');
        $this->form->setActions(array($connectElt), 'bottom');
    }

    /**
     * Initialiaze recovery form elements
     *
     * @access public
     * @author Aleh Hutnikau <hutnikau@1pt.com>
     */
    public function initElements()
    {
        $mailElement = tao_helpers_form_FormFactory::getElement('userMail', 'Textbox');
        $mailElement->setDescription(__('Your mail') . '*');
        $mailElement->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
        $mailElement->addValidator(tao_helpers_form_FormFactory::getValidator('Email'));
        $mailElement->setAttributes(array('autofocus' => 'autofocus'));
        
        $this->form->addElement($mailElement);
    }
    
}
