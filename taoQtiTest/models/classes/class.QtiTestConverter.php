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
* Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
*/
use qtism\data\storage\xml\XmlDocument;
use qtism\data\QtiComponent;
use qtism\data\QtiComponentCollection;
use qtism\common\datatypes\Duration;
use qtism\common\collections\IntegerCollection;
use qtism\common\collections\StringCollection;
use qtism\data\ViewCollection;
use qtism\data\View;

/**
 * This class helps you to convert a QTITest from the qtism library.
 * It supports only JSON convertion, but uses assoc arrays as transitional format.
 *
 * This converter will be replaced by a JSON Marshaller from inside the qtism lib.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 *
 * @access public
 * @package taoQtiTest
 *
 */
class taoQtiTest_models_classes_QtiTestConverter
{

    /**
     * The instance of the XmlDocument that represents the QTI Test.
     *
     * This is the pivotal class.
     *
     * @var XmlDocument
     */
    private $doc;

    /**
     * Instantiate the converter using a QTITest document.
     *
     * @param \qtism\data\storage\xml\XmlDocument $doc
     */
    public function __construct(XmlDocument $doc)
    {
        $this->doc = $doc;
    }

    /**
     * Converts the test from the document to JSON.
     *
     * @return a json string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Converts the test from the document to an array
     *
     * @return the test data as array
     */
    public function toArray()
    {
        try {
            return $this->componentToArray($this->doc->getDocumentComponent());
        } catch (ReflectionException $re) {
            common_Logger::e($re->getMessage());
            common_Logger::d($re->getTraceAsString());
            throw new taoQtiTest_models_classes_QtiTestConverterException('Unable to convert the QTI Test to json: ' . $re->getMessage());
        }
    }

    /**
     * Popoulate the document using the JSON parameter.
     *
     * @param string $json a valid json object (one that comes from the toJson method).
     */
    public function fromJson($json)
    {
        try {
            $data = json_decode($json, true);
            if (is_array($data)) {
                $this->arrayToComponent($data);
            }
        } catch (ReflectionException $re) {
            common_Logger::e($re->getMessage());
            common_Logger::d($re->getTraceAsString());
            throw new taoQtiTest_models_classes_QtiTestConverterException('Unable to create the QTI Test from json: ' . $re->getMessage());
        }
    }

    /**
     * Converts a QTIComponent to an assoc array (instances variables to key/val), using reflection.
     *
     * @param \qtism\data\QtiComponent $component
     * @return array
     */
    private function componentToArray(QtiComponent $component)
    {
        $array = array(
            'qti-type' => $component->getQtiClassName()
        );

        $reflector = new ReflectionClass($component);

        foreach ($this->getProperties($reflector) as $property) {
            $value = $this->getValue($component, $property);
            if ($value !== null) {
                $key = $property->getName();
                if ($value instanceof QtiComponentCollection) {
                    $array[$key] = array();
                    foreach ($value as $item) {
                        $array[$key][] = $this->componentToArray($item);
                    }
                } else
                    if ($value instanceof ViewCollection) {
                        $array[$property->getName()] = array();
                        foreach ($value as $item) {
                            $array[$property->getName()][] = View::getNameByConstant($item);
                        }
                    } else
                        if ($value instanceof QtiComponent) {
                            $array[$property->getName()] = $this->componentToArray($value);
                        } else
                            if ($value instanceof Duration) {
                                $array[$property->getName()] = $value->getSeconds(true);
                            } else
                                if ($value instanceof IntegerCollection || $value instanceof StringCollection) {
                                    $array[$property->getName()] = array();
                                    foreach ($value as $item) {
                                        $array[$property->getName()][] = $item;
                                    }
                                } else {
                                    $array[$property->getName()] = $value;
                                }
            }
        }

        return $array;
    }

    /**
     * Get the class properties.
     *
     * @param ReflectionClass $reflector
     * @param array $childrenProperties for recursive usage only
     * @return the list of properties
     */
    private function getProperties(ReflectionClass $reflector, array $childrenProperties = array())
    {
        $properties = array_merge($childrenProperties, $reflector->getProperties());
        if ($reflector->getParentClass() != null) {
            $properties = $this->getProperties($reflector->getParentClass(), $properties);
        }
        return $properties;
    }

    /**
     * Call the getter from a relfection property, to get the value
     *
     * @param \qtism\data\QtiComponent $component
     * @param ReflectionProperty $property
     * @return the value produced by the getter
     */
    private function getValue(QtiComponent $component, ReflectionProperty $property)
    {
        $value = null;
        $getterProps = array(
            'get',
            'is',
            'does',
            'must'
        );
        foreach ($getterProps as $getterProp) {
            $getterName = $getterProp . ucfirst($property->getName());
            try {
                $method = new ReflectionMethod($component, $getterName);
                if ($method->isPublic()) {
                    $value = $component->{$getterName}();
                }
            } catch (ReflectionException $re) { // this must be ignored
                continue;
            }
            return $value;
        }
    }

    /**
     * Call the setter to assign a value to a component using a relfection property
     *
     * @param \qtism\data\QtiComponent $component
     * @param ReflectionProperty $property
     * @param type $value
     */
    private function setValue(QtiComponent $component, ReflectionProperty $property, $value)
    {
        $setterName = 'set' . ucfirst($property->getName());
        try {

            $method = new ReflectionMethod($component, $setterName);
            if ($method->isPublic()) {
                $component->{$setterName}($value);
            }
        } catch (ReflectionException $re) {} // this must be ignored
    }

    /**
     * If a class is explicitly defined for a property, we get it (from the setter's parameter...).
     *
     * @param \qtism\data\QtiComponent $component
     * @param ReflectionProperty $property
     * @return null or the ReflectionClass
     */
    public function getPropertyClass(QtiComponent $component, ReflectionProperty $property)
    {
        $setterName = 'set' . ucfirst($property->getName());
        try {

            $method = new ReflectionMethod($component, $setterName);
            $parameters = $method->getParameters();

            if (count($parameters) == 1) {
                $param = $parameters[0];
                return $param->getClass();
            }
        } catch (ReflectionException $re) {}

        return null;
    }

    /**
     * Converts an assoc array to a QtiComponent using reflection
     *
     * @param array $testArray the assoc array
     * @param \qtism\data\QtiComponent $parent for recursive usage only
     * @param type $attach if we want to attach the component to it's parent or return it
     * @return see above
     */
    private function arrayToComponent(array $testArray, QtiComponent $parent = null, $attach = true)
    {
        if (isset($testArray['qti-type']) && ! empty($testArray['qti-type'])) {

            $compName = $this->lookupClass($testArray['qti-type']);

            if (! empty($compName)) {

                $reflector = new ReflectionClass($compName);
                $component = $this->createInstance($reflector, $testArray);

                $properties = array();
                foreach ($this->getProperties($reflector) as $property) {
                    $properties[$property->getName()] = $property;
                }

                foreach ($testArray as $key => $value) {

                    if (array_key_exists($key, $properties)) {

                        $class = $this->getPropertyClass($component, $properties[$key]);

                        if (is_array($value) && array_key_exists('qti-type', $value)) {

                            $this->arrayToComponent($value, $component, true);
                        } else {
                            $assignableValue = $this->componentValue($value, $class);
                            if (! is_null($assignableValue)) {
                                $this->setValue($component, $properties[$key], $assignableValue);
                            }
                        }
                    }
                }

                if ($attach) {
                    if (is_null($parent)) {
                        $this->doc->setDocumentComponent($component);
                    } else {
                        $parentReflector = new ReflectionClass($parent);
                        foreach ($this->getProperties($parentReflector) as $property) {
                            if ($property->getName() == $testArray['qti-type']) {
                                $this->setValue($parent, $property, $component);
                                break;
                            }
                        }
                    }
                }
                return $component;
            }
        }
    }

    /**
     * Get the value according to it's type and class.
     *
     * @param type $value
     * @param type $class
     * @return \qtism\common\datatypes\Duration
     */
    private function componentValue($value, $class)
    {
        if (! is_null($class)) {
            if (is_array($value)) {
                return $this->createComponentCollection(new ReflectionClass($class->name), $value);
            } else
                if ($class->name == 'qtism\common\datatypes\Duration') {
                    return new qtism\common\datatypes\Duration('PT' . $value . 'S');
                }
        }
        return $value;
    }

    /**
     * Instantiate and fill a QtiComponentCollection
     *
     * @param ReflectionClass $class
     * @param type $values
     * @return \qtism\data\QtiComponentCollection|null
     */
    private function createComponentCollection(ReflectionClass $class, $values)
    {
        $collection = $class->newInstance();
        if ($collection instanceof ViewCollection) {
            foreach ($values as $value) {
                $collection[] = View::getConstantByName($value);
            }
            return $collection;
        }
        if ($collection instanceof QtiComponentCollection) {
            foreach ($values as $value) {
                $collection->attach($this->arrayToComponent($value, null, false));
            }
            return $collection;
        }
        if ($collection instanceof IntegerCollection || $collection instanceof StringCollection) {
            foreach ($values as $value) {
                $collection[] = $value;
            }
            return $collection;
        }

        return null;
    }

    /**
     * Call the constructor with the required parameters of a QtiComponent.
     *
     * @param ReflectionClass $class
     * @param array|string $properties
     * @return the QtiComponent's instance
     */
    private function createInstance(ReflectionClass $class, $properties)
    {
        $arguments = array();
        if ($class->implementsInterface('qtism\common\enums\Enumeration') && is_string($properties)) {
            $enum = $class->newInstance();
            return $enum->getConstantByName($properties);
        }
        $constructor = $class->getConstructor();
        if (is_null($constructor)) {
            return $class->newInstance();
        }
        $docComment = $constructor->getDocComment();
        foreach ($class->getConstructor()->getParameters() as $parameter) {
            if (! $parameter->isOptional()) {
                $name = $parameter->getName();
                $paramClass = $parameter->getClass();
                if (! is_null($paramClass)) {
                    if (is_array($properties[$name])) {
                        $arguments[] = $this->createComponentCollection(new ReflectionClass($paramClass->name), $properties[$name]);
                    }
                } else
                    if (array_key_exists($name, $properties)) {
                        $arguments[] = $properties[$name];
                    } else {
                        $hint = $this->getHint($docComment, $name);
                        switch ($hint) {
                            case 'int':
                                $arguments[] = 0;
                                break;
                            case 'integer':
                                $arguments[] = 0;
                                break;
                            case 'boolean':
                                $arguments[] = false;
                                break;
                            case 'string':
                                $arguments[] = '';
                                break;
                            case 'array':
                                $arguments[] = array();
                                break;
                            default:
                                $arguments[] = null;
                                break;
                        }
                    }
            }
        }

        return $class->newInstanceArgs($arguments);
    }

    /**
     * Get the type of parameter from the jsdoc (yes, I know...
     * but this is temporary ok!)
     *
     * @param type $docComment
     * @param type $varName
     * @return null|array
     */
    private function getHint($docComment, $varName)
    {
        $matches = array();
        $count = preg_match_all('/@param[\t\s]*(?P<type>[^\t\s]*)[\t\s]*\$(?P<name>[^\t\s]*)/sim', $docComment, $matches);
        if ($count > 0) {
            foreach ($matches['name'] as $n => $name) {
                if ($name == $varName) {
                    return $matches['type'][$n];
                }
            }
        }
        return null;
    }

    /**
     * get the namespaced class name
     *
     * @param type $name the short class name
     * @return the long class name
     */
    private function lookupClass($name)
    {
        $namespaces = array(
            'qtism\\common\\datatypes\\',
            'qtism\\data\\',
            'qtism\\data\\content\\',
            'qtism\\data\\content\\xhtml\\',
            'qtism\\data\\content\\xhtml\\lists\\',
            'qtism\\data\\content\\xhtml\\presentation\\',
            'qtism\\data\\content\\xhtml\\tables\\',
            'qtism\\data\\content\\xhtml\\text\\',
            'qtism\\data\\content\\interactions\\',
            'qtism\\data\\content\\expressions\\',
            'qtism\\data\\content\\operators\\',
            'qtism\\data\\processing\\',
            'qtism\\data\\rules\\',
            'qtism\\data\\state\\'
        );
        foreach ($namespaces as $namespace) { // this could be cached
            $className = $namespace . ucfirst($name);
            if (class_exists($className, true)) {
                return $className;
            }
        }
    }
}
?>
