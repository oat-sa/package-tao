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
?>
<?php
require_once dirname(__FILE__) . '/../includes/raw_start.php';

//output regarding the context
function out($msg = ''){
	print $msg;
	print (PHP_SAPI == 'cli') ? "\n" : "<br />";
}
out();
out("Running ".basename(__FILE__));

$exportDir = '';
$nameMode  = 'short';
if(PHP_SAPI == 'cli'){	//from command line
	
	if($_SERVER['argc'] < 1){
		echo "\nUsage : php {$_SERVER['argv'][0]} /dir/to/export short|long\n";
		echo "Example: php  {$_SERVER['argv'][0]} /tmp/export long\n";
	}

	if(isset($_SERVER['argv'][1])){
		$exportDir = $_SERVER['argv'][1]; 
	}
	if(isset($_SERVER['argv'][2])){
                 ($_SERVER['argv'][2] == 'long') ? $nameMode = 'long' :  $nameMode = 'short'; 
        }
}
else{					//from a browser
	
	if(isset($_GET['exportDir'])){
		$exportDir = $_GET['exportDir']; 
	}
	if(isset($_GET['nameMode'])){
                 ($_GET['nameMode'] == 'long') ? $nameMode = 'long' :  $nameMode = 'short';
        }
}
if(!is_dir($exportDir)){
	out("$exportDir is not a directory");
	exit;
}

$api = core_kernel_impl_ApiModelOO::singleton();

$nsManager = common_ext_NamespaceManager::singleton();
$namespaces = $nsManager->getAllNamespaces();

//$namespaces = array(LOCAL_NAMESPACE);

foreach($namespaces as $namespace){
	out("Exporting $namespace");
	$rdfData = core_kernel_api_ModelExporter::exportModelByUri($namespace);
	if(empty($rdfData)){
		out("Nothing exported!");
		continue;
	}
	if($nameMode == 'long'){
		$filename = str_replace('/', '_', str_replace('#', '', $namespace));
	}
	else{
		$filename = str_replace('#', '', strtolower(basename($namespace))); 
	}
	if(!preg_match("/\.rdf$/", $filename)){
		$filename .= '.rdf';
	}
	$path = tao_helpers_File::concat(array($exportDir, $filename));
	if(file_put_contents($path, $rdfData) != false){
		out("Namespace exported at $path");
	}
	else{
		out("Error during the file creation : $path");
	}
	out();
}
?>
