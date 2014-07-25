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
 * A Translation Unit represents a single unit of translation of a software,
 * file, ... It has a source text in the original language and a target in which
 * text has to be translated.
 *
 * Example:
 * Source (English): The end is far away
 * Target (Yoda English): Far away the end is
 *
 * @author Jerome Bogaerts
 * @package tao
 * @since 2.2
 * @subpackage helpers_translation
 * @version 1.0
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Any Object that claims to be annotable should implement this interface.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('tao/helpers/translation/interface.Annotable.php');

/* user defined includes */
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:0000000000003478-includes begin
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:0000000000003478-includes end

/* user defined constants */
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:0000000000003478-constants begin
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:0000000000003478-constants end

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
 * @subpackage helpers_translation
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
        // section -64--88-56-1--5deb8f54:136cf746d4c:-8000:0000000000003948 begin
        $this->annotations = $annotations;
        // section -64--88-56-1--5deb8f54:136cf746d4c:-8000:0000000000003948 end
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

        // section -64--88-56-1--5deb8f54:136cf746d4c:-8000:0000000000003952 begin
        $returnValue = $this->annotations;
        // section -64--88-56-1--5deb8f54:136cf746d4c:-8000:0000000000003952 end

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
        // section -64--88-56-1--5deb8f54:136cf746d4c:-8000:0000000000003955 begin
        $this->annotations[$name] = $value;
        // section -64--88-56-1--5deb8f54:136cf746d4c:-8000:0000000000003955 end
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
        // section -64--88-56-1--5deb8f54:136cf746d4c:-8000:000000000000395C begin
        if (isset($this->annotations[$name])){
            unset($this->annotations[$name]);
        }
        // section -64--88-56-1--5deb8f54:136cf746d4c:-8000:000000000000395C end
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

        // section -64--88-56-1--1ef43195:136cfde50f6:-8000:0000000000003971 begin
        if (isset($this->annotations[$name])){
            $returnValue = array('name' => $name, 'value' => $this->annotations[$name]);
        }else{
            $returnValue = null;
        }
        // section -64--88-56-1--1ef43195:136cfde50f6:-8000:0000000000003971 end

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

        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:000000000000347F begin
        $returnValue = $this->source;
        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:000000000000347F end

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

        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:0000000000003481 begin
        $returnValue = $this->target;
        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:0000000000003481 end

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
        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:0000000000003483 begin
        $this->source = $source;
        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:0000000000003483 end
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
        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:0000000000003486 begin
        $this->target = $target;
        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:0000000000003486 end
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
        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:0000000000003489 begin
        // Default values for source and target languages are en-US.
        $this->setSourceLanguage(tao_helpers_translation_Utils::getDefaultLanguage());
        $this->setTargetLanguage(tao_helpers_translation_Utils::getDefaultLanguage());
        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:0000000000003489 end
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
        // section 10-13-1-85-4b6473d:1331c301495:-8000:000000000000351D begin
        $this->sourceLanguage = $sourceLanguage;
        $this->addAnnotation('sourceLanguage', $sourceLanguage);
        // section 10-13-1-85-4b6473d:1331c301495:-8000:000000000000351D end
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
        // section 10-13-1-85-4b6473d:1331c301495:-8000:0000000000003520 begin
        $this->targetLanguage = $targetLanguage;
        $this->addAnnotation('targetLanguage', $targetLanguage);
        // section 10-13-1-85-4b6473d:1331c301495:-8000:0000000000003520 end
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

        // section 10-13-1-85-4b6473d:1331c301495:-8000:0000000000003523 begin
        $returnValue = $this->sourceLanguage;
        // section 10-13-1-85-4b6473d:1331c301495:-8000:0000000000003523 end

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

        // section 10-13-1-85-4b6473d:1331c301495:-8000:0000000000003525 begin
        $returnValue = $this->targetLanguage;
        // section 10-13-1-85-4b6473d:1331c301495:-8000:0000000000003525 end

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

        // section 10-13-1-85--248fc0f4:133211c8937:-8000:0000000000003549 begin
        $returnValue = $this->getSourceLanguage() . '->' . $this->getTargetLanguage() . ':' .
        			   $this->getSource() . '-' . $this->getTarget();
        // section 10-13-1-85--248fc0f4:133211c8937:-8000:0000000000003549 end

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

        // section -64--88-1-7-576a6b36:1333bcb6e9d:-8000:000000000000322F begin
        $returnValue = $this->getSource() == $translationUnit->getSource();
        // section -64--88-1-7-576a6b36:1333bcb6e9d:-8000:000000000000322F end

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

        // section -64--88-1-7-576a6b36:1333bcb6e9d:-8000:0000000000003232 begin
        $returnValue = $this->getTarget() == $translationUnit->getTarget();
        // section -64--88-1-7-576a6b36:1333bcb6e9d:-8000:0000000000003232 end

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

        // section 127-0-1-1--60155064:1355488bb4c:-8000:0000000000003712 begin
        $returnValue = $this->getSourceLanguage() == $translationUnit->getSourceLanguage();
        // section 127-0-1-1--60155064:1355488bb4c:-8000:0000000000003712 end

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

        // section 127-0-1-1--60155064:1355488bb4c:-8000:0000000000003715 begin
        $returnValue = $this->getTargetLanguage() == $translationUnit->getTargetLanguage();
        // section 127-0-1-1--60155064:1355488bb4c:-8000:0000000000003715 end

        return (bool) $returnValue;
    }

} /* end of class tao_helpers_translation_TranslationUnit */

?>