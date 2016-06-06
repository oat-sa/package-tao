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

namespace oat\taoQtiItem\model\import;

use \tao_helpers_form_FormContainer;
use \tao_helpers_form_xhtml_Form;
use \tao_helpers_form_FormFactory;
use \tao_helpers_Environment;

/**
 * Import form for QTI Items (xml files)
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
class QtiItemImportForm
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
		$submitElt->setValue('<a href="#" class="form-submitter btn-success small"><span class="icon-import"></span> ' .__('Import').'</a>');

		$this->form->setActions(array($submitElt), 'bottom');
		$this->form->setActions(array(), 'top');
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_helpers_form_FormContainer::initElements()
     */
    public function initElements()
    {
    	
    	//create file upload form box
		$fileElt = tao_helpers_form_FormFactory::getElement('source', 'AsyncFile');
		$fileElt->setDescription(__("Add a QTI XML file"));
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
		
		$apipElt = tao_helpers_form_FormFactory::getElement('import_options', 'Checkbox');
		$apipElt->setOptions(array('apip' => __('APIP data')));
		$apipElt->setValues(array('apip'));
		$apipElt->setDescription(__('Import'));
		$this->form->addElement($apipElt);
		
		
		$this->form->createGroup('file', __('Import QTI 2.X Item'), array('source','import_options'));
		
		$qtiSentElt = tao_helpers_form_FormFactory::getElement('import_sent_qti', 'Hidden');
		$qtiSentElt->setValue(1);
		$this->form->addElement($qtiSentElt);
    }

}