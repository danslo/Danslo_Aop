<?php

// Re-register the Varien autoloader.
spl_autoload_register(array(\Varien_Autoload::instance(), 'autoload'));

// Pull in composer autoloader.
require_once $_baseDir . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

// Now register ours.
$observer = Mage::getSingleton('aop/observer');
$observer->registerAutoloader();