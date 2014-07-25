<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
require_once dirname(__FILE__). '/../tao/includes/class.Bootstrap.php';

$options = array('constants' => array());
foreach (common_ext_ExtensionsManager::singleton()->getInstalledExtensions() as $ext) {
	$options['constants'][] = $ext->getID();
}

$bootStrap = new BootStrap('ontoBrowser', $options);
$bootStrap->start();
$bootStrap->dispatch();
?>
