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

/**
 * Get data from the request context.
 *
 * @param string $key A key to identify the data.
 * @return mixed The data bound to the key. If no data is bound to the provided key, null is return.
 */
function get_data($key)
{
	return RenderContext::getCurrentContext()->getData($key);
}

/**
 * Returns whenever or not a variable with the specified key is defined
 * 
 * @param string $key
 * @return boolean
 */
function has_data($key){
	return RenderContext::getCurrentContext()->hasData($key);
}
?>