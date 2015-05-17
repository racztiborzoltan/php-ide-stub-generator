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

    public function setFilePath($file_path)
    {
        if (empty($file_path)) {
            throw new \InvalidArgumentException('Path of file is empty!');
        }

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
            throw new \UnderflowException('Value of base directory is empty!');
        }

        return $this->file_path;
    }

    /**
     * (non-PHPdoc)
     * @see \Z\IdeStubGenerator\Generator::_generate()
     */
    protected function _generate()
    {
        $file_path = $this->getFilePath();

        // Check directory of generated path:
        $dirname = dirname($file_path);
        if (! is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }

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
        // ---------------------------------------

        // ---------------------------------------
        // Process the classes:
        //
        // Separate the classes, based on namespaces:
        $classes_by_namespace = array();
        foreach ($template_variables['classes'] as $class_info) {
            $namespace = $class_info['namespace'];
            $classes_by_namespace[$namespace][] = $class_info;
        }
        foreach (array_keys($classes_by_namespace) as $namespace) {
            $template_variables['classes_by_namespace']['namespaces'][] = array(
                'name' => $namespace,
                'classes' => $classes_by_namespace[$namespace],
            );
        }
        // ---------------------------------------

        $m = $this->createMustacheEngine();
        $template_path = realpath(__DIR__.'/../../templates/onefile/onefile.mustache');
        if (empty($template_path)) {
            throw new \UnexpectedValueException('Template path is not exists!');
        }
        $template_content = file_get_contents($template_path);
        $rendered = $m->render($template_content, $template_variables);

        file_put_contents($file_path, $rendered);
    }
}
