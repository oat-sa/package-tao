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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoQTI/actions/QTIform/class.TemplatesDrivenResponseOptions.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 25.01.2012, 16:01:55 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage actions_QTIform
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 127-0-1-1-53d7bbd:135145c7d03:-8000:000000000000367C-includes begin
// section 127-0-1-1-53d7bbd:135145c7d03:-8000:000000000000367C-includes end

/* user defined constants */
// section 127-0-1-1-53d7bbd:135145c7d03:-8000:000000000000367C-constants begin
// section 127-0-1-1-53d7bbd:135145c7d03:-8000:000000000000367C-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage actions_QTIform
 */
class taoQTI_actions_QTIform_TemplatesDrivenResponseOptions
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute responseProcessing
     *
     * @access public
     * @var ResponseProcessing
     */
    public $responseProcessing = null;

    /**
     * Short description of attribute response
     *
     * @access public
     * @var Response
     */
    public $response = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  ResponseProcessing responseProcessing
     * @param  Response response
     * @return mixed
     */
    public function __construct( taoQTI_models_classes_QTI_response_ResponseProcessing $responseProcessing,  taoQTI_models_classes_QTI_ResponseDeclaration $response)
    {
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003684 begin
		$this->responseProcessing = $responseProcessing;
        $this->response = $response;
        parent::__construct();
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003684 end
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003680 begin
        $this->form = tao_helpers_form_FormFactory::getForm('InteractionResponseProcessingForm');

		$this->form->setActions(array(), 'bottom');
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003680 end
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003682 begin
        $serialElt = tao_helpers_form_FormFactory::getElement('responseSerial', 'Hidden');
		$serialElt->setValue($this->response->getSerial());
		$this->form->addElement($serialElt);
		
        $rpElt = tao_helpers_form_FormFactory::getElement('responseprocessingSerial', 'Hidden');
		$rpElt->setValue($this->responseProcessing->getSerial());
		$this->form->addElement($rpElt);
		
		$mapKey = tao_helpers_Uri::encode(taoQTI_models_classes_QTI_response_Template::MAP_RESPONSE);
		$mapPointKey = tao_helpers_Uri::encode(taoQTI_models_classes_QTI_response_Template::MAP_RESPONSE_POINT);
		
		$availableTemplates = array(
			tao_helpers_Uri::encode(taoQTI_models_classes_QTI_response_Template::MATCH_CORRECT) => __('correct')
		);
		
		$interaction = $this->response->getAssociatedInteraction();
		if(!is_null($interaction)){
			switch(strtolower($interaction->getType())){
				case 'order':
				case 'graphicorder':{
					break;
				}
				case 'selectpoint';
				case 'positionobject':{
					$availableTemplates[$mapPointKey] = __('map point');
					break;
				}
				default:{
					$availableTemplates[$mapKey] = __('map');
				}
			}
		}
		
		$ResponseProcessingTplElt = tao_helpers_form_FormFactory::getElement('processingTemplate', 'Combobox');
		$ResponseProcessingTplElt->setDescription(__('Processing type'));
		$ResponseProcessingTplElt->setOptions($availableTemplates);
		$ResponseProcessingTplElt->setValue($this->responseProcessing->getTemplate($this->response));
		$this->form->addElement($ResponseProcessingTplElt);
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003682 end
    }

} /* end of class taoQTI_actions_QTIform_TemplatesDrivenResponseOptions */

?>