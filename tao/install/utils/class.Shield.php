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
 * install Shield to block future installation
 *
 * @package tao
 
 */
class tao_install_utils_Shield{
	
	protected $extensions = array();
	protected $accessFiles = array();
	
	public function __construct(array $extensions) {
		$this->extensions = $extensions;
		foreach($this->extensions as $extension){
			$file = ROOT_PATH . $extension . '/.htaccess';
			if(file_exists($file)){
				if(!is_readable($file)){
					throw new tao_install_utils_Exception("Unable to read .htaccess file of extension '" . $extension . " while Production Mode activation'.");
				}
				$this->accessFiles[] = $file;
			}
		}
	}
	
	public function disableRewritePattern(array $patterns){

		$globalPattern = '';
		$size = count($patterns) - 1;
		foreach($patterns as $i => $pattern){
			$globalPattern .= preg_quote($pattern, '/');
			if($i < $size){
				$globalPattern .= '|';
			}
		}
		if(!empty($globalPattern)){
		
			foreach($this->accessFiles as $file){
				$lines = explode("\n", file_get_contents($file));
				$updated = 0;
				foreach($lines as $i => $line){
					if(preg_match("/".$globalPattern."/", $line)){
						$lines[$i] = '#'.$line;
						$updated++;
					}
				}
				if($updated > 0){
					if(!is_writable($file)){
						throw new tao_install_utils_Exception("Unable to write .htaccess file : ${file}.");
					}
					file_put_contents($file, implode("\n", $lines));
				}
			}
		}
	}
	
	public function protectInstall(){
		foreach($this->extensions as $extension){
			$installDir = ROOT_PATH . $extension . '/install/';
			if(file_exists($installDir) && is_dir($installDir)){
				if(!is_writable($installDir) || (file_exists($installDir . '.htaccess' && !is_writable($installDir . '.htaccess')))){
					throw new tao_install_utils_Exception("Unable to write .htaccess file into : ${installDir}.");
				}
				file_put_contents($installDir . '.htaccess', "Options +FollowSymLinks\n"
														   . "<IfModule mod_rewrite.c>\n"
														   . "RewriteEngine On\n"
														   . "RewriteCond %{REQUEST_URI} !/css/ [NC]\n"
														   . "RewriteCond %{REQUEST_URI} !/js/ [NC]\n"
														   . "RewriteCond %{REQUEST_URI} !/images/ [NC]\n"
														   . "RewriteCond %{REQUEST_URI} !/production.html [NC]\n"
														   . "RewriteRule ^.*$ " . ROOT_URL . "tao/install/production.html\n"
														   . "</IfModule>");
			}
		}
	}
        
        public function denyAccessTo($paths){
            
            
            foreach($this->extensions as $extension){
                foreach($paths as $path){
                    
                    if(!preg_match("/^\\".DIRECTORY_SEPARATOR."/", $path)){
                        $path = DIRECTORY_SEPARATOR . $path;
                    }
                    $denied = ROOT_PATH . $extension. $path;
                    if(file_exists($denied) && is_dir($denied)){
                        $accessFile = $denied .  DIRECTORY_SEPARATOR . '.htaccess';
                        if(!is_writable($denied) || (file_exists($accessFile && !is_writable($accessFile)))){
                                throw new tao_install_utils_Exception("Unable to write .htaccess file into : ${denied}.");
                        }
                        file_put_contents($accessFile, "<IfModule mod_rewrite.c>\n"
                                                    . "RewriteEngine On\n"
                                                    . "RewriteRule ^.*$ - [F]\n"
                                                    . "</IfModule>");
                    }
                }
            }
        }
	
}
?>