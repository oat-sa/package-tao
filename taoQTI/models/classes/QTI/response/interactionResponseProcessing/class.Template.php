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
 * TAO -
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 09.02.2012, 16:51:48 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response_interactionResponseProcessing
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The response processing of a single interaction
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoQTI/models/classes/QTI/response/interactionResponseProcessing/class.InteractionResponseProcessing.php');

/* user defined includes */
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000003599-includes begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000003599-includes end

/* user defined constants */
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000003599-constants begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000003599-constants end

/**
 * Short description of class
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response_interactionResponseProcessing
 */
abstract class taoQTI_models_classes_QTI_response_interactionResponseProcessing_Template
    extends taoQTI_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method createByTemplate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string templateUri
     * @param  Response response
     * @param  Item item
     * @return taoQTI_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing
     */
    public static function createByTemplate($templateUri,  taoQTI_models_classes_QTI_ResponseDeclaration $response,  taoQTI_models_classes_QTI_Item $item)
    {
        $returnValue = null;

        // section 127-0-1-1--2c448032:1355d3fa7bf:-8000:00000000000037B1 begin
    switch ($templateUri) {
			case taoQTI_models_classes_QTI_response_Template::MATCH_CORRECT :
				$returnValue = self::create(taoQTI_models_classes_QTI_response_interactionResponseProcessing_MatchCorrectTemplate::CLASS_ID, $response, $item);
				break;
			case taoQTI_models_classes_QTI_response_Template::MAP_RESPONSE :
				$returnValue = self::create(taoQTI_models_classes_QTI_response_interactionResponseProcessing_MapResponseTemplate::CLASS_ID, $response, $item);
				break;
			case taoQTI_models_classes_QTI_response_Template::MAP_RESPONSE_POINT :
				$returnValue = self::create(taoQTI_models_classes_QTI_response_interactionResponseProcessing_MapResponsePointTemplate::CLASS_ID, $response, $item);
				break;
			default :
				throw new taoQTI_models_classes_QTI_ParsingException('Cannot create interactionResponseProcessing for unknown Template '.$templateUri);
		}
        // section 127-0-1-1--2c448032:1355d3fa7bf:-8000:00000000000037B1 end

        return $returnValue;
    }

} /* end of abstract class taoQTI_models_classes_QTI_response_interactionResponseProcessing_Template */

?>