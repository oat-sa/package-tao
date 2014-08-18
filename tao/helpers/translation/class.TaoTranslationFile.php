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
 * A TranslationFile aiming at translating a TAO Component
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
abstract class tao_helpers_translation_TaoTranslationFile
    extends tao_helpers_translation_TranslationFile
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Id of the extension the translations belongs to.
     *
     * @access public
     * @var Integer
     */
    public $extensionId = null;

    // --- OPERATIONS ---

    /**
     * Gets the extensionId of the extension the translations belong to.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getExtensionId()
    {
        $returnValue = (string) '';

        
        

        return (string) $returnValue;
    }

    /**
     * Sets the extensionId of the extension the translations belong to.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string extensionId
     * @return mixed
     */
    public function setExtensionId($extensionId)
    {
        
        
    }

} /* end of abstract class tao_helpers_translation_TaoTranslationFile */

?>