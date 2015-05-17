<?php
// ============================================================================
// Functions for this example:
//
// -----------------------------------------------
function getDefinedClasses()
{
    $temp = include '../vendor/composer/autoload_classmap.php';

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
require_once '../vendor/autoload.php';

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
$generator = new Z\IdeStubGenerator\Strategy\PSR0();
$generator->setBaseDir(__DIR__.'/../temp/'.pathinfo(__FILE__, PATHINFO_FILENAME).'/');
$generator->setFunctionsStubFileName('functions.stub.php');
$generator->setConstantsStubFileName('constants.stub.php');

$generator->setClasses(getDefinedClasses());
$generator->setFunctions(getDefinedFunctions());
$generator->setConstants(getDefinedConstants());
$generator->generate();
// -----------------------------------------------

// -----------------------------------------------
// Example 2:
//      Z\IdeStubGenerator\Strategy\OneFile
//
$generator = new Z\IdeStubGenerator\Strategy\OneFile();
$generator->setFilePath(__DIR__.'/../temp/'.pathinfo(__FILE__, PATHINFO_FILENAME).'/example_onefile_stub.php');

$generator->setClasses(getDefinedClasses());
$generator->setFunctions(getDefinedFunctions());
$generator->setConstants(getDefinedConstants());
$generator->generate();
// -----------------------------------------------

echo PHP_EOL;echo PHP_EOL;
echo 'End of example!!!';
