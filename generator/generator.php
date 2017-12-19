<?php
use libs\Generator;

spl_autoload_register(function ($class_name) {
    include __DIR__ . "/" . str_replace('\\', '/', $class_name) . ".class.php";
});

$generator = new Generator();
$generator->execute();
