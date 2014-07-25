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

# Temp
class PluginException extends Exception{
}


/**
 * This class manages all operations related to plugins, whose the loading of the classes
 *
 * @author Eric Montecalvo <eric.montecalvo@tudor.lu> <eric.mtc@gmail.com>
 */
class Plugin {

	/**
	 * @var Array Folders skipped during the process
	 */
	static public $folderBlackList = array('.', '..', '.svn');

	/**
	 * @var Array The default classpath used to load the classes of plugins
	 */
	static public $defautlPluginClassPath = array('actions', 'models');

	/**
	 * @var String Name of the manifest file
	 */
	static public $manifestFileName = 'manifest.php';

	/**
	 * @var String The directory separator used to process all paths
	 */
	static public $directorySeparator = DIRECTORY_SEPARATOR;

	/**
	 * @var Bool Use to debug
	 */
	static public $debug = false;


	/**
	 * @return Array The installed-plugin list (The foldername)
	 */
	static public function getPluginList(){
		$folderList = array();

		# Test the root path of the plugins
		if(!file_exists(DIR_PLUGINS))
			throw new PluginException('Plugin root path  \'' . DIR_PLUGINS . '\' does not exist');

		# Parses the plugin fodler to discover all plugins
		if($root = opendir(DIR_PLUGINS))
	 		while( false !== ($file = readdir($root)))
				if(is_dir(DIR_PLUGINS . $file) && !in_array($file, Plugin::$folderBlackList))
					$folderList[] =  $file;

		return $folderList;
	}

	/**
	 * @return Array The manifest (php variable) containing all the information about the plugin
	 *
	 * @param String $pluginFolderName The plugin name
	 */
	static public function getManifest($pluginFolderName){
		# Get the plugin path
		$pluginFolderPath = Plugin::getAbsolutePath($pluginFolderName);

		# If the path of the plugin exists
		if(!file_exists($pluginFolderPath))
			throw new PluginException('Plugin folder \'' .  $pluginFolderPath . '\' does not exist');
		# If the manifest-file of the plugin exists
		else if(!file_exists($pluginFolderPath . Plugin::$manifestFileName)){
			throw new PluginException('Plugin manifest file \'' . $pluginFolderPath . Plugin::$manifestFileName . '\' not defined !');
		}else{
			# return the manifest (php variable)
			return require $pluginFolderPath . Plugin::$manifestFileName;
		}
	}

	/**
	 * @param String $pluginFolderName The plugin name
	 *
	 * @return String The absolute path of the plugin
	 */
	static public function getAbsolutePath($pluginFolderName){
		return DIR_PLUGINS . $pluginFolderName . '/';
	}

	/**
	 * @param String $pluginFolderName The plugin name
	 *
	 * @return Bool Returns true if the plugin is installed
	 */
	static public function exist($pluginFolderName){
		return file_exists(Plugin::getAbsolutePath($pluginFolderName));
	}

	/**
	 * This function can be used as the autoloader for the classes of all plugins
	 * Add spl_autoload_register("Plugin::pluginClassAutoLoad"); to the common.php framework file to use it
	 *
	 * @param String $className The name of the requested class
	 */
	static public function pluginClassAutoLoad($className){
		$pluginFolderList = Plugin::getPluginList();
		# Foreach folder of plugins
		foreach($pluginFolderList as $pluginFolder)
			# Foreach default classpath of plugins
			foreach(Plugin::$defautlPluginClassPath as $classFolder){
				# Build the potential path wherein the classes can be found
				$potentialClassPath = Plugin::getAbsolutePath($pluginFolder)
					. $classFolder
					. Plugin::$directorySeparator
					. $className
					. '.class.php';

				# Used to debug
				if(Plugin::$debug)echo '<br/>Plugin::pluginClassAutoLoad('.$className.') : ' . $potentialClassPath;

				# If a class is found
				if(file_exists($potentialClassPath)){
					# Used to debug
					if(Plugin::$debug) echo ' [OK]';

					# Includes the class
					require_once $potentialClassPath;
					return;
				}
			}
	}

	/**
	 * Includes the config file of the specified plugin
	 *
	 * @param String $pluginFolderName The plugin name
	 */
	static public function loadConfig($pluginFolderName){
		# Get the plugin path
		$pluginFolderPath = Plugin::getAbsolutePath($pluginFolderName);

		# If the plugin path exists
		if(!file_exists($pluginFolderPath))
			throw new PluginException('Plugin folder \'' .  $pluginFolderPath . '\' does not exist');
		# If the plugin config file exists
		else if(file_exists($pluginFolderPath . 'config.php'))
			# Include the config file
			require_once $pluginFolderPath . 'config.php';
	}

	/**
	 * This function is used to load all classes available in the specified plugin and the config file too,
	 * or, to load the specifics files or directory, presents into any folder within the plugin folder.
	 *
	 * Notice : Only one or other is processed
	 *
	 * @param String $pluginFolderName The plugin name
	 * @param String $specificClassFile Others specifics files or directory
	 */
	static public function load($pluginFolderName, $specificClassFile = null){
		$pluginFolderPath = Plugin::getAbsolutePath($pluginFolderName);

		# If the plugin exists
		if(!file_exists($pluginFolderPath))
			throw new PluginException('Plugin folder \'' .  $pluginFolderPath . '\' does not exist');
		else{
			if($specificClassFile !== null){
				# Include only the specifics specified-files
				Plugin::includeClassesFromFile($pluginFolderPath . $specificClassFile);
			}else{
				# Include the config file
				Plugin::loadConfig($pluginFolderName);

				# Foreach default classpath of plugins
				foreach(Plugin::$defautlPluginClassPath as $classFolder){
					$potentialClassPath = Plugin::getAbsolutePath($pluginFolderName)
						. $classFolder
						. Plugin::$directorySeparator;

					# If classes are avalaible, include them
					Plugin::includeClassesFromFile($potentialClassPath);
				}
			}
		}
	}

	/**
	 * Used to load classes of all plugins
	 *
	 * @param Array $pluginFolderBlackList The list of plugin ignored
	 */
	static public function loadAllPlugin($pluginFolderBlackList = array()){
		$pluginFolderList = Plugin::getPluginList();
		foreach($pluginFolderList as $pluginFolderName)
			if(!in_array($pluginFolderName, $pluginFolderBlackList))
				Plugin::load($pluginFolderName);

	}

	/**
	 * This specific function is just used to include a class according his path, or,
	 * a group of classes according a given folder.
	 *
	 * @param String $filePath The folder/file of the class(es)
	 */
	static protected function includeClassesFromFile($filePath){
		# If it is a folder
		if(is_dir($filePath)){
			# Just looks for the '/' char
			if($filePath[strlen($filePath)-1] != Plugin::$directorySeparator)
				$filePath .=  Plugin::$directorySeparator;

			# Load all classes of the folder
			if($root = opendir($filePath))
		 		while( false !== ($file = readdir($root)))
					if(!is_dir($file)
					&& !in_array($file, Plugin::$folderBlackList)
					&& ereg('\.class\.php$', $file) ){
						if(Plugin::$debug) echo '<br/>Plugin::getClassesFromFile :  ' . $filePath . $file . ' [OK]';
						require_once $filePath . $file;
					}
		# It it is just a single class, load it
		}else if(file_exists($filePath . '.class.php')){
			if(Plugin::$debug) echo '<br/>Plugin::getClassesFromFile :  ' . $filePath . '.class.php [OK]';
			require_once $filePath . '.class.php';
		}else
			throw new PluginException('Plugin::getClassesFromFile:  folder \'' .  $filePath . '\' does not exist');
	}
}

?>
