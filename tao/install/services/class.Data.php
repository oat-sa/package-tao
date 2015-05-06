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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Represents a bunch of Data consumed and/or returned by a tao_install_services_Service.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
class tao_install_services_Data{
    
    /**
     * The actual data. Services are expecting JSON encoded data.
     */
    private $content;
    
    /**
     * The mime type of the content.
     */
    private $mimeType;
    
    /**
     * The content encoding.
     */
    private $encoding;
    
    /**
     * Creates a new instance of tao_install_services_Data. Services expect the content to be
     * JSON encoded data.
     * @param string $content A JSON encoded content.
     * @param string $encoding The content encoding.
     * @param string $mimeType The content mime type.
     */
    public function __construct($content, $encoding = 'UTF-8', $mimeType = 'application/json'){
        $this->setContent($content);
        $this->setEncoding($encoding);
        $this->setMimeType($mimeType);
    }
    
    /**
     * Gets the actual content of the data (JSON encoded).
     * @return string JSON encoded value.
     */
    public function getContent(){
        return $this->content;
    }
    
    /**
     * Sets the actual content of the data (JSON encoded).
     * @param string $content Some JSON encoded content.
     * @return void
     */
    public function setContent($content){
        $this->content = $content;
    }
    
    /**
     * Sets the encoding of the Data content.
     * @return string A content encoding such as 'UTF-8'.
     */
    public function getEncoding(){
        return $this->encoding;
    }
    
    /**
     * Sets the encoding of the Data content.
     * @param string $encoding A content encoding such as 'UTF-8'.
     * @return void
     */
    protected function setEncoding($encoding){
        $this->encoding = $encoding;
    }
    
    /**
     * Gets the content mime type for the actual content of this Data.
     * @return string A content mime type such as 'application/json'.
     */
    public function getMimeType(){
        return $this->mimeType;
    }
    
    /**
     * Sets the content mime type for the actual content of this Data.
     * @param string $mimeType A content mime type such as 'text/html'.
     * @return void
     */
    public function setMimeType($mimeType){
        $this->mimeType = $mimeType;
    }
}
?>