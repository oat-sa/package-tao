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
 * This container initialize the list form.
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
// section 127-0-1-1--289f70ef:127af0e99db:-8000:000000000000237A-includes begin
// section 127-0-1-1--289f70ef:127af0e99db:-8000:000000000000237A-includes end

/* user defined constants */
// section 127-0-1-1--289f70ef:127af0e99db:-8000:000000000000237A-constants begin
// section 127-0-1-1--289f70ef:127af0e99db:-8000:000000000000237A-constants end

/**
 * This container initialize the list form.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */
class tao_actions_form_List
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
        // section 127-0-1-1--289f70ef:127af0e99db:-8000:000000000000237C begin

        $this->form = tao_helpers_form_FormFactory::getForm('list');

        $addElt = tao_helpers_form_FormFactory::getElement('add', 'Free');
		$addElt->setValue("<a class='form-submiter' href='#'><img src='".TAOBASE_WWW."img/add.png' class='icon' />".__('Add')."</a>");
		$this->form->setActions(array($addElt), 'bottom');
		$this->form->setActions(array(), 'top');

        // section 127-0-1-1--289f70ef:127af0e99db:-8000:000000000000237C end
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
        // section 127-0-1-1--289f70ef:127af0e99db:-8000:000000000000237E begin

    	$labelElt = tao_helpers_form_FormFactory::getElement('label', 'Textbox');
		$labelElt->setDescription(__('Name'));
		$labelElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		$this->form->addElement($labelElt);

		$sizeElt = tao_helpers_form_FormFactory::getElement('size', 'Textbox');
		$sizeElt->setDescription(__('Number of elements'));
		$sizeElt->setAttribute('size', '4');
		$sizeElt->setValue(1);
		$sizeElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('NotEmpty'),
			tao_helpers_form_FormFactory::getValidator('Integer', array('min' => 1))
		));
		$this->form->addElement($sizeElt);

		$this->form->createGroup('options', __('List Options'), array('label', 'size'));

        // section 127-0-1-1--289f70ef:127af0e99db:-8000:000000000000237E end
    }

} /* end of class tao_actions_form_List */

?>