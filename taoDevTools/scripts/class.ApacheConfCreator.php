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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

class taoDevTools_scripts_ApacheConfCreator extends tao_scripts_Runner
{
    
    /**
     * (non-PHPdoc)
     * @see tao_scripts_Runner::preRun()
     */
    public function preRun(){

        if(!isset($this->parameters['target']) ){
            $this->parameters['target'] = $this->parameters['t']; 
        }

        if(!isset($this->parameters['documentRoot']) ){
            $this->parameters['documentRoot'] = $this->parameters['d'];
        }
        if(!isset($this->parameters['serverName']) ){
            $this->parameters['serverName'] = $this->parameters['s'];
        }
        if(!isset($this->parameters['tpl']) ){
            $this->parameters['tpl'] = dirname(__FILE__). '/sample/sample.conf';
        }
        
        $fileinfo =pathinfo($this->parameters['target']);
        
        if($fileinfo['extension'] != 'conf'){
            $this->err('Extension of target file has to be conf', true);
        }
    }
    /**
     * (non-PHPdoc)
     * @see tao_scripts_Runner::run()
     */
    public function run()
    {
 
        
        $sample = file_get_contents($this->parameters['tpl']);
        $target = $this->parameters['target'];
        
        $serverName = $this->parameters['serverName'];
        $documentRoot = $this->parameters['documentRoot'];
        $directory = array();
        
        $it = new RecursiveDirectoryIterator($documentRoot);

        foreach (new RecursiveIteratorIterator($it) as $file) {
            if($file->getFileName() == '.htaccess' 
                && strpos($file->getPath(), 'install/checks/testRewrite') ===false){
              
          
               $pathName = $file->getPathname();
               $path = $file->getPath();
               
               $directory[$path] = "\t<Directory ". $path . ">\n";
               $htaccess = file_get_contents($pathName);
               $directory[$path] .= $htaccess;
               $directory[$path] .= "\n\t</Directory>\n";
               
            }
        }
        
        $directoryContent = implode("\n",array_values($directory));
    
        $maps = array(
        	'{ServerName}' => $serverName,
            '{DocumentRoot}' => $documentRoot,
            '{Directory}' => $directoryContent
        );
        
        $finalContent = str_replace(array_keys($maps), array_values($maps), $sample);
        file_put_contents($target, $finalContent);
    }
}

?>