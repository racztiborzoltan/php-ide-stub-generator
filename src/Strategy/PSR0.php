<?php
namespace Z\IdeStubGenerator\Strategy;

use Z\IdeStubGenerator\Generator;

class PSR0 extends Generator
{

    /**
     * Base directory for generated files
     * Default: WORKING_DIRECTORY/ide-stub/
     *
     * @var string
     */
    private $basedir = null;

    /**
     * The file name that will contain the functions.
     * Must be relative to the base directory!!! (->basedir)
     *
     * @var string
     */
    private $functions_file_name = 'functions.php';

    /**
     * The file name will contain the constants
     * Must be relative to the base directory (->basedir)
     *
     * @var string
     */
    private $constants_file_name = 'constants.php';

    public function setBaseDir($basedir)
    {
        $basedir = str_replace('\\', '/', $basedir);
        if ($basedir[strlen($basedir)-1] !== '/') {
            throw new \InvalidArgumentException('Trailing slash is important!');
        }

        $this->basedir = $basedir;
    }

    /**
     * Get the base directory for generated files
     *
     * @return string
     */
    public function getBaseDir()
    {
        if (empty($this->basedir)) {
            throw new \UnderflowException('Value of base directory is empty!');
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

    /**
     * (non-PHPdoc)
     * @see \Z\IdeStubGenerator\Generator::_generate()
     */
    protected function _generate()
    {
        $basedir = $this->getBaseDir();
        if (!is_dir($basedir)) {
            mkdir($basedir, 0755, true);
        }

        $this->_generateClasses();
        $this->_generateFunctions();
        $this->_generateConstants();
    }

    private function _generateClasses()
    {
        $template_variables = $this->getTemplateVariables();
        // ---------------------------------------
        // Process the classes:
        //
        foreach ($template_variables['classes'] as $class_info) {
            $class_name = $class_info['name'];

            $class_path = str_replace(array(
                '\\',
                '_'
            ), '/', $class_name);

            // Calculate the generated file path:
            $file_path = $this->getBaseDir() . '/' . $class_path . '.php';
            $file_path = str_replace('//', '/', $file_path);

            // Check directory of generated path:
            $dirname = dirname($file_path);
            if (! is_dir($dirname)) {
                mkdir($dirname, 0755, true);
            }

            $template_variables['class'] = $class_info;

            // Generate file content:
            $m = $this->createMustacheEngine();
            $template_path = realpath(__DIR__.'/../../templates/psr0/classes.mustache');
            if (empty($template_path)) {
                throw new \UnexpectedValueException('Template path is not exists!');
            }
            $template_content = file_get_contents($template_path);
            $rendered = $m->render($template_content, $template_variables);

            // Write file content:
            file_put_contents($file_path, $rendered);

        }
        // ---------------------------------------
    }

    /**
     * Generate ide stubs for functions
     *
     * @throws \UnexpectedValueException
     */
    private function _generateFunctions()
    {
        $template_variables = $this->getTemplateVariables();
        // ---------------------------------------
        // Process the functions:
        //
        // Separate the functions, based on namespaces:
        $functions_by_namespace = array();
        foreach ($template_variables['functions'] as $function_info) {
            $namespace = $function_info['namespace'];
            $functions_by_namespace[$namespace][] = $function_info;
        }
        foreach (array_keys($functions_by_namespace) as $namespace) {
            $template_variables['functions_by_namespace']['namespaces'][] = array(
                'name' => $namespace,
                'functions' => $functions_by_namespace[$namespace],
            );
        }

        $file_path = $this->basedir . '/' . $this->getFunctionsStubFileName();
        $m = $this->createMustacheEngine();
        $template_path = realpath(__DIR__.'/../../templates/psr0/functions.mustache');
        if (empty($template_path)) {
            throw new \UnexpectedValueException('Template path is not exists!');
        }
        $template_content = file_get_contents($template_path);
        $rendered = $m->render($template_content, $template_variables);
        file_put_contents($file_path, $rendered);
        // ---------------------------------------
    }

    /**
     * Generate ide stubs for constants
     *
     * @throws \UnexpectedValueException
     */
    private function _generateConstants()
    {
        $template_variables = $this->getTemplateVariables();
        // ---------------------------------------
        // Process the constants:
        //
        $file_path = $this->basedir . '/' . $this->getConstantsStubFileName();
        $m = $this->createMustacheEngine();
        $template_path = realpath(__DIR__.'/../../templates/psr0/constants.mustache');
        if (empty($template_path)) {
            throw new \UnexpectedValueException('Template path is not exists!');
        }
        $template_content = file_get_contents($template_path);
        $rendered = $m->render($template_content, $template_variables);
        file_put_contents($file_path, $rendered);
        // ---------------------------------------
    }
}
