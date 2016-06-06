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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
namespace oat\taoDeliveryRdf\view\form\export;

/**
 * Export form for assemblies
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 */
class ExportForm
    extends \tao_helpers_form_FormContainer
{
   
    /**
     * (non-PHPdoc)
     * @see tao_helpers_form_FormContainer::initForm()
     */
    public function initForm()
    {
    	$this->form = new \tao_helpers_form_xhtml_Form('export');
    }

    /**
     * (non-PHPdoc)
     * @see tao_helpers_form_FormContainer::initElements()
     */
    public function initElements()
    {

    	$fileName = '';
    	$instances = array();
    	if (isset($this->data['instance'])){
    		$instance = $this->data['instance'];
    		if ($instance instanceof \core_kernel_classes_Resource) {
    			$instances[$instance->getUri()] = $instance->getLabel();
    		}
    	}
    	elseif (isset($this->data['class'])) {
    		$class = $this->data['class'];
    		if ($class instanceof \core_kernel_classes_Class) {
				foreach($class->getInstances() as $instance){
					$instances[$instance->getUri()] = $instance->getLabel();
				}
    		}
		} else {
		    throw new \common_Exception('No class nor instance specified for export');
    	}
    	$instances = \tao_helpers_Uri::encodeArray($instances, \tao_helpers_Uri::ENCODE_ARRAY_KEYS);

    	$descElt = \tao_helpers_form_FormFactory::getElement('desc', 'Label');
		$descElt->setValue(__('Enables you to export a published delivery'));
		$this->form->addElement($descElt);

		$instanceElt = \tao_helpers_form_FormFactory::getElement('exportInstance', 'Radiobox');
		$instanceElt->setDescription(__('Delivery'));
		$instanceElt->setAttribute('checkAll', true);
		$instanceElt->setOptions($instances);
		$instanceElt->setValue(current(array_keys($instances)));
		
		$this->form->addElement($instanceElt);


		$this->form->createGroup('options', __('Export Options'), array('desc', 'exportInstance'));
    }

}
