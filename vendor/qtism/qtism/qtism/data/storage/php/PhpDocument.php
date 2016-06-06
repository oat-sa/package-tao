<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package
 */

namespace qtism\data\storage\php;

use qtism\common\datatypes\Coords;
use qtism\data\ExtendedAssessmentSection;
use qtism\data\QtiDocument;
use qtism\data\storage\php\marshalling\PhpQtiDatatypeMarshaller;
use qtism\common\datatypes\QtiDatatype;
use qtism\data\AssessmentSection;
use qtism\data\processing\ResponseProcessing;
use qtism\data\AssessmentItem;
use qtism\data\AssessmentTest;
use qtism\data\storage\php\marshalling\PhpCollectionMarshaller;
use qtism\data\storage\php\marshalling\PhpQtiComponentMarshaller;
use qtism\data\storage\php\marshalling\PhpArrayMarshaller;
use qtism\common\beans\Bean;
use qtism\common\collections\AbstractCollection;
use qtism\data\storage\php\marshalling\PhpScalarMarshaller;
use qtism\common\storage\MemoryStream;
use qtism\data\storage\php\marshalling\PhpMarshallingContext;
use qtism\data\QtiComponent;
use qtism\data\storage\php\Utils as PhpUtils;
use \SplStack;
use \Exception;

/**
 * Represents a PHP source code document containing the appropriate source code
 * to load a QTI Component and the components it contains.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PhpDocument extends QtiDocument {

    /**
     * Create a new PhpDocument object.
     *
     * @param QtiComponent $documentComponent The root QtiComponent object to contained by the PhpDocument.
     */
    public function __construct($version = '2.1', QtiComponent $documentComponent = null) {
        parent::__construct($version, $documentComponent);
    }

    /**
     * Save the PhpDocument to a specific location.
     *
     * @param string $url A URL (Uniform Resource Locator) describing where to save the document.
     * @return $this
     * @throws PhpStorageException If an error occurs while saving.
     */
    public function save($url) {
        try {
            $stream = $this->transformToPhp();
            file_put_contents($url, $stream->getBinary());
        }
        catch (StreamAccessException $e) {
            $msg = "An error occured while writing the PHP source code stream.";
            throw new PhpStorageException($msg, 0, $e);
        }
    }

    /**
     * Return components as string
     *
     * @return Stream
     * @throws PhpStorageException
     */
    public function saveToString()
    {
        try {
            $memoryStream = $this->transformToPhp();
            return $memoryStream->getBinary();
        }
        catch (StreamAccessException $e) {
            $msg = "An error occured while writing the PHP source code stream.";
            throw new PhpStorageException($msg, 0, $e);
        }
    }

    /**
     * Convert components to php source
     *
     * @return MemoryStream
     * @throws PhpStorageException
     */
    protected function transformToPhp()
    {
        $stack = new SplStack();
        $stack->push($this->getDocumentComponent());

        // 1st/2nd pass marker.
        $marker = array();

        // marshalling context.
        $stream = new MemoryStream();
        $stream->open();
        $streamAccess = new PhpStreamAccess($stream);
        $streamAccess->writeOpeningTag();
        $ctx = new PhpMarshallingContext($streamAccess);
        $ctx->setFormatOutput(true);

        while (count($stack) > 0) {

            $component = $stack->pop();
            $isMarked = in_array($component, $marker, true);

            if ($isMarked === false && ($component instanceof QtiComponent)) {
                // -- QtiComponent node, 1st pass.
                // Mark as explored.
                array_push($marker, $component);
                // Ask for a 2nd pass.
                $stack->push($component);

                // Let's look at the Bean properties and ask for a future exploration.
                $bean = new Bean($component, false, self::getBaseImplementation($component));
                $ctorGetters = $bean->getConstructorGetters();
                $bodyGetters = $bean->getGetters(true);
                $getters = array_reverse(array_merge($bodyGetters->getArrayCopy(), $ctorGetters->getArrayCopy()));

                foreach ($getters as $getter) {
                    $stack->push(call_user_func(array($component, $getter->getName())));
                }
            }
            // Warning!!! Check for Coords Datatype objects. Indeed, it extends AbstractCollection, but must not be considered as it is.
            else if ($isMarked === false && ($component instanceof AbstractCollection && !$component instanceof Coords)) {
                // AbstractCollection node, 1st pass.
                // Mark as explored.
                array_push($marker, $component);
                // Ask for a 2nd pass.
                $stack->push($component);

                // Explore all values of the collection please!
                $values = array_reverse($component->getArrayCopy());
                foreach ($values as $val) {
                    $stack->push($val);
                }
            }
            else if ($isMarked === true && $component instanceof QtiComponent) {
                // QtiComponent, 2nd pass.
                $marshaller = new PhpQtiComponentMarshaller($ctx, $component);
                $marshaller->setAsInstanceOf(self::getBaseImplementation($component));

                if ($component === $this->getDocumentComponent()) {
                    $marshaller->setVariableName('rootcomponent');
                }

                $marshaller->marshall();
            }
            else if ($component instanceof QtiDatatype) {
                // Leaf node QtiDataType.
                $marshaller = new PhpQtiDatatypeMarshaller($ctx, $component);
                $marshaller->marshall();
            }
            else if ($isMarked === true && $component instanceof AbstractCollection) {
                // AbstractCollection, 2nd pass.
                $marshaller = new PhpCollectionMarshaller($ctx, $component);
                $marshaller->marshall();
            }
            else if (PhpUtils::isScalar($component) === true) {
                // Leaf node (QtiDatatype or PHP scalar (including the null value)).
                $marshaller = new PhpScalarMarshaller($ctx, $component);
                $marshaller->marshall();
            }
            else if (is_array($component) === true) {
                // Leaf node array.
                $marshaller = new PhpArrayMarshaller($ctx, $component);
                $marshaller->marshall();
            }
            else {
                $msg = "Datatype '" . gettype($component) . "' cannot be handled by the PhpDocument::save() method.";
                throw new PhpStorageException($msg);
            }
        }

        return $stream;
    }

    /**
     * Load a PHP QTI document at the specified URL.
     *
     * @param string $url A URL (Uniform Resource Locator) describing where to find the PHP document to load.
     * @throws PhpStorageException If an error occurs while loading the PHP file located at $url.
     */
    public function load($url) {
        if (is_readable($url) === false) {
            $msg = "The PHP document located at '${url}' is not readable or does not exist.";
            throw new PhpStorageException($msg);
        }

        try {
            require($url);
            $this->setDocumentComponent($rootcomponent);
            $this->setUrl($url);
        }
        catch (Exception $e) {
            $msg = "A PHP Runtime Error occurred while executing the PHP source code representing the document to be loaded at '${url}'.";
            throw new PhpStorageException($msg, 0, $e);
        }
    }

    /**
     * Load a PHP QTI document using a stream resource.
     *
     * @param $data
     * @throws PhpStorageException
     */
    public function loadFromString($data)
    {
        try {
            if (empty($data)) {
                throw new Exception('Unable to find valid data to load.');
            }
            $data = trim($data);
            if (substr($data,0,5)==='<?php') {
                eval('?>' . $data);
            } else {
                eval($data);
            }
            $this->setDocumentComponent($rootcomponent);
        } catch (Exception $e) {
            $msg = "A PHP Runtime Error occurred while executing the PHP source code representing the document.";
            throw new PhpStorageException($msg, 0, $e);
        }
    }

    protected static function getBaseImplementation($object) {
        if ($object instanceof AssessmentTest) {
            return "qtism\\data\\AssessmentTest";
        }
        else if ($object instanceof AssessmentItem) {
            return "qtism\\data\\AssessmentItem";
        }
        else if ($object instanceof ResponseProcessing) {
            return "qtism\\data\\processing\\ResponseProcessing";
        }
        else if ($object instanceof ExtendedAssessmentSection) {
            return 'qtism\\data\\ExtendedAssessmentSection';
        }
        else if ($object instanceof AssessmentSection) {
            return "qtism\\data\\AssessmentSection";
        }
        else {
            return get_class($object);
        }
    }
}
