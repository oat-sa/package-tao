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
 * TAO - tao\helpers\translation\class.POFile.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 07.06.2012, 13:55:30 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A TranslationFile aiming at translating a TAO Component
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('tao/helpers/translation/class.TaoTranslationFile.php');

/* user defined includes */
// section 10-13-1-85-73c9aa2d:134d14a8b30:-8000:00000000000038C6-includes begin
// section 10-13-1-85-73c9aa2d:134d14a8b30:-8000:00000000000038C6-includes end

/* user defined constants */
// section 10-13-1-85-73c9aa2d:134d14a8b30:-8000:00000000000038C6-constants begin
// section 10-13-1-85-73c9aa2d:134d14a8b30:-8000:00000000000038C6-constants end

/**
 * Short description of class tao_helpers_translation_POFile
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */
class tao_helpers_translation_POFile
    extends tao_helpers_translation_TaoTranslationFile
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute headers
     *
     * @access private
     * @var array
     */
    private $headers = array();

    // --- OPERATIONS ---

    /**
     * Short description of method addHeader
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name
     * @param  string value
     * @return void
     */
    public function addHeader($name, $value)
    {
        // section 10-13-1-85-73c9aa2d:134d14a8b30:-8000:00000000000038DA begin
        $this->headers[$name] = $value;
        // section 10-13-1-85-73c9aa2d:134d14a8b30:-8000:00000000000038DA end
    }

    /**
     * Short description of method removeHeader
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name
     * @return void
     */
    public function removeHeader($name)
    {
        // section 10-13-1-85-73c9aa2d:134d14a8b30:-8000:00000000000038DE begin
        unset($this->headers[$name]);
        // section 10-13-1-85-73c9aa2d:134d14a8b30:-8000:00000000000038DE end
    }

    /**
     * Short description of method getHeaders
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getHeaders()
    {
        $returnValue = array();

        // section 10-13-1-85--53f40a93:134d16b5c93:-8000:00000000000038E1 begin
        $returnValue = $this->headers;
        // section 10-13-1-85--53f40a93:134d16b5c93:-8000:00000000000038E1 end

        return (array) $returnValue;
    }

    /**
     * Get a collection of POTranslationUnits based on the $flag argument
     * If no Translation Units are found, an empty array is returned.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string flag A PO compliant string flag.
     * @return array
     */
    public function getByFlag($flag)
    {
        $returnValue = array();

        // section -64--88-56-1-6fd49a8c:137c6c06daf:-8000:0000000000003B17 begin
        foreach ($this->getTranslationUnits() as $tu){
            if ($tu->hasFlag($flag)){
                $returnValue[] = $tu;
            }
        }
        // section -64--88-56-1-6fd49a8c:137c6c06daf:-8000:0000000000003B17 end

        return (array) $returnValue;
    }

    /**
     * Get a collection of POTranslationUnits that have all the flags referenced
     * the $flags array. If no TranslationUnits are found, an empty array is
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array flags An array of PO compliant string flags.
     * @return array
     */
    public function getByFlags($flags)
    {
        $returnValue = array();

        // section -64--88-56-1-6fd49a8c:137c6c06daf:-8000:0000000000003B1D begin
        foreach ($this->getTranslationUnits() as $tu){
            $matching = true;
            foreach ($flags as $f){
                if (!$tu->hasFlag($f)){
                    $matching = false;
                    break;
                } 
            }
            
            if ($matching == true){
                $returnValue[] = $tu;
            }
            else{
                // Prepare next iteration.
                $matching = true;
            }
        }
        // section -64--88-56-1-6fd49a8c:137c6c06daf:-8000:0000000000003B1D end

        return (array) $returnValue;
    }

} /* end of class tao_helpers_translation_POFile */

?>