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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */
namespace oat\tao\model\search;

use core_kernel_classes_Class;
use oat\oatbox\PhpSerializable;

/**
 * Search interface
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
interface Search extends PhpSerializable 
{
    /**
     * Search for instances using a Lucene query
     *
     * @param string $queryString
     * @param core_kernel_classes_Class $rootClass
     * @param int $start
     * @param int $count
     *
     * @return ResultSet
     */
    public function query( $queryString, $rootClass = null, $start = 0, $count = 10 );

    /**
     * Index the resources given as a traversable
     * 
     * @param \Traversable $resourceTraversable
     */
    public function index(\Traversable $resourceTraversable);
}