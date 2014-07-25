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
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/actions/form/class.Item.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 08.09.2010, 16:04:58 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Create a form from a  resource of your ontology. 
 * Each property will be a field, regarding it's widget.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/actions/form/class.Instance.php');

/* user defined includes */
// section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002591-includes begin
// section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002591-includes end

/* user defined constants */
// section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002591-constants begin
// section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002591-constants end

/**
 * Short description of class taoItems_actions_form_Item
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */
class taoItems_actions_form_Item
    extends tao_actions_form_Instance
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

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
        // section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002593 begin
        
    	parent::initForm();
    	
    	$actions = $this->form->getActions();
    	
    	if(!tao_helpers_Context::check('STANDALONE_MODE')){
			/*
			$url = _url('itemVersionedContentIO', 'Items', 'taoItems', array(
				'uri'			=> tao_helpers_Uri::encode($this->instance->getUri()),
				'propertyUri'	=> tao_helpers_Uri::encode('http://www.tao.lu/Ontologies/TAOItem.rdf#ItemVersionedContent')
			));

			$itemVersionedContentIOElt = tao_helpers_form_FormFactory::getElement('itemVersionedContentIO', 'Free');
			$itemVersionedContentIOElt->setValue("<a href='{$url}' class='nav' ><img src='".BASE_WWW."/img/text-xml.png' alt='xml' class='icon' /> ".__('VersionedContent')."</a>");
			$actions[] = $itemVersionedContentIOElt;
			*/
			
	    		// Add content action
	    		$url = _url('itemContentIO', 'Items', 'taoItems', array(
	    			'uri'		=> tao_helpers_Uri::encode($this->instance->getUri()),
	    			'classUri'	=> tao_helpers_Uri::encode($this->clazz->getUri())
	    		));
	    		/*
	    		$itemContentIOElt = tao_helpers_form_FormFactory::getElement('itemContentIO', 'Free');
				$itemContentIOElt->setValue("<a href='{$url}' class='nav' ><img src='".BASE_WWW."/img/text-xml.png' alt='xml' class='icon' /> ".__('Content')."</a>");
				$actions[] = $itemContentIOElt;
				*/
				
    		
		}
		
		$this->form->setActions($actions, 'top');
		$this->form->setActions($actions, 'bottom');
    	
        // section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002593 end
    }

} /* end of class taoItems_actions_form_Item */

?>