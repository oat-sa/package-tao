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
 * A Translation Unit represents a single unit of translation of a software,
 * file, ... It has a source text in the original language and a target in which
 * text has to be translated.
 *
 * Example:
 * Source (English): The end is far away
 * Target (Yoda English): Far away the end is
 *
 * @access public
 * @author Jerome Bogaerts
 * @package tao
 * @since 2.2
 
 * @version 1.0
 */
class tao_helpers_translation_TranslationUnit
        implements tao_helpers_translation_Annotable
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute source
     *
     * @access private
     * @var string
     */
    private $source = '';

    /**
     * Short description of attribute target
     *
     * @access private
     * @var string
     */
    private $target = '';

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
     * The annotations bound to this translation unit.
     *
     * @access private
     * @var array
     */
    private $annotations = array();

    /**
     * The context of the translation bound to this translation unit.
     *
     * @access private
     * @var string
     */
    private $context = '';

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
     * Gets the source text.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getSource()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->source;
        

        return (string) $returnValue;
    }

    /**
     * Gets the target text.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getTarget()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->target;
        

        return (string) $returnValue;
    }

    /**
     * Sets the source text.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string source
     * @return mixed
     */
    public function setSource($source)
    {
        
        $this->source = $source;
        
    }

    /**
     * Sets the target text.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string target
     * @return mixed
     */
    public function setTarget($target)
    {
        
        $this->target = $target;
        
    }

    /**
     * Creates a new instance of Translation Unit with specific source & target.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        
        // Default values for source and target languages are en-US.
        $this->setSourceLanguage(tao_helpers_translation_Utils::getDefaultLanguage());
        $this->setTargetLanguage(tao_helpers_translation_Utils::getDefaultLanguage());
        
    }

    /**
     * Short description of method setSourceLanguage
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
     * Short description of method setTargetLanguage
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
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }



    /**
     * Short description of method getSourceLanguage
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getSourceLanguage()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->sourceLanguage;
        

        return (string) $returnValue;
    }

    /**
     * Short description of method getTargetLanguage
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getTargetLanguage()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->targetLanguage;
        

        return (string) $returnValue;
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

        
        $returnValue = $this->getSourceLanguage() . '->' . $this->getTargetLanguage() . ':' .
        			   $this->getSource() . '-' . $this->getTarget();
        

        return (string) $returnValue;
    }

    /**
     * Short description of method hasSameTranslationUnitSource
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  TranslationUnit translationUnit
     * @return boolean
     */
    public function hasSameTranslationUnitSource( tao_helpers_translation_TranslationUnit $translationUnit)
    {
        $returnValue = (bool) false;

        
        $returnValue = $this->getSource() == $translationUnit->getSource();
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method hasSameTranslationUnitTarget
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  TranslationUnit translationUnit
     * @return boolean
     */
    public function hasSameTranslationUnitTarget( tao_helpers_translation_TranslationUnit $translationUnit)
    {
        $returnValue = (bool) false;

        
        $returnValue = $this->getTarget() == $translationUnit->getTarget();
        

        return (bool) $returnValue;
    }

    /**
     * Checks whether or not a given TranslationUnit has the same source
     * than the current instance.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  TranslationUnit translationUnit
     * @return boolean
     */
    public function hasSameTranslationUnitSourceLanguage( tao_helpers_translation_TranslationUnit $translationUnit)
    {
        $returnValue = (bool) false;

        
        $returnValue = $this->getSourceLanguage() == $translationUnit->getSourceLanguage();
        

        return (bool) $returnValue;
    }

    /**
     * Checks whether or not a given TranslationUnit has the same target
     * than the current instance.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  TranslationUnit translationUnit
     * @return boolean
     */
    public function hasSameTranslationUnitTargetLanguage( tao_helpers_translation_TranslationUnit $translationUnit)
    {
        $returnValue = (bool) false;

        
        $returnValue = $this->getTargetLanguage() == $translationUnit->getTargetLanguage();
        

        return (bool) $returnValue;
    }

} 