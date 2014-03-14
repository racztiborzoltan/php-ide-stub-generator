<?php
namespace Z\IdeStubGenerator\Strategy;

use Z\IdeStubGenerator\Strategy;

class PSR0 extends Strategy
{

    /**
     * Base directory for generated files
     * Default: WORKING_DIRECTORY/ide-stub/
     *
     * @var string
     */
    protected $basedir = null;

    public function setBaseDir($basedir)
    {
        $this->basedir = $basedir;
        if (! in_array($this->basedir[strlen($this->basedir) - 1], array(
            '\\',
            '/'
        )))
            $this->basedir .= self::DS;
    }

    /**
     * Get the base directory for generated files
     *
     * @return string
     */
    public function getBaseDir()
    {
        if (empty($this->basedir)) {
            $this->setBaseDir(getcwd() . self::DS . 'ide-stub' . self::DS);
        }

        return $this->basedir;
    }

    /*
     * (non-PHPdoc) @see \Z\IdeStubGenerator\StrategyInterface::generate()
     */
    public function generate(array $classes)
    {
        // Check the base directory:
        $this->getBaseDir();

        foreach ($classes as $class_name) {
            // Calculate the generated file path:
            $file_path = $this->basedir . '/' . str_replace(array(
                '\\',
                '_'
            ), '/', $class_name) . '.php';

            $file_content = $this->getPHPBegin() . $this->getClassStubString($class_name);

            // Check directory of generated path:
            $dirname = dirname($file_path);
            if (! is_dir($dirname))
                mkdir($dirname, 0755, true);

            // Write the generated content to the file:
            file_put_contents($file_path, $file_content);
        }
    }
}