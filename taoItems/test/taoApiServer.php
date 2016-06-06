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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

switch($_GET['action']){

	case 'push':
		(isset($_POST['token'])) ? ($_POST['token'] == '7114e56cb3b9423314a425500afb41fc56183000') ? $saved = true : $saved = false : $saved = false;
		echo json_encode(array('saved' => $saved));
		break;
		
	case 'traceEvents':
		(isset($_POST['token'])) ? ($_POST['token'] == '7114e56cb3b9423314a425500afb41fc56183000') ? $saved = true : $saved = false : $saved = false;
		if($saved){
			if($saved = $saved && isset($_POST['events'])){
				$saved = $saved && count($_POST['events']) > 0;
			}
		}
		echo json_encode(array('saved' => $saved));
		break;
}
?>