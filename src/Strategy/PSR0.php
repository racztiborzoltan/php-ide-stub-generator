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

    /**
     * The file name that will contain the functions.
     * Must be relative to the base directory!!! (->basedir)
     *
     * @var string
     */
    protected $functions_file_name = 'functions.php';

    /**
     * The file name will contain the constants
     * Must be relative to the base directory (->basedir)
     *
     * @var string
     */
    protected $constants_file_name = 'constants.php';

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

    /**
     * Set the file name that will contain the functions.
     * Must be relative to the base directory (->getBaseDir())
     *
     * @param string $file_name
     */
    public function setFunctionsStubFileName($file_name)
    {
        $this->functions_file_name = $file_name;
    }

    /**
     * Get the file name that will contain the functions.
     *
     * @return string
     */
    public function getFunctionsStubFileName()
    {
        return $this->functions_file_name;
    }

    /**
     * Set the file name that will contain the constants.
     * Must be relative to the base directory (->getBaseDir())
     *
     * @param string $file_name
     */
    public function setConstantsStubFileName($file_name)
    {
        $this->constants_file_name = $file_name;
    }

    /**
     * Get the file name that will contain the constants.
     *
     * @return string
     */
    public function getConstantsStubFileName()
    {
        return $this->constants_file_name;
    }

    /*
     * (non-PHPdoc) @see \Z\IdeStubGenerator\StrategyInterface::generate()
     */
    public function generate(array $classes, array $functions, array $constants)
    {
        // Check the base directory:
        $this->getBaseDir();

        // ---------------------------------------
        // Process the classes:
        //
        foreach ($classes as $class_name) {
            // Calculate the generated file path:
            $file_path = $this->basedir . '/' . str_replace(array(
                '\\',
                '_'
            ), '/', $class_name) . '.php';

            $file_content = $this->getPHPBegin() . $this->getNamespaceBlock($this->getNamespaceOfClassName($class_name), $this->getClassStubString($class_name));

            // Check directory of generated path:
            $dirname = dirname($file_path);
            if (! is_dir($dirname))
                mkdir($dirname, 0755, true);

                // Write the generated content to the file:
            file_put_contents($file_path, $file_content);
        }
        // ---------------------------------------

        // ---------------------------------------
        // Process the functions:
        //
        $file_path = $this->basedir . self::DS . $this->getFunctionsStubFileName();
        $file_content = $this->getPHPBegin();
        // Separate the functions based on namespaces:
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
        file_put_contents($file_path, $file_content);
        // ---------------------------------------

        // ---------------------------------------
        // Process the constants:
        $file_path = $this->basedir . self::DS . $this->getConstantsStubFileName();
        $file_content = $this->getPHPBegin();
        foreach ($constants as $constant_name => $constant_value) {
            $file_content .= $this->getConstantStubString($constant_name, $constant_value);
        }
        file_put_contents($file_path, $file_content);
        // ---------------------------------------
    }
}