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

namespace oat\oatbox;

/**
 * This interface means that the class can serialize instances
 * into executable code snippets
 *
 * @access public
 * @author joel bout <joel@taotesting.com>
 * @package generis
 
 */
interface PhpSerializable
{
    /**
     * this function should generate the php code to recreate
     * the current instance
     *
     * @access public
     * @author joel bout <joel@taotesting.com>
     * @return string
     */
    public function __toPhpCode();

}