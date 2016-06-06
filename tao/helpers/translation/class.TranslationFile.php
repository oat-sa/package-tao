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
 * A translation file represents the translation of a file, software, item, ...
 * contains a list of Translation Units a source language and a target language.
 * File can be read and written by TranslationFileReader & TranslationFileWriter
 *
 * @access public
 * @author Jerome Bogaerts
 * @package tao
 * @see tao_model_classes_TranslationUnit
tao_model_classes_TranslationFileReader
tao_model_classes_TranslationFileWriter
 * @since 2.2
 
 * @version 1.0
 */
class tao_helpers_translation_TranslationFile
        implements tao_helpers_translation_Annotable
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute sourceLanguage
     *
     * @access private
     * @var string
     */
    private $sourceLanguage = '';

    /**
     * Short description of attribute targetLanguage
     *
     * @access private
     * @var string
     */
    private $targetLanguage = '';

    /**
     * Short description of attribute translationUnits
     *
     * @access private
     * @var array
     */
    private $translationUnits = array();

    /**
     * Ascending sort case-sensitive
     *
     * @access public
     * @var int
     */
    const SORT_ASC = 1;

    /**
     * Descending sort case-sensitive
     *
     * @access public
     * @var int
     */
    const SORT_DESC = 2;

    /**
     * Ascending sort case-insensitive
     *
     * @access public
     * @var int
     */
    const SORT_ASC_I = 3;

    /**
     * Descending sort case-insensitive.
     *
     * @access public
     * @var int
     */
    const SORT_DESC_I = 4;

    /**
     * The annotations bound to this translation file.
     *
     * @access private
     * @var array
     */
    private $annotations = array();

    // --- OPERATIONS ---

    /**
     * Sets the collection of annotations bound to this Translation Object.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array annotations An associative array of annotations where keys are the annotation names and values are annotation values.
     * @return void
     */
    public function setAnnotations($annotations)
    {
        
        $this->annotations = $annotations;
        
    }

    /**
     * Returns an associative array that represents a collection of annotations
     * keys are annotation names and values annotation values.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getAnnotations()
    {
        $returnValue = array();

        
        $returnValue = $this->annotations;
        

        return (array) $returnValue;
    }

    /**
     * Adds an annotation with a given name and value. If value is not provided,
     * annotation will be taken into account as a flag.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name The name of the annotation to add.
     * @param  string value The value of the annotation to add.
     * @return void
     */
    public function addAnnotation($name, $value = '')
    {
        
        $this->annotations[$name] = $value;
        
    }

    /**
     * Removes an annotation for a given annotation name.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name The name of the annotation to remove.
     * @return void
     */
    public function removeAnnotation($name)
    {
        
        if (isset($this->annotations[$name])){
            unset($this->annotations[$name]);
        }
        
    }

    /**
     * Get an annotation for a given annotation name. Returns an associative
     * where keys are 'name' and 'value'.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name
     * @return array
     */
    public function getAnnotation($name)
    {
        $returnValue = array();

        
        if (isset($this->annotations[$name])){
            $returnValue = array('name' => $name, 'value' => $this->annotations[$name]);
        }else{
            $returnValue = null;
        }
        

        return (array) $returnValue;
    }

    /**
     * Creates a new instance of TranslationFile for a specific source and
     * language.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        
        $this->setSourceLanguage(tao_helpers_translation_Utils::getDefaultLanguage());
        $this->setTargetLanguage(tao_helpers_translation_Utils::getDefaultLanguage());
        $this->setTranslationUnits(array());
        
    }

    /**
     * Gets the source language.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getSourceLanguage()
    {
        $returnValue = (string) '';

        
        return $this->sourceLanguage;
        

        return (string) $returnValue;
    }

    /**
     * Gets the target language.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getTargetLanguage()
    {
        $returnValue = (string) '';

        
        return $this->targetLanguage;
        

        return (string) $returnValue;
    }

    /**
     * Gets the collection of Translation Units representing the
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getTranslationUnits()
    {
        $returnValue = array();

        
        return $this->translationUnits;
        

        return (array) $returnValue;
    }

    /**
     * Sets the source language.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string sourceLanguage
     * @return mixed
     */
    public function setSourceLanguage($sourceLanguage)
    {
        
        $this->sourceLanguage = $sourceLanguage;
        $this->addAnnotation('sourceLanguage', $sourceLanguage);
        
    }

    /**
     * Sets the target language.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string targetLanguage
     * @return mixed
     */
    public function setTargetLanguage($targetLanguage)
    {
        
        $this->targetLanguage = $targetLanguage;
        $this->addAnnotation('targetLanguage', $targetLanguage);
        
    }

    /**
     * Sets the collection of TranslationUnits representing the file.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array translationUnits
     * @return mixed
     */
    public function setTranslationUnits($translationUnits)
    {
        
        $this->translationUnits = $translationUnits;
        
    }

    /**
     * Adds a TranslationUnit instance to the file. It is appenned at the end of
     * collection.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  TranslationUnit translationUnit
     * @return mixed
     */
    public function addTranslationUnit( tao_helpers_translation_TranslationUnit $translationUnit)
    {
        
        // If the translation unit exists, we replace the target with the new one if it exists.
        foreach($this->getTranslationUnits() as $tu) {
            
        	if ($tu->getSource() == $translationUnit->getSource()) {
        	    // If we are here, it means that this TU is being overriden by
        	    // another one having the same source...
        	    //
        	    // Let's make sure we don't override the existing one with an empty target!
        	    if ($translationUnit->getTarget() !== '') {
        	        $tu->setTarget($translationUnit->getTarget());
        	        $tu->setAnnotations($translationUnit->getAnnotations());
        	    }
    			
        		return;
        	}
        }
        
        // If we are here, it means that this TU does not exist.
        $translationUnit->setSourceLanguage($this->getSourceLanguage());
        $translationUnit->setTargetLanguage($this->getTargetLanguage());
        array_push($this->translationUnits, $translationUnit);

    }

    /**
     * Removes a given TranslationUnit from the collection of TranslationUnits
     * the file.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  TranslationUnit translationUnit
     * @return mixed
     */
    public function removeTranslationUnit( tao_helpers_translation_TranslationUnit $translationUnit)
    {
        
        $tus = $this->getTranslationUnits();
        for ($i = 0; $i < count($tus); $i++) {
        	if ($tus[$i] === $translationUnit) {
        		unset($tus[$i]);
        		break;
        	}
        }
        
        throw new tao_helpers_translation_TranslationException('Cannot remove Translation Unit. Not Found.');
        
    }

    /**
     * Short description of method __toString
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function __toString()
    {
        $returnValue = (string) '';

        
    	$returnValue = $this->getSourceLanguage() . '->' . $this->getTargetLanguage() . ':';
        foreach ($this->getTranslationUnits() as $tu) {
        	$returnValue .= $tu;
        }
        

        return (string) $returnValue;
    }

    /**
     * Adds a set of TranslationUnits to the existing set of TranslationUnits
     * in the TranslationFile. No duplicate entries will be made based on the
     * of the translation units.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array translationUnits
     * @return mixed
     */
    public function addTranslationUnits($translationUnits)
    {
        
        foreach ($translationUnits as $tu) {
        	$this->addTranslationUnit($tu);
        }
        
    }

    /**
     * Short description of method hasSameSource
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  TranslationUnit translationUnit
     * @return boolean
     */
    public function hasSameSource( tao_helpers_translation_TranslationUnit $translationUnit)
    {
        $returnValue = (bool) false;

        
        foreach ($this->getTranslationUnits() as $tu) {
        	if ($tu->hasSameTranslationUnitSource($translationUnit)) {
        		$returnValue = true;
        		break;
        	}
        }
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method hasSameTarget
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  TranslationUnit translationUnit
     * @return boolean
     */
    public function hasSameTarget( tao_helpers_translation_TranslationUnit $translationUnit)
    {
        $returnValue = (bool) false;

        
        foreach ($this->getTranslationUnits() as $tu) {
        	if ($tu->hasSameTranslationUnitTarget($translationUnit)) {
        		$returnValue = true;
        		break;
        	}
        }
        

        return (bool) $returnValue;
    }

    /**
     * Sorts and returns the TranslationUnits by Source with a specified sorting
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int sortingType
     * @return array
     */
    public function sortBySource($sortingType)
    {
        $returnValue = array();

        
        $returnValue = $this->getTranslationUnits();
        switch ($sortingType) {
        	case self::SORT_ASC:
        	case self::SORT_ASC_I:
        		// Ascending algorithm.
        		$aCode = (($sortingType == self::SORT_ASC_I) ? 'mb_strtolower($a->getSource(), "UTF-8")' : '$a->getSource()');
        		$bCode = (($sortingType == self::SORT_ASC_I) ? 'mb_strtolower($b->getSource(), "UTF-8")' : '$b->getSource()');
        		$cmpFunction = create_function('$a,$b', 'return strcmp(' . $aCode . ', ' . $bCode . ');');
        		usort($returnValue, $cmpFunction);
        	break;
        	
        	case self::SORT_DESC:
        	case self::SORT_DESC_I:
        		// Ascending algorithm.
        		$aCode = (($sortingType == self::SORT_DESC_I) ? 'mb_strtolower($a->getSource(), "UTF-8")' : '$a->getSource()');
        		$bCode = (($sortingType == self::SORT_DESC_I) ? 'mb_strtolower($b->getSource(), "UTF-8")' : '$b->getSource()');
        		$cmpFunction = create_function('$a,$b', 'return -1 * strcmp(' . $aCode . ', ' . $bCode . ');');
        		usort($returnValue, $cmpFunction);
        	break;
        }
        

        return (array) $returnValue;
    }

    /**
     * Sorts and returns the TranslationUnits by Target with a specified sorting
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int sortingType
     * @return array
     */
    public function sortByTarget($sortingType)
    {
        $returnValue = array();

        
        throw new tao_helpers_translation_TranslationException("Not yet implemtented.");
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getBySource
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  TranslationUnit translationUnit
     * @return tao_helpers_translation_TranslationUnit
     */
    public function getBySource( tao_helpers_translation_TranslationUnit $translationUnit)
    {
        $returnValue = null;

        
        foreach ($this->getTranslationUnits() as $tu) {
        	if ($tu->hasSameTranslationUnitSource($translationUnit)) {
        		$returnValue = $tu;
        		break;
        	}
        }
        

        return $returnValue;
    }

    /**
     * Short description of method getByTarget
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  TranslationUnit translationUnit
     * @return tao_helpers_translation_TranslationUnit
     */
    public function getByTarget( tao_helpers_translation_TranslationUnit $translationUnit)
    {
        $returnValue = null;

        
    	foreach ($this->getTranslationUnits() as $tu) {
        	if ($tu->hasSameTranslationUnitTarget($translationUnit)) {
        		$returnValue = $tu;
        		break;
        	}
        }
        

        return $returnValue;
    }

    /**
     * Counts the TranslationUnits within the TranslationFile.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return int
     */
    public function count()
    {
        $returnValue = (int) 0;

        
        $returnValue = count($this->getTranslationUnits());
        

        return (int) $returnValue;
    }

}

?>