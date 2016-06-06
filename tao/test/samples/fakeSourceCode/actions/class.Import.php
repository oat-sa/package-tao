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
 * This container initialize the import form.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_actions_form_Import
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute formats
     *
     * @access protected
     * @var array
     */
    protected $formats = array('csv' => 'CSV', 'rdf' => 'RDF');

    /**
     * Short description of attribute UPLOAD_MAX
     *
     * @access protected
     * @var int
     */
    const UPLOAD_MAX = 3000000;

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
        
        
    	$this->form = tao_helpers_form_FormFactory::getForm('import');
    	
    	$nextButton = true;
    	if(isset($_POST['format'])){
			if($_POST['format'] != 'csv'){
				$nextButton = false;
			}
    	}
    	
    	$submitElt = tao_helpers_form_FormFactory::getElement('import', 'Free');
    	if($nextButton){
			$submitElt->setValue('<a href="#" class="form-submitter btn-success small"><span class="icon-next"></span> ' .__('Next').'</a>');
    	}
    	else{
			$submitElt->setValue('<a href="#" class="form-submitter btn-success small"><span class="icon-import"></span> ' .__('Import').'</a>');
    	}
		$this->form->setActions(array($submitElt), 'bottom');
		$this->form->setActions(array(), 'top');
    	
        
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
        
        
    	//create the element to select the import format
    	$formatElt = tao_helpers_form_FormFactory::getElement('format', 'Radiobox');
    	$formatElt->setDescription(__(' Please select the input data format to import '));
    	
    	//mandatory field
    	$formatElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
    	$formatElt->setOptions($this->formats);

    	//shortcut: add the default value here to load the first time the form is defined
		if(count($this->formats) == 1){
			foreach($this->formats as $formatKey => $format){
				$formatElt->setValue($formatKey);
			}
		}
		if(isset($_POST['format'])){
			if(array_key_exists($_POST['format'], $this->formats)){
				$formatElt->setValue($_POST['format']);
			}
		}
		
    	$this->form->addElement($formatElt);
    	$this->form->createGroup('formats', __('Supported formats to import'), array('format'));
    	
    	//load dynamically the method regarding the selected format 
    	if(!is_null($formatElt->getValue())){
    		$method = "init".strtoupper($formatElt->getValue())."Elements";
    		
    		if(method_exists($this, $method)){
    			$this->$method();
    		}
    	}
    	
        
    }

    /**
     * Short description of method initCSVElements
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initCSVElements()
    {
        
        
    	$adapter = new tao_helpers_data_GenerisAdapterCsv();
		$options = $adapter->getOptions();
		
		//create import options form
		foreach($options as $optName => $optValue){
			(is_bool($optValue))  ? $eltType = 'Checkbox' : $eltType = 'Textbox';
			
			$optElt = tao_helpers_form_FormFactory::getElement($optName, $eltType);
			$optElt->setDescription(tao_helpers_Display::textCleaner($optName, ' '));
			$optElt->setValue(addslashes($optValue));
			
			$optElt->addAttribute("size", ($optName == 'column_order') ? 40 : 6);
			if(is_null($optValue) || $optName == 'line_break'){
				$optElt->addAttribute("disabled", "true");
			}
			$optElt->setValue($optValue);
			if($eltType == 'Checkbox'){
				$optElt->setOptions(array($optName => ''));
				$optElt->setValue($optName);
			}
			if(!preg_match("/column/", strtolower($optName))){
				$optElt->addValidator(
					tao_helpers_form_FormFactory::getValidator('NotEmpty')
				);
			}
			$this->form->addElement($optElt);
		}
		$this->form->createGroup('options', __('CSV Options'), array_keys($options));
		

		$descElt = tao_helpers_form_FormFactory::getElement('csv_desc', 'Label');
		$descElt->setValue(__("Please upload a CSV file formated as \"defined\" %min by %max the options above."));
		$this->form->addElement($descElt);
		
		//create file upload form box
		$fileElt = tao_helpers_form_FormFactory::getElement('source', 'AsyncFile');
		$fileElt->setDescription(__("Add the source file"));
  	  	if(isset($_POST['import_sent_csv'])){
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		}
		else{
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty', array('message' => '')));
		}
		$fileElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => array('text/plain', 'text/csv', 'text/comma-separated-values', 'application/csv', 'application/csv-tab-delimited-table'), 'extension' => array('csv', 'txt'))),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => self::UPLOAD_MAX))
		));
		
		$this->form->addElement($fileElt);
		$this->form->createGroup('file', __('Upload CSV File'), array('csv_desc', 'source'));
		
		$csvSentElt = tao_helpers_form_FormFactory::getElement('import_sent_csv', 'Hidden');
		$csvSentElt->setValue(1);
		$this->form->addElement($csvSentElt);
    	
        
    }

    /**
     * Short description of method initRDFElements
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initRDFElements()
    {
        
        
    	$descElt = tao_helpers_form_FormFactory::getElement('rdf_desc', 'Label');
		$descElt->setValue(__("Please upload \t an RDF file.\n\n"));
		$this->form->addElement($descElt);
		
		//create file upload form box
		$fileElt = tao_helpers_form_FormFactory::getElement('source', 'AsyncFile');
		$fileElt->setDescription(__("Add the source file"));
  	  	if(isset($_POST['import_sent_rdf'])){
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		}
		else{
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty', array('message' => '')));
		}
		$fileElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => array('text/xml', 'application/rdf+xml', 'application/xml'), 'extension' => array('rdf', 'rdfs'))),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => self::UPLOAD_MAX))
		));
		
		$this->form->addElement($fileElt);
		$this->form->createGroup('file', __('Upload RDF File'), array('rdf_desc', 'source'));
		
		$rdfSentElt = tao_helpers_form_FormFactory::getElement('import_sent_rdf', 'Hidden');
		$rdfSentElt->setValue(1);
		$this->form->addElement($rdfSentElt);
    	
        
    }

}

?>