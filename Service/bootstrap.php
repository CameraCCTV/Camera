<?php
require_once("vendor/autoload.php");

$environment = array_merge($_ENV, $_SERVER);
ksort($environment);