<?php
// load Composer autoloader:
require_once '../vendor/autoload.php';


class Custom_PSR0 extends \Z\IdeStubGenerator\Strategy\PSR0
{

    protected function getMethodParameterName(\ReflectionParameter $parameter)
    {
        return 'par_'.$parameter->getPosition();
    }
}

class Custom_OneFile extends \Z\IdeStubGenerator\Strategy\OneFile
{

    protected function getMethodParameterName(\ReflectionParameter $parameter)
    {
        return 'par_'.$parameter->getPosition();
    }
}


header('Content-Type: text/plain');

$extension_name = 'phalcon';

$full_extension_name = $extension_name;
$extension_version = phpversion($extension_name);
if (!empty($extension_version)) {
    $full_extension_name .= '-'.$extension_version;
}

// -----------------------------------------------
// Example 1:
//      Z\IdeStubGenerator\Strategy\PSR0
//
$stubgenerator_strategy = new Custom_PSR0();
$stubgenerator_strategy->setBaseDir(__DIR__.'/../temp/'.$full_extension_name);
$stubgenerator_strategy->setFunctionsStubFileName('functions.stub.php');
$stubgenerator_strategy->setConstantsStubFileName('constants.stub.php');

$generator = new Z\IdeStubGenerator\Generator($stubgenerator_strategy);
$generator->addClasses(getDefinedClasses($extension_name));
$generator->addFunctions(getDefinedFunctions($extension_name));
$generator->addConstants(getDefinedConstants($extension_name));
$generator->generate();
// -----------------------------------------------

// -----------------------------------------------
// Example 2:
//      Z\IdeStubGenerator\Strategy\OneFile
//
$stubgenerator_strategy = new Custom_OneFile();
$stubgenerator_strategy->setFilePath(__DIR__.'/../temp/'.$full_extension_name.'_onefile_stub.php');


$generator = new Z\IdeStubGenerator\Generator($stubgenerator_strategy);
$generator->addClasses(getDefinedClasses($extension_name));
$generator->addFunctions(getDefinedFunctions($extension_name));
$generator->addConstants(getDefinedConstants($extension_name));
$generator->generate();
// -----------------------------------------------

echo PHP_EOL;echo PHP_EOL;
echo 'Done!!!';
exit();


// ============================================================================
//                      Functions for this example:
//
// -----------------------------------------------
function getDefinedClasses($extension_name)
{
    $classes = get_declared_classes();
    foreach ($classes as $key => $class_name)
    {
        $rc = new ReflectionClass($class_name);
        // Csak a mongóhoz tartozó definiált osztályokra lesz szükség:
        if ($rc->getExtensionName() !== $extension_name)
            unset($classes[$key]);
    }
    return $classes;
}
// -----------------------------------------------
function getDefinedFunctions($extension_name)
{
    $functions = get_defined_functions();
    $temp = array();
    foreach ($functions as $second_level)
        foreach ($second_level as $key => $function_name)
        {
            $rf = new ReflectionFunction($function_name);
            // Csak a mongóhoz tartozó definiált függvényekre lesz szükség:
            if ($rf->getExtensionName() === $extension_name)
                $temp[$key] = $function_name;
        }
    $functions = $temp;
    return $functions;
}
function getDefinedConstants($extension_name)
{
    $constants = get_defined_constants(true);
    $constants = isset($constants[$extension_name]) ? $constants[$extension_name] : array();
    return $constants;
}
// -----------------------------------------------
// ============================================================================
