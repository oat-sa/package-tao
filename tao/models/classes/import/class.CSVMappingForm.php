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
 * This container initialize the form used to map class properties to data to be
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_models_classes_import_CSVMappingForm extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---
    /**
     * Suffix to append to the default values of the properties
     * @var string
     */
    const DEFAULT_VALUES_SUFFIX = '-taocsvdef';
    
    
    /**
     * Short description of method initForm
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        
        
    	$this->form = tao_helpers_form_FormFactory::getForm('mapping');
    	
    	$importElt = tao_helpers_form_FormFactory::getElement('import', 'Free');
		$importElt->setValue('<a href="#" class="form-submitter btn-success small"><span class="icon-import"></span> ' .__('Import').'</a>');
		$this->form->setActions(array($importElt), 'bottom');
		$this->form->setActions(array(), 'top');
    	
        
    }

	/**
	 * Short description of method initElements
	 *
	 * @access public
	 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
	 * @throws Exception
	 * @throws common_Exception
	 * @return mixed
	 */
    public function initElements()
    {

        if(!isset($this->options['class_properties'])){
    		throw new Exception('No class properties found');
    	}
    	if(!isset($this->options['csv_column'])){
    		throw new Exception('No csv columns found');
    	}
    	
        $columnsOptions = array();
        $columnsOptionsLabels = array();
    	$columnsOptionsLiteral =  array();
    	$columnsOptionsLiteral['csv_select'] = ' --- ' . __('Select') . ' --- ';
    	$columnsOptionsLiteral['csv_null']  = ' --- ' . __("Don't set");
        
        $columnsOptionsRanged = array();
        $columnsOptionsRanged['csv_select'] = ' --- ' . __('Select') . ' --- ';
        $columnsOptionsRanged['csv_null'] = ' --- ' . __('Use default value');
    	
    	// We build the list of CSV columns that can be mapped to
    	// the target class properties. 
    	if ($this->options[tao_helpers_data_CsvFile::FIRST_ROW_COLUMN_NAMES]){
	    	foreach($this->options['csv_column'] as $i => $column){
	    		$columnsOptions[$i.tao_models_classes_import_CsvImporter::OPTION_POSTFIX] = __('Column') . ' ' . ($i + 1) . ' : ' . $column;
                $columnsOptionsLabels[$i.tao_models_classes_import_CsvImporter::OPTION_POSTFIX] = $this->prepareString($column);
	    	}
    	}
    	else{
    		// We do not know column so we display more neutral information
    		// about columns to the end user.
    		for ($i = 0; $i < count($this->options['csv_column']); $i++){
				$columnsOptions[$i.tao_models_classes_import_CsvImporter::OPTION_POSTFIX] = __('Column') . ' ' . ($i + 1);
	    	}
    	}

    	$i = 0;
    	foreach($this->options['class_properties'] as $propertyUri => $propertyLabel){

    		$propElt = tao_helpers_form_FormFactory::getElement($propertyUri, 'Combobox');
    		$propElt->setDescription($propertyLabel);

            // literal or ranged?
            $options = array_key_exists($propertyUri, $this->options['ranged_properties'])
                ? array_merge($columnsOptionsRanged, $columnsOptions)
                : array_merge($columnsOptionsLiteral, $columnsOptions);

			$value = 'csv_select';
			if (isset($_POST[$propertyUri]) && isset($columnsOptions[$_POST[$propertyUri]])) {
				$value = $_POST[$propertyUri];
			}

            // We trying compare current label with option labels from file and set most suitable
            $label = $this->prepareString($propertyLabel);
            $value = key(preg_grep("/^$label$/", $columnsOptionsLabels)) ?: $value;

            $propElt->setOptions($options);
            $propElt->setValue($value);

    		$this->form->addElement($propElt);
    		
    		$i++;
    	}
    	$this->form->createGroup('property_mapping', __('Map the properties to the CSV columns'), array_keys($this->options['class_properties']));
    	
    	$ranged = array();
    	foreach($this->options['ranged_properties'] as $propertyUri => $propertyLabel){
    		$property = new core_kernel_classes_Property(tao_helpers_Uri::decode($propertyUri));
    		$propElt = tao_helpers_form_GenerisFormFactory::elementMap($property);
    		if(!is_null($propElt)){
    			$defName = tao_helpers_Uri::encode($property->getUri()) . self::DEFAULT_VALUES_SUFFIX;
    			$propElt->setName($defName);
				$propElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
    			$this->form->addElement($propElt);
    			$ranged[$defName] = $propElt;
    		}
    	}

    	if(count($this->options['ranged_properties']) > 0){
    		$this->form->createGroup('ranged_property', __('Define the default values'), array_keys($ranged));
		}
    	
    	$importFileEle = tao_helpers_form_FormFactory::getElement('importFile', 'Hidden');
    	$this->form->addElement($importFileEle);
    	
    	$optDelimiter = tao_helpers_form_FormFactory::getElement(tao_helpers_data_CsvFile::FIELD_DELIMITER, 'Hidden');
		$this->form->addElement($optDelimiter);
		
		$optEncloser = tao_helpers_form_FormFactory::getElement(tao_helpers_data_CsvFile::FIELD_ENCLOSER, 'Hidden');
		$this->form->addElement($optEncloser);
		
		$optMulti = tao_helpers_form_FormFactory::getElement(tao_helpers_data_CsvFile::MULTI_VALUES_DELIMITER, 'Hidden');
		$this->form->addElement($optMulti);

		$optFirstColumn = tao_helpers_form_FormFactory::getElement(tao_helpers_data_CsvFile::FIRST_ROW_COLUMN_NAMES.'_0', 'Hidden');
		$this->form->addElement($optFirstColumn);
		

    }

    protected function prepareString($value)
    {
        return mb_strtolower(preg_replace('/[\s\t\r\n-]/', '', $value));
    }

}
