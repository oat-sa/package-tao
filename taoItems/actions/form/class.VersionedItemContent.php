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
 * TAO - taoItems/actions/form/class.VersionedItemContent.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 25.04.2012, 15:41:44 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_actions_form_VersionedFile
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/actions/form/class.VersionedFile.php');

/* user defined includes */
// section 127-0-1-1-34f65b5e:136df48a4e6:-8000:0000000000004ADA-includes begin
// section 127-0-1-1-34f65b5e:136df48a4e6:-8000:0000000000004ADA-includes end

/* user defined constants */
// section 127-0-1-1-34f65b5e:136df48a4e6:-8000:0000000000004ADA-constants begin
// section 127-0-1-1-34f65b5e:136df48a4e6:-8000:0000000000004ADA-constants end

/**
 * Short description of class taoItems_actions_form_VersionedItemContent
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */
class taoItems_actions_form_VersionedItemContent
    extends tao_actions_form_VersionedFile
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getDownloadUrl
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return string
     */
    public function getDownloadUrl()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-34f65b5e:136df48a4e6:-8000:0000000000004AE1 begin
		if(!is_null($this->ownerInstance)){
			$returnValue = _url('directItemExport', 'ItemExport', 'taoItems', array('uri' => tao_helpers_Uri::encode($this->ownerInstance->getUri())));
		}
        // section 127-0-1-1-34f65b5e:136df48a4e6:-8000:0000000000004AE1 end

        return (string) $returnValue;
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        // section 127-0-1-1-34f65b5e:136df48a4e6:-8000:0000000000004AE7 begin
		parent::initElements();
		$this->form->removeElement(tao_helpers_Uri::encode(PROPERTY_FILE_FILENAME));
        // section 127-0-1-1-34f65b5e:136df48a4e6:-8000:0000000000004AE7 end
    }

    /**
     * Short description of method getDefaultRepository
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return core_kernel_versioning_Repository
     */
    public function getDefaultRepository()
    {
        $returnValue = null;

        // section 127-0-1-1--4b5c8f5d:136e9bb93ae:-8000:0000000000006ACE begin
		$returnValue = taoItems_models_classes_ItemsService::singleton()->getDefaultFileSource();
        // section 127-0-1-1--4b5c8f5d:136e9bb93ae:-8000:0000000000006ACE end

        return $returnValue;
    }

} /* end of class taoItems_actions_form_VersionedItemContent */

?>