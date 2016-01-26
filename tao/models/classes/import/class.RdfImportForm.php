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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Import form for RDF
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 */
class tao_models_classes_import_RdfImportForm
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
    	$this->form = new tao_helpers_form_xhtml_Form('export');
    	$submitElt = tao_helpers_form_FormFactory::getElement('import', 'Free');
		$submitElt->setValue('<a href="#" class="form-submitter btn-success small"><span class="icon-import"></span> ' .__('Import').'</a>');

		$this->form->setActions(array($submitElt), 'bottom');
		$this->form->setActions(array(), 'top');
    }
    
    /**
     * overriden
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
		//create file upload form box
		$fileElt = tao_helpers_form_FormFactory::getElement('source', 'AsyncFile');
		$fileElt->setDescription(__("Add an RDF/XML file"));
  	  	if(isset($_POST['import_sent_rdf'])){
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		}
		else{
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty', array('message' => '')));
		}
		$fileElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => array('text/xml', 'application/rdf+xml', 'application/xml'), 'extension' => array('rdf', 'rdfs'))),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => tao_helpers_Environment::getFileUploadLimit()))
		));
		
		$this->form->addElement($fileElt);
		$this->form->createGroup('file', __('Import Metadata from RDF file'), array('source'));
		
		$rdfSentElt = tao_helpers_form_FormFactory::getElement('import_sent_rdf', 'Hidden');
		$rdfSentElt->setValue(1);
		$this->form->addElement($rdfSentElt);
    }

}
