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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\taoQtiItem\model\import;

use \tao_helpers_form_FormContainer;
use \tao_helpers_form_xhtml_Form;
use \tao_helpers_form_xhtml_TagWrapper;
use \tao_helpers_form_FormFactory;
use \tao_helpers_Environment;

/**
 * Import form for QTI packages
 *
 * @access public
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @package taoQTIItem
 
 */
class ApipPackageImportForm
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

		$this->form->setDecorators(array(
			'element'			=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div')),
			'group'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-group')),
			'error'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-error ui-state-error ui-corner-all')),
			'actions-bottom'	=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar')),
			'actions-top'		=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar'))
		));

    	$submitElt = tao_helpers_form_FormFactory::getElement('import', 'Free');
		$submitElt->setValue('<a href="#" class="form-submitter btn-success small"><span class="icon-import"></span> ' .__('Import').'</a>');
    	

		$this->form->setActions(array($submitElt), 'bottom');
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_helpers_form_FormContainer::initElements()
     */
    public function initElements()
    {
    	//create file upload form box
		$fileElt = tao_helpers_form_FormFactory::getElement('source', 'AsyncFile');
		$fileElt->setDescription(__("Add a zip file containing APIP items"));
    	if(isset($_POST['import_sent_apip'])){
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		}
		else{
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty', array('message' => '')));
		}
		$fileElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => array('application/zip', 'application/x-zip', 'application/x-zip-compressed', 'application/octet-stream'), 'extension' => array('zip'))),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => tao_helpers_Environment::getFileUploadLimit()))
		));
		$this->form->addElement($fileElt);
		
		$rollbackElt = tao_helpers_form_FormFactory::getElement('rollback', 'Checkbox');
		$rollbackElt->setOptions(array('error' => 'error', 'warning' => 'warning'));
		$rollbackElt->setDescription(__('Rollback on...'));
		$this->form->addElement($rollbackElt);
		
		$this->form->createGroup('file', __('Import an APIP 1.0 Package'), array('apip_desc', 'source', 'rollback'));
		
		
		$qtiSentElt = tao_helpers_form_FormFactory::getElement('import_sent_apip', 'Hidden');
		$qtiSentElt->setValue(1);
		$this->form->addElement($qtiSentElt);
    }

}