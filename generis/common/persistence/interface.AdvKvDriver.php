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
 *
 * @author Patrick Plichart  <patrick@taotesting.com>
 * @license GPLv2
 * @package generis
 
 *
 */

/**
 * Interface of drivers that provide the advanced Key Value Persistence 
 * Some Key value provide hash possibilities 
 * 
 * @author Patrick Plichart <patrick@taotesting.com>
 */
interface common_persistence_AdvKvDriver extends common_persistence_KvDriver
{
    public function hmSet($key, $fields);
    public function hExists($key, $field);
    public function hSet($key, $field, $value);
    public function hGet($key, $field);
    public function hGetAll($key);
    public function keys($pattern);
    public function incr($key);
}