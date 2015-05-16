<?php
namespace Z\IdeStubGenerator\Strategy;

use Z\IdeStubGenerator\Generator;

class OneFile extends Generator
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
    protected function _generate()
    {
        // Check the file path:
        $this->getFilePath();

        $file_content = $this->getPHPBegin();

        // ---------------------------------------
        // Process the constants:
        $constants = $this->getConstants();
        $file_content .= self::NL;
        foreach ($constants as $constant_name => $constant_value) {
            $file_content .= $this->getConstantStubString($constant_name, $constant_value);
        }
        $file_content .= self::NL;
        // ---------------------------------------

        // ---------------------------------------
        // Process the functions:
        //
        // Separate the functions based on namespaces:
        $functions = $this->getFunctions();
        $functions_by_namespace = array();
        foreach ($functions as $function_name) {
            $refl = new \ReflectionFunction($function_name);
            $namespace = $refl->getNamespaceName();
            $functions_by_namespace[$namespace][$function_name] = $function_name;
        }
        foreach ($functions_by_namespace as $namespace_name => $functions) {
            $temp_file_content = '';
            foreach ($functions as $function_name) {
                $temp_file_content .= $this->getFunctionStubString($function_name);
            }
            $file_content .= $this->getNamespaceBlock($namespace_name, $temp_file_content) . self::NL . self::NL;
        }
        // ---------------------------------------

        // ---------------------------------------
        // Process the classes:
        //
        $classes = $this->getClasses();
        foreach ($classes as $class_name) {
            $file_content .= $this->getNamespaceBlock($this->getNamespaceOfClassName($class_name), $this->getClassStubString($class_name));
        }
        // ---------------------------------------

        // Check directory of generated path:
        $dirname = dirname($this->file_path);
        if (! is_dir($dirname))
            mkdir($dirname, 0755, true);

        // Write the generated content to the file:
        file_put_contents($this->file_path, $file_content);
    }
}
