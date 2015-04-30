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
 * Copyright (c) 20013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Represents an http response
 *
 * @access public
 * @author Patrick Plichart
 * @package generis
 
 */
class common_http_Response
{

    public $httpCode;
    public $headerOut; //CURLINFO_HEADER_OUT The request string sent. For this to work, add the CURLINFO_HEADER_OUT option to the handle by calling curl_setopt()
    public $effectiveUrl; // Last effective URL
    public $responseData;
}