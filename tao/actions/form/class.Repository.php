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
 * TAO - tao/actions/form/class.Repository.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 02.11.2012, 16:18:46 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Create a form from a  resource of your ontology. 
 * Each property will be a field, regarding it's widget.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/actions/form/class.Instance.php');

/* user defined includes */
// section 127-0-1-1-35689176:13832da9f32:-8000:0000000000003B3A-includes begin
// section 127-0-1-1-35689176:13832da9f32:-8000:0000000000003B3A-includes end

/* user defined constants */
// section 127-0-1-1-35689176:13832da9f32:-8000:0000000000003B3A-constants begin
// section 127-0-1-1-35689176:13832da9f32:-8000:0000000000003B3A-constants end

/**
 * Short description of class tao_actions_form_Repository
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */
class tao_actions_form_Repository
    extends tao_actions_form_Instance
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        // section 127-0-1-1-35689176:13832da9f32:-8000:0000000000003B3E begin
        parent::initElements();
        $ele = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL));
        $ele->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('NotEmpty'),        	
        	tao_helpers_form_FormFactory::getValidator('Url')
        ));
        $ele = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE));
        $ele->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
        
        $ele = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_GENERIS_VERSIONEDREPOSITORY_ENABLED));
        //prevent having neither enabled or disabled selected
        if (is_null($ele->getValue())) {
        	$ele->setValue(tao_helpers_Uri::encode(GENERIS_FALSE));
        }
        
		$ele = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN));
		$ele->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
        
		$ele = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH));
		$ele->setHelp( __('Path to the local working copy, it is where your local 
				version of your versioned Resource will be stored. ') . '/path/to/the/local/working_copy');
		$ele->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		        // section 127-0-1-1-35689176:13832da9f32:-8000:0000000000003B3E end
    }

} /* end of class tao_actions_form_Repository */

?>