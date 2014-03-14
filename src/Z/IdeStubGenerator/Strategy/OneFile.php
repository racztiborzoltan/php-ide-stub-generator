<?php
namespace Z\IdeStubGenerator\Strategy;

use Z\IdeStubGenerator\Strategy;

class OneFile extends Strategy
{

    /**
     * File path for generated file
     * Default: WORKING_DIRECTORY/ide-stub/stub.php
     *
     * @var string
     */
    protected $file_path = null;

    /**
     * Base directory for generated files
     * Default: WORKING_DIRECTORY/ide-stub/
     *
     * @var string
     */
    protected $basedir = null;

    public function setFilePath($file_path)
    {
        $this->file_path = $file_path;
    }

    /**
     * Get the file path for generated file
     *
     * @return string
     */
    public function getFilePath()
    {
        if (empty($this->file_path)) {
            $this->setFilePath(getcwd() . self::DS . 'ide-stub' . self::DS . 'stub.php');
        }

        return $this->file_path;
    }

    /*
     * (non-PHPdoc) @see \Z\IdeStubGenerator\StrategyInterface::generate()
     */
    public function generate(array $classes)
    {
        // Check the file path:
        $this->getFilePath();

        $file_content = $this->getPHPBegin();

        foreach ($classes as $class_name) {
            $file_content .= $this->getClassStubString($class_name);
        }

        // Write the generated content to the file:
        file_put_contents($this->file_path, $file_content);
    }
}