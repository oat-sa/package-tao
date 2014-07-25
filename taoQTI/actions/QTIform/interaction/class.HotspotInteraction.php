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
 * TAO - taoItems\actions\QTIform\interaction\class.HotspotInteraction.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.01.2011, 11:32:50 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Sam, <sam@taotesting.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10321
 * @subpackage actions_QTIform_interaction
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoQTI_actions_QTIform_interaction_GraphicInteraction
 *
 * @author Sam, <sam@taotesting.com>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10319
 */
require_once('taoQTI/actions/QTIform/interaction/class.GraphicInteraction.php');

/* user defined includes */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050A0-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050A0-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050A0-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050A0-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10321
 * @subpackage actions_QTIform_interaction
 */
class taoQTI_actions_QTIform_interaction_HotspotInteraction
    extends taoQTI_actions_QTIform_interaction_GraphicInteraction
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     */
    public function initElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050A2 begin
		parent::setCommonElements();
		$this->form->addElement(taoQTI_actions_QTIform_AssessmentItem::createTextboxElement($this->getInteraction(), 'maxChoices', __('Maximum selectable choices')));
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050A2 end
    }

} /* end of class taoQTI_actions_QTIform_interaction_HotspotInteraction */

?>