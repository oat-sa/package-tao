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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2002-2008 (update and modification) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoSubjects\actions\form\class.Subject.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 06.12.2012, 17:48:11 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoSubjects
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This container initialize the user edition form.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('tao/actions/form/class.Users.php');

/* user defined includes */
// section 10-13-1-85-30abb332:13b711a69fc:-8000:0000000000003C75-includes begin
// section 10-13-1-85-30abb332:13b711a69fc:-8000:0000000000003C75-includes end

/* user defined constants */
// section 10-13-1-85-30abb332:13b711a69fc:-8000:0000000000003C75-constants begin
// section 10-13-1-85-30abb332:13b711a69fc:-8000:0000000000003C75-constants end

/**
 * Short description of class taoSubjects_actions_form_Subject
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoSubjects
 * @subpackage actions_form
 */
class taoSubjects_actions_form_Subject
    extends tao_actions_form_Users
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function initElements()
    {
        // section 10-13-1-85-30abb332:13b711a69fc:-8000:0000000000003C77 begin
        parent::initElements();
		$this->form->removeElement(tao_helpers_Uri::encode(PROPERTY_USER_DEFLG));
		$this->form->removeElement(tao_helpers_Uri::encode(PROPERTY_USER_ROLES));
        // section 10-13-1-85-30abb332:13b711a69fc:-8000:0000000000003C77 end
    }

} /* end of class taoSubjects_actions_form_Subject */

?>