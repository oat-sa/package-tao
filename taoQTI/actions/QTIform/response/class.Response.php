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
 * TAO - taoItems\actions\QTIform\response\class.Response.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.01.2011, 11:32:47 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Sam, <sam@taotesting.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10074
 * @subpackage actions_QTIform_response
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Sam, <sam@taotesting.com>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050B8-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050B8-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050B8-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050B8-constants end

/**
 * Short description of class taoQTI_actions_QTIform_response_Response
 *
 * @abstract
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10074
 * @subpackage actions_QTIform_response
 */
abstract class taoQTI_actions_QTIform_response_Response
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute response
     *
     * @access protected
     * @var Response
     */
    protected $response = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  Response response
     */
    public function __construct( taoQTI_models_classes_QTI_ResponseDeclaration $response)
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050BC begin
		
		$this->response = $response;
		$returnValue = parent::__construct(array(), array());
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050BC end
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     */
    public function initForm()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050BF begin
		
		$this->form = tao_helpers_form_FormFactory::getForm('Response_Form');
		
		$saveElt = tao_helpers_form_FormFactory::getElement('save', 'Free');
		$saveElt->setValue("<a href='#' class='response-form-submitter' ><img src='".BASE_WWW."img/qtiAuthoring/update.png'  /> ".__('Save Responses and Feedbacks')."</a>");
		$actions[] = $saveElt;
		
		$this->form->setActions($actions, 'top');
		$this->form->setActions(array(), 'bottom');
			
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050BF end
    }

    /**
     * Short description of method getResponse
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return taoQTI_models_classes_QTI_ResponseDeclaration
     */
    public function getResponse()
    {
        $returnValue = null;

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050C1 begin
		$returnValue = $this->response;
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050C1 end

        return $returnValue;
    }

    /**
     * Short description of method setCommonElements
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return mixed
     */
    public function setCommonElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050C3 begin
		//serial
		$serialElt = tao_helpers_form_FormFactory::getElement('responseSerial', 'Hidden');
		$serialElt->setValue($this->response->getSerial());
		$this->form->addElement($serialElt);
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050C3 end
    }

} /* end of abstract class taoQTI_actions_QTIform_response_Response */

?>