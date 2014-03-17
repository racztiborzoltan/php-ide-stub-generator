<?php
// ============================================================================
// Functions for this example:
//
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
function getDefinedFunctions()
{
    $functions = get_defined_functions();
    $functions = $functions['user'];
    foreach ($functions as $key => $function_name)
    {
        $refl = new ReflectionFunction($function_name);
        $function_name = $refl->getName();

        if (strpos(strtolower($function_name), 'composer')!==false
            || $refl->getFileName() === __FILE__
            )
            unset($functions[$key]);
        else
            $functions[$key] = $function_name;
    }

    return $functions;
}
// -----------------------------------------------
function getDefinedConstants()
{
    $constants = get_defined_constants(true);
    $constants = $constants['user'];
    return $constants;
}
// -----------------------------------------------
// ============================================================================

header('Content-Type: text/plain');

// load Composer autoloader:
require_once 'vendor/autoload.php';

define('TEST_INT', 100);
define('TEST_BOOL', true);
define('TEST_BOOL_2', false);
define('TEST_DOUBLE', 10.234);
define('TEST_STRING', 'ASDFGHdfgh');
define('TEST_NULL', null);

// -----------------------------------------------
// Example 1:
//      Z\IdeStubGenerator\Strategy\PSR0
//
$stubgenerator_strategy = new Z\IdeStubGenerator\Strategy\PSR0();
$stubgenerator_strategy->setBaseDir(__DIR__.'/temp');
$stubgenerator_strategy->setFunctionsStubFileName('functions.stub.php');
$stubgenerator_strategy->setConstantsStubFileName('constants.stub.php');

$generator = new Z\IdeStubGenerator\Generator($stubgenerator_strategy);
$generator->addClasses(getDefinedClasses());
$generator->addFunctions(getDefinedFunctions());
$generator->addConstants(getDefinedConstants());
$generator->generate();
// -----------------------------------------------

// -----------------------------------------------
// Example 2:
//      Z\IdeStubGenerator\Strategy\OneFile
//
$stubgenerator_strategy = new Z\IdeStubGenerator\Strategy\OneFile();
$stubgenerator_strategy->setFilePath(__DIR__.'/temp/example_onefile_stub.php');


$generator = new Z\IdeStubGenerator\Generator($stubgenerator_strategy);
$generator->addClasses(getDefinedClasses());
$generator->addFunctions(getDefinedFunctions());
$generator->addConstants(getDefinedConstants());
$generator->generate();
// -----------------------------------------------

echo PHP_EOL;echo PHP_EOL;
echo 'End of example!!!';