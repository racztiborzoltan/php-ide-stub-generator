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
     * Get stored strategy object
     * @return \Z\IdeStubGenerator\Strategy
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * Set strategy for generation
     * @param Strategy $strategy
     * @return \Z\IdeStubGenerator\Generator
     */
    public function setStrategy(Strategy $strategy)
    {
        $this->strategy = $strategy;
        return $this;
    }

    /**
     * Add class to class list
     * @param string $class_name
     * @return \Z\IdeStubGenerator\Generator
     */
    public function addClass($class_name)
    {
        $this->classes[$class_name] = $class_name;
        return $this;
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
        foreach ($classes as $class_name)
            $this->addClass($class_name);
        return $this;
    }

    /**
     * Adding a class to class list
     * @param string $class_name
     * @return \Z\IdeStubGenerator\Generator
     */
    public function removeClass($class_name)
    {
        unset($this->classes[$class_name]);
        return $this;
    }

    /**
     * Remove classes from class list
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
    public function removeClasses(array $classes)
    {
        foreach ($classes as $class_name)
            $this->removeClass($class_name);
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
     * Add function to function list
     * @param string $function_name
     * @return \Z\IdeStubGenerator\Generator
     */
    public function addFunction($function_name)
    {
        $this->functions[$function_name] = $function_name;
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
        foreach ($functions as $function_name)
            $this->addFunction($function_name);
        return $this;
    }

    /**
     * Remove function from function list
     * @param string $function_name
     * @return \Z\IdeStubGenerator\Generator
     */
    public function removeFunction($function_name)
    {
        unset($this->functions[$function_name]);
        return $this;
    }

    /**
     * Remove functions from function list
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
    public function removeFunctions(array $functions)
    {
        foreach ($functions as $function_name)
            $this->removeFunction($function_name);
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
     * Add constant to constant list
     * @param string $constant_name
     * @param bool|int|float|string $value
     * @return \Z\IdeStubGenerator\Generator
     */
    public function addConstant($constant_name, $value)
    {
        $this->constants[$constant_name] = $value;
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
        foreach ($constants as $constant_name => $constant_value)
            $this->addConstant($constant_name, $constant_value);
        return $this;
    }

    /**
     * Remove constant from constant list
     * @param string $constant_name
     * @return \Z\IdeStubGenerator\Generator
     */
    public function removeConstant($constant_name)
    {
        unset($this->constants[$constant_name]);
        return $this;
    }

    /**
     * Remove constants from constant list
     *
     * Structure of first parameter:
     * Array(
     *  constant_name,
     *  // OR:
     *  constant_name => constant_value,
     *  ...
     * )
     *
     * @param array $constants
     * @return \Z\IdeStubGenerator\Generator
     */
    public function removeConstants(array $constants)
    {
        foreach ($constants as $key => $constant_name)
        {
            if (is_string($key))
                $constant_name = $key;
            $this->removeConstant($constant_name);
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