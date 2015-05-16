<?php
namespace Z\IdeStubGenerator;

class Generator
{

    /**
     * Strategy for stub generation
     *
     * @var \Z\IdeStubGenerator\Strategy
     */
    private $strategy = null;

    /**
     * Class names for generation
     * Structure:
     * Array(
     *  class_name,
     *  ...
     * )
     * @var array
     */
    private $classes = array();

    /**
     * Function names for generation
     * Array(
     *  function_name,
     *  ...
     * )
     * @var array
     */
    private $functions = array();

    /**
     * Constants for generation
     * Array(
     *  constant_name => constant_value,
     *  ...
     * )
     * @var array
     */
    private $constants = array();

    /**
     * Constructor
     * @param Strategy $stubgenerator_strategy
     */
    public function __construct(Strategy $stubgenerator_strategy)
    {
        $this->strategy = $stubgenerator_strategy;
    }

    /**
     * Add classes to class list
     *
     * Structure of first parameter:
     * Array(
     *  class_name,
     *  ...
     * )
     *
     * @param array $classes
     * @return \Z\IdeStubGenerator\Generator
     */
    public function addClasses(array $classes)
    {
        foreach ($classes as $class_name) {
            $this->classes[$class_name] = $class_name;
        }
        return $this;
    }

    /**
     * Clear the class list
     * @return \Z\IdeStubGenerator\Generator
     */
    public function clearClasses()
    {
        $this->classes = array();
        return $this;
    }

    /**
     * Add functions to function list
     *
     * Structure of first parameter:
     * Array(
     *  function_name,
     *  ...
     * )
     *
     * @param array $functions
     * @return \Z\IdeStubGenerator\Generator
     */
    public function addFunctions(array $functions)
    {
        foreach ($functions as $function_name) {
            $this->functions[$function_name] = $function_name;
        }
        return $this;
    }

    /**
     * Clear the function list
     * @return \Z\IdeStubGenerator\Generator
     */
    public function clearFunctions()
    {
        $this->functions = array();
        return $this;
    }

    /**
     * Add constants to constant list
     *
     * Structure of first parameter:
     * Array(
     *  constant_name => constant_value,
     *  ...
     * )
     *
     * @param array $constants
     * @return \Z\IdeStubGenerator\Generator
     */
    public function addConstants(array $constants)
    {
        foreach ($constants as $constant_name => $constant_value) {
            $this->constants[$constant_name] = $constant_value;
        }
        return $this;
    }

    /**
     * Clear the constant list
     * @return \Z\IdeStubGenerator\Generator
     */
    public function clearConstants()
    {
        $this->constants = array();
        return $this;
    }

    /**
     * Starting generation
     * @throws \Exception
     */
    public function generate()
    {
        $this->strategy->generate(
            $this->classes,
            $this->functions,
            $this->constants
        );
    }
}
