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
 * Short description of class tao_helpers_translation_POFile
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
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
        
        $this->headers[$name] = $value;
        
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
        
        unset($this->headers[$name]);
        
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

        
        $returnValue = $this->headers;
        

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

        
        foreach ($this->getTranslationUnits() as $tu){
            if ($tu->hasFlag($flag)){
                $returnValue[] = $tu;
            }
        }
        

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
        

        return (array) $returnValue;
    }

    /**
     * Adds a TranslationUnit instance to the file. It is appenned at the end of
     * collection.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param tao_helpers_translation_TranslationUnit $translationUnit
     * @return mixed
     */
    public function addTranslationUnit(tao_helpers_translation_TranslationUnit $translationUnit)
    {

        // If the translation unit exists, we replace the target with the new one if it exists.
        // also now we take care about context
        /** @var tao_helpers_translation_TranslationUnit $tu */
        foreach ($this->getTranslationUnits() as $tu) {
            if ($tu->getSource() == $translationUnit->getSource() &&
                (!$translationUnit->getContext() || $tu->getContext() == $translationUnit->getContext())
            ) {

                $tu->setTarget($translationUnit->getTarget());
                $tu->setAnnotations($translationUnit->getAnnotations());

                return;
            }
        }

        // If we are here, it means that this TU does not exist.
        $translationUnit->setSourceLanguage($this->getSourceLanguage());
        $translationUnit->setTargetLanguage($this->getTargetLanguage());

        $tus = $this->getTranslationUnits();
        array_push($tus, $translationUnit);
        $this->setTranslationUnits($tus);
    }

}