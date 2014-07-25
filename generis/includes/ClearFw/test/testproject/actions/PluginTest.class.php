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

class PluginTest extends Module{

    function index() {
    	echo '<h1>Example Plugins Index :</h1>';
    	# Without the plugin class autoloader
    	spl_autoload_unregister('Plugin::pluginClassAutoLoad'); # Desactivation (Activation comes from common.php)
    	self::loadPlugin();
    	self::loadAllPlugins();

    	# Using the plugin class autoloader
    	spl_autoload_register('Plugin::pluginClassAutoLoad');
    	self::autoLoadActionClass();
    	self::autoLoadModelClass();
    }

    static function autoLoadActionClass(){
    	echo '<h2>autoLoadActionClass</h2>';
		ActionMinimalPlugin::index();
    }

    static function autoLoadModelClass(){
    	echo '<h2>autoLoadModelClass</h2>';
		echo ModelMinimalPlugin::getStatus();
    }

    static function loadPlugin(){
       	echo '<h2>loadPlugin</h2>';
	 	# The plugin is not loaded
		echo '<h3>test 1</h3>';
		if( class_exists('ActionMinimalPlugin') && class_exists('ModelMinimalPlugin'))
			echo 'fail ';
		else echo 'ok ';

	 	# Load all classes now, without the plugin autoloader
	 	Plugin::load('minimal');
		echo '<h3>test 2</h3>';
		if( class_exists('ActionMinimalPlugin') && class_exists('ModelMinimalPlugin'))
			echo 'ok';
		else echo 'fail';

		echo '<h3>test 3 - List of plugins</h3>';
		var_dump(Plugin::getPluginList());

		echo '<h3>test 4 - Manifest of the minimal plugin</h3>';
		var_dump(Plugin::getManifest('minimal'));
    }

    static function loadAllPlugins(){
    	echo '<h2>loadAllPlugins</h2>';
    	Plugin::loadAllPlugin();
    	if( class_exists('ActionMinimalPlugin') && class_exists('ModelMinimalPlugin'))
			echo 'ok';
		else echo 'fail';
    }

}
?>