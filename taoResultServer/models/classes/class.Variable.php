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
 * Copyright (c) 2013 (original work) Open Assessment Technologies S.A.
 *
 * @author "Patrick Plichart, <patrick@taotesting.com>"
 * 
 * 
 * An Assessment Result is used to report the results of a candidate's interaction
 * with a test and/or one or more items attempted. Information about the test is optional,
 * in some systems it may be possible to interact with items that are not organized into a 
 * test at all. For example, items that are organized with learning resources and presented 
 * individually in a formative context.
 */
abstract class taoResultServer_models_classes_Variable 
//implements JsonSerializable 
    {

    /**
     * The purpose of an itemVariable is to report the value of the item variable with the given identifier.
     * @var string
     */
    public $identifier;
    /**
     * The cardinality of the variable, taken from the corresponding declaration or definition.
     * @var string {single, multiple, ordered, record}
     */
    public $cardinality;

    /**
     * The base type of the variable, taken from the corresponding declaration of definition.
     * This value is omitted only for variables with record cardinality.
     * 
     * @var baseType should move to an enumeration
     */
    public $baseType;

    public $epoch;
    /**
     * @author  "Patrick Plichart, <patrick@taotesting.com>"
     * @param unknown $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @author  "Patrick Plichart, <patrick@taotesting.com>"
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @author  "Patrick Plichart, <patrick@taotesting.com>"
     * @param string $cardinality
     * @throws common_exception_InvalidArgumentType
     */
    public function setCardinality($cardinality = "single")
    {
        if (! (in_array($cardinality, array(
            "single",
            "multiple",
            "ordered",
            "record"
        )))) {
            throw new common_exception_InvalidArgumentType("cardinality");
        }
        $this->cardinality = $cardinality;
    }

    /**
     * @author  "Patrick Plichart, <patrick@taotesting.com>"
     * @return string
     */
    public function getCardinality()
    {
        return $this->cardinality;
    }

    /**
     * @author  "Patrick Plichart, <patrick@taotesting.com>"
     * @param unknown $baseType
     */
    public function setBaseType($baseType)
    {
        $this->baseType = $baseType;
    }

    /**
     * @author  "Patrick Plichart, <patrick@taotesting.com>"
     * @return baseType
     */
    public function getBaseType()
    {
        return $this->baseType;
    }
    
    public function setEpoch($epoch)
    {
         $this->epoch = $epoch;
    }
    public function getEpoch()
    {
         return $this->epoch;
    }
    public function isSetEpoch(){
        return (isset($this->epoch));
    }
    
    public function toJson() {
        return json_encode((array)$this);
    }
}
?>