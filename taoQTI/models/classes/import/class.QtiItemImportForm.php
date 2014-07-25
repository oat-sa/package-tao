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

/**
 * Import form for QTI Items (xml files)
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_import
 */
class taoQTI_models_classes_import_QtiItemImportForm
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---
    /**
     * (non-PHPdoc)
     * @see tao_helpers_form_FormContainer::initForm()
     */
    public function initForm()
    {
    	$this->form = new tao_helpers_form_xhtml_Form('export');
    	$submitElt = tao_helpers_form_FormFactory::getElement('import', 'Free');
		$submitElt->setValue( "<a href='#' class='form-submiter' ><img src='".TAOBASE_WWW."/img/import.png' /> ".__('Import')."</a>");

		$this->form->setActions(array($submitElt), 'bottom');
		$this->form->setActions(array(), 'top');
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_helpers_form_FormContainer::initElements()
     */
    public function initElements()
    {

    	$descElt = tao_helpers_form_FormFactory::getElement('qti_desc', 'Label');
		$descElt->setValue(__('A QTI item file is an XML file following the version 2.0 of the QTI standard.'));
		$this->form->addElement($descElt);
    	
    	//create file upload form box
		$fileElt = tao_helpers_form_FormFactory::getElement('source', 'AsyncFile');
		$fileElt->setDescription(__("Add the source file"));
    	if(isset($_POST['import_sent_qti'])){
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		}
		else{
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty', array('message' => '')));
		}
		$fileElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => array('text/xml', 'application/xml', 'application/x-xml'), 'extension' => array('xml'))),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => tao_helpers_Environment::getFileUploadLimit()))
		));
    	
		$this->form->addElement($fileElt);
		
		$disableValidationElt = tao_helpers_form_FormFactory::getElement("disable_validation", 'Checkbox');
		$disableValidationElt->setDescription("Disable validation");
		$disableValidationElt->setOptions(array("on" => ""));
		$this->form->addElement($disableValidationElt);
		
		$this->form->createGroup('file', __('Upload QTI File'), array('source','qti_desc', 'disable_validation'));
		
		$qtiSentElt = tao_helpers_form_FormFactory::getElement('import_sent_qti', 'Hidden');
		$qtiSentElt->setValue(1);
		$this->form->addElement($qtiSentElt);
    }

}