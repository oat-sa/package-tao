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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class taoItems_models_classes_Scale_Numerical
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 
 */
class taoItems_models_classes_Scale_Numerical
    extends taoItems_models_classes_Scale_Scale
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute lowerBound
     *
     * @access public
     * @var double
     */
    public $lowerBound = 0.0;

    /**
     * Short description of attribute upperBound
     *
     * @access public
     * @var double
     */
    public $upperBound = 0.0;

    /**
     * Short description of attribute CLASS_URI
     *
     * @access public
     * @var string
     */
    const CLASS_URI = 'http://www.tao.lu/Ontologies/TAOItem.rdf#NumericalScale';

    // --- OPERATIONS ---

} /* end of class taoItems_models_classes_Scale_Numerical */

?>