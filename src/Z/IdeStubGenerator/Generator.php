<?php
namespace Z\IdeStubGenerator;

class Generator
{

    private $strategy = null;

    private $classes = array();

    private $functions = array();

    private $constants = array();

    public function __construct(Strategy $stubgenerator_strategy)
    {
        $this->strategy = $stubgenerator_strategy;
    }

    public function getStrategy()
    {
        return $this->strategy;
    }

    public function setStrategy(Strategy $strategy)
    {
        $this->strategy = $strategy;
        return $this;
    }

    public function addClass($class_name)
    {
        $this->classes[$class_name] = $class_name;
        return $this;
    }

    public function addClasses(array $classes)
    {
        foreach ($classes as $class_name)
            $this->addClass($class_name);
        return $this;
    }

    public function removeClass($class_name)
    {
        unset($this->classes[$class_name]);
        return $this;
    }

    public function removeClasses(array $classes)
    {
        foreach ($classes as $class_name)
            $this->removeClass($class_name);
        return $this;
    }

    public function clearClasses()
    {
        $this->classes = array();
        return $this;
    }

    public function addFunction($function_name)
    {
        $this->functions[$function_name] = $function_name;
        return $this;
    }

    public function addFunctions(array $functions)
    {
        foreach ($functions as $function_name)
            $this->addFunction($function_name);
        return $this;
    }

    public function removeFunction($function_name)
    {
        unset($this->functions[$function_name]);
        return $this;
    }

    public function removeFunctions(array $functions)
    {
        foreach ($functions as $function_name)
            $this->removeFunction($function_name);
        return $this;
    }

    public function clearFunctions()
    {
        $this->functions = array();
        return $this;
    }

    public function addConstant($constant_name, $value)
    {
        $this->constants[$constant_name] = $value;
        return $this;
    }

    public function addConstants(array $constants)
    {
        foreach ($constants as $constant_name => $constant_value)
            $this->addConstant($constant_name, $constant_value);
        return $this;
    }

    public function removeConstant($constant_name)
    {
        unset($this->constants[$constant_name]);
        return $this;
    }

    public function removeConstants(array $constants)
    {
        foreach ($constants as $constant_name)
            $this->removeConstant($constant_name);
        return $this;
    }

    public function clearConstants()
    {
        $this->constants = array();
        return $this;
    }

    public function generate()
    {
        if (empty($this->classes))
            throw new \Exception(__METHOD__ . ' - empty class list! Use ->addClasses() method!');
        $this->strategy->generate(
            $this->classes,
            $this->functions,
            $this->constants
        );
    }
}