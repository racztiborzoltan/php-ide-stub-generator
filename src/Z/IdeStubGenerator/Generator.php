<?php
namespace Z\IdeStubGenerator;

class Generator
{

    public $strategy = null;

    public $classes = array();

    public function __construct(Strategy $stubgenerator_strategy)
    {
        $this->strategy = $stubgenerator_strategy;
    }

    public function addClasses(array $classes)
    {
        $this->classes = array_merge($this->classes, $classes);
        return $this;
    }

    public function clearClasses()
    {
        $this->classes = array();
        return $this;
    }

    public function generate()
    {
        if (empty($this->classes))
            throw new \Exception(__METHOD__ . ' - empty class list! Use ->addClasses() method!');
        $this->strategy->generate($this->classes);
    }
}