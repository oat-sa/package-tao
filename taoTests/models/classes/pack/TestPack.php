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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoTests\models\pack;

use \InvalidArgumentException;
use \JsonSerializable;

/**
 * The Item Pack represents the item package data produced by the compilation.
 *
 * @package taoTests
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class TestPack implements JsonSerializable
{
    /**
     * The item type
     * @var string
     */
    private $type;

    /**
     * The item data as arrays. Can be anything, just be careful of cyclic refs.
     * @var array
     */
    private $data = array();

    /**
     * The test item's data
     * @var array
     */
    private $items = array();

    /**
     * Creates an TestPack with the required data.
     *
     * @param string $type the test type
     * @param array $data the test data
     * @param array $items the test items
     * @throw InvalidArgumentException
     */
    public function __construct($type, $data, $items)
    {
        if(empty($type)){
            throw new InvalidArgumentException('Please provide a test type');
        }
        if(!is_array($data)){
            throw new InvalidArgumentException('Please provide the test data as an array');
        }
        if(!is_array($items)){
            throw new InvalidArgumentException('Please provide the items as an array');
        }
        $this->type = $type;
        $this->data = $data;
        $this->items = $items;
    }

    /**
     * Get the test type
     * @return string the type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the test data
     * @return array the data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the test's items
     * @return array the items
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * How to serialize the pack in JSON.
     */
    public function JsonSerialize()
    {
        return array(
            'type'      => $this->type,
            'data'      => $this->data,
            'items'     => $this->items
        );
    }
}
?>
