<?php

// -----------------------------------------------
function getDefinedClasses()
{
    $temp = include 'vendor/composer/autoload_classmap.php';

    if (empty($temp))
        exit('Please, use the follow command: composer dump-autoload -o');

    $classes = array();
    foreach ($temp as $class_name => $class_file_path)
    {
        if (strpos($class_file_path, 'src/Z/')!==false)
            continue;

        if (!class_exists($class_name))
            require_once $class_file_path;

        $classes[$class_name] = $class_file_path;
    }

    return array_keys($classes);
}
// -----------------------------------------------

// load Composer autoloader:
require_once 'vendor/autoload.php';

// -----------------------------------------------
// Example 1:
//      Z\IdeStubGenerator\Strategy\PSR0
//
$stubgenerator_strategy = new Z\IdeStubGenerator\Strategy\PSR0();
$stubgenerator_strategy->setBaseDir(__DIR__.'/temp');

$generator = new Z\IdeStubGenerator\Generator($stubgenerator_strategy);
$generator->addClasses(getDefinedClasses());
$generator->generate();
// -----------------------------------------------

// -----------------------------------------------
// Example 2:
//      Z\IdeStubGenerator\Strategy\OneFile
//
$stubgenerator_strategy = new Z\IdeStubGenerator\Strategy\OneFile();
$stubgenerator_strategy->setFilePath(__DIR__.'/temp/example_stub.php');


$generator = new Z\IdeStubGenerator\Generator($stubgenerator_strategy);
$generator->addClasses(getDefinedClasses());
$generator->generate();
// -----------------------------------------------
