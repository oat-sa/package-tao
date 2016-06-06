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
 * Copyright (c) 2006-2009 (original work) Public Research Centre Henri Tudor (under the project FP6-IST-PALETTE);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of "ClearFw".
# Copyright (c) 2007 CRP Henri Tudor and contributors.
# All rights reserved.
#
# "ClearFw" is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License version 2 as published by
# the Free Software Foundation.
# 
# "ClearFw" is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with "ClearFw"; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****

/**
 * The Camelizer class provides string Camelization services (to understand what
 * camelize means think at the camel). For instance, the camelized version
 * of 'iwantmore' is 'iWantMore'.
 *
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu>
 * @package util
 */
class Camelizer
{
	/**
	 * Puts together a multiplicity of string in a camelized version.
	 * e.g : using {'i', 'want', 'more'} as parameter, the camelize function
	 * will return 'iWantMore'.
	 *
	 * @param array $strings An array of string that must be put together in a camelized version.
	 */
	public static function camelize(array $strings, $firstToUpper = false)
	{
		$firstDone = false;
		$camelized = '';
		
		foreach ($strings as $string)
		{
			if (!$firstDone)
			{
				$camelized .= (!$firstToUpper) ? self::firstToLower($string) : self::firstToUpper($string);
				$firstDone = true;
			}
			else
			{
				$camelized .= self::firstToUpper($string);
			}
		}
		
		return $camelized;
	}
	
	/**
	 * Changes the first character of a string in its capital version.
	 * e.g : 'bilibu' becomes 'Bilibu'.
	 *
	 * @param string $string The string to modify.
	 * @return string The string with its first character in a capital version.
	 */
	public static function firstToUpper($string)
	{
		return strtoupper(substr($string, 0, 1)) . substr($string, 1);
	}
	
	/**
	 * Changes the first character of a string in its lower version.
	 * e.g : 'Bilibu' becomes 'bilibu'.
	 *
	 * @param string $string The string to modify.
	 * @return string The string with its first character in a capital version.
	 */
	public static function firstToLower($string)
	{
		return strtolower(substr($string, 0, 1)) . substr($string, 1);
	}
}
?>