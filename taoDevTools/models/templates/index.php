<?php
{licenseBlock}
               
require_once dirname(__FILE__). '/../tao/includes/class.Bootstrap.php';

$bootStrap = new BootStrap('{id}');
$bootStrap->start();
$bootStrap->dispatch();