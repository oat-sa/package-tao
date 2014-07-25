<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
require_once dirname(__FILE__). '/../tao/includes/class.Bootstrap.php';

$bootStrap = new BootStrap('wfAuthoring');
$bootStrap->start();
$bootStrap->dispatch();
?>