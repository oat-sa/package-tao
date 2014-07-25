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
 * Export form for QTI packages
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */
class tao_models_classes_export_RdfExportForm
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
        // section 127-0-1-1-74d22378:1271a9c9d21:-8000:0000000000001ED5 begin

    	$this->form = new tao_helpers_form_xhtml_Form('export');

		$this->form->setDecorators(array(
			'element'			=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div')),
			'group'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-group')),
			'error'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-error ui-state-error ui-corner-all')),
			'actions-bottom'	=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar')),
			'actions-top'		=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar'))
		));

    	$exportElt = tao_helpers_form_FormFactory::getElement('export', 'Free');
		$exportElt->setValue( "<a href='#' class='form-submiter' ><img src='".TAOBASE_WWW."/img/export.png' /> ".__('Export')."</a>");

		$this->form->setActions(array($exportElt), 'bottom');
        // section 127-0-1-1-74d22378:1271a9c9d21:-8000:0000000000001ED5 end
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

    	$fileName = (isset($this->data['currentExtension'])) ? $this->data['currentExtension'] : '';
    	$instances = array();
    	if(isset($this->data['instance'])){
    		$instance = $this->data['instance'];
    		if($instance instanceof core_kernel_classes_Resource){
    			$fileName = strtolower(tao_helpers_Display::textCleaner($instance->getLabel(), '*'));
    			$instances[$instance->getUri()] = $instance->getLabel();
    		}
    	}
    	else {
    		if(isset($this->data['class'])){
	    		$class = $this->data['class'];
	    		if($class instanceof core_kernel_classes_Class){
					$fileName =  strtolower(tao_helpers_Display::textCleaner($class->getLabel(), '*'));
					foreach($class->getInstances() as $instance){
						$instances[$instance->getUri()] = $instance->getLabel();
					}
	    		}
    		}
    	}
    	$instances = tao_helpers_Uri::encodeArray($instances, tao_helpers_Uri::ENCODE_ARRAY_KEYS);

    	$descElt = tao_helpers_form_FormFactory::getElement('rdf_desc', 'Label');
		$descElt->setValue(__('Enables you to export an RDF file containing the selected namespaces or instances'));
		$this->form->addElement($descElt);

		$nameElt = tao_helpers_form_FormFactory::getElement('filename', 'Textbox');
		$nameElt->setDescription(__('File name'));
		$nameElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		$nameElt->setValue($fileName);
		$nameElt->setUnit(".rdf");
		$this->form->addElement($nameElt);

		$tplElt = new tao_helpers_form_elements_template_Template('rdftpl');
		$tplElt->setPath(TAO_TPL_PATH . '/form/rdfexport.tpl.php');
		$tplElt->setVariables(array(
			'instances'		=> $instances
		));
		$this->form->addElement($tplElt);


		$this->form->createGroup('options', __('Export Options'), array('rdf_desc', 'filename', 'rdftpl'));
    }

} /* end of class taoItems_actions_form_Export */

?>