<?php


function autoload($class_name)
{
	$class_name=str_replace('\\', '/', $class_name); 
	include_once '/home/cclosh/SVN/cc/test/'.$class_name.'.php'; 
}

spl_autoload_register('autoload', true, true);


include_once 'b.php';

