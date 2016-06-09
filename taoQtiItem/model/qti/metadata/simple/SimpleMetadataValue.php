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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoQtiItem\model\qti\metadata\simple;

use oat\taoQtiItem\model\qti\metadata\MetadataValue;
use \InvalidArgumentException;

/**
 * A Basic implementation of the MetadataValue interface.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class SimpleMetadataValue implements MetadataValue
{
    /**
     * The Resource Identifier the MetadataValue belongs to.
     * 
     * @var string
     */
    private $resourceIdentifier;
    
    /**
     * The language of the MetadatValue.
     * 
     * @var string
     */
    private $language;
    
    /**
     * The Path of the MetadataValue.
     * 
     * @var array
     */
    private $path;
    
    /**
     * The intrinsic value of the MetadataValue.
     * 
     * @var string
     */
    private $value;
    
    /**
     * Create a new SimpleMetadataValue object.
     * 
     * @param string $resourceIdentifier The Identifier of the resource the MetadataValue describes.
     * @param string $path The descriptive path of the metadata.
     * @param string $value The intrinsic value of the MetadataValue.
     * @param string $language A string. If no specific language, an empty string is accepted.
     * @throws InvalidArgumentException If one of the argument contains an invalid value.
     */
    public function __construct($resourceIdentifier, $path, $value, $language = '')
    {
        $this->setResourceIdentifier($resourceIdentifier);
        $this->setPath($path);
        $this->setValue($value);
        $this->setLanguage($language);
    }
    
    /**
     * Set the identifier of the resource the MetadataValue describes.
     * 
     * @param string $resourceIdentifier An identifier.
     * @throws InvalidArgumentException If $resourceIdentifier is not a string or an empty string.
     */
    public function setResourceIdentifier($resourceIdentifier)
    {
        if (is_string($resourceIdentifier) === false) {
            $msg = "The resourceIdentifier argument must be a string.";
            throw new InvalidArgumentException($msg);
        } elseif ($resourceIdentifier === '') {
            $msg = "The resourceIdentifier argument must be a non-empty string.";
            throw new InvalidArgumentException($msg);
        } else {
            $this->resourceIdentifier = $resourceIdentifier;
        }
    }
    
    /**
     * @see \oat\taoQtiItem\model\qti\metadata\MetadataValue::getResourceIdentifier()
     */
    public function getResourceIdentifier()
    {
        return $this->resourceIdentifier;
    }
    
    /**
     * Set the descriptive path of the MetadataValue.
     * 
     * @param array $path An array of Path Components.
     * @throws InvalidArgumentException If $path is an empty array.
     */
    public function setPath(array $path)
    {
        if (count($path) === 0) {
            $msg = "The path argument must be a non-empty array.";
            throw new InvalidArgumentException($msg);
        } else {
            $this->path = $path;
        }
    }
    
    /**
     * @see \oat\taoQtiItem\model\qti\metadata\MetadataValue::getPath()
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * Set the intrinsic value of the MetadataValue.
     * 
     * @param string $value An intrinsic value.
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    /**
     * @see \oat\taoQtiItem\model\qti\metadata\MetadataValue::getValue()
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Set the language of the MetadataValue. If the intrinsic value of 
     * the MetadataValue has no specific language, $language is an empty string.
     * 
     * @param string $language A language or an empty string.
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }
    
    /**
     * @see \oat\taoQtiItem\model\qti\metadata\MetadataValue::getLanguage()
     */
    public function getLanguage()
    {
        return $this->language;
    }
}