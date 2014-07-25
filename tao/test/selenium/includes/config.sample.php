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
// Testing accounts.
// Backend testing account.
define('TAO_SELENIUM_BACKEND_LOGIN', 'admin');
define('TAO_SELENIUM_BACKEND_PASSWORD', 'admin');

// Root URL of the TAO instance to be tested.
define('TAO_SELENIUM_ROOT_URL', 'http://taotransfer');

// Speed of the client-side testing. The value of this
// constant is expressed in milliseconds. It corresponds to
// the time the client has to wait between each Selenese command
// execution.
define('TAO_SELENIUM_SPEED', 500);

// Selenium RC server host name.
define('TAO_SELENIUM_HOST', 'localhost');

// Selenium RC server port.
define('TAO_SELENIUM_PORT', 4444);

// Relative path from EXTENSION/Selenium to the RDF directory.
define('TAO_SELENIUM_RDF_DIR', '/rdf');
?>