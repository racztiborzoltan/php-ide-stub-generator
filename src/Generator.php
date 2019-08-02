<?php
namespace Z\IdeStubGenerator;

/**
 * Ide Stub Generator abstract class
 *
 * @author Rácz Tibor Zoltán
 *
 */
abstract class Generator
{
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
     * Variables for template
     * @var array
     */
    private $template_variables = array();

    /**
     * Return list of class names
     *
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Set class list
     *
     * Structure of first parameter:
     * Array(
     *  class_name,
     *  ...
     * )
     *
     * Clear the class list: ->setClasses(array());
     *
     * @param array $classes
     * @return \Z\IdeStubGenerator\Generator
     */
    public function setClasses(array $classes)
    {
        $this->classes = $classes;
    }

    /**
     * Return list of function names
     *
     * @return array
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * Set function list
     *
     * Structure of first parameter:
     * Array(
     *  function_name,
     *  ...
     * )
     *
     * Clear the function list: ->setFunctions(array());
     *
     * @param array $functions
     * @return \Z\IdeStubGenerator\Generator
     */
    public function setFunctions(array $functions)
    {
        $this->functions = $functions;
    }

    /**
     * Return list of constant names and values
     *
     * @return array
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * Set list of constants
     *
     * Structure of first parameter:
     * Array(
     *  constant_name => constant_value,
     *  ...
     * )
     *
     * Clear the function list: ->setConstants(array());
     *
     * @param array $constants
     * @return \Z\IdeStubGenerator\Generator
     */
    public function setConstants(array $constants)
    {
        $this->constants = $constants;
    }

    /**
     * Get variables of template
     *
     * @return array
     */
    protected function getTemplateVariables()
    {
        return $this->template_variables;
    }

    /**
     * Starting generation
     * @throws \Exception
     */
    public function generate()
    {
        $template_vars = array();

        $classes = $this->getClasses();
        $template_vars['classes'] = array();
        $class_infos = &$template_vars['classes'];
        foreach ($classes as $class_name) {
            $class_info = $this->_getClassDatas(new \ReflectionClass($class_name));
            if (empty($class_info)) {
                continue;
            }
            $class_infos[] = $class_info;
        }
        unset($class_infos, $class_name, $classes);

        $functions = $this->getFunctions();
        $template_vars['functions'] = array();
        $function_infos = &$template_vars['functions'];
        foreach ($functions as $function_name) {
            $function_info = $this->_getFunctionDatas(new \ReflectionFunction($function_name));
            if (empty($function_info)) {
                continue;
            }
            $function_infos[] = $function_info;
        }
        unset($function_infos, $function_name, $function_info);

        $constants = $this->getConstants();
        $template_vars['constants'] = array();
        $constant_infos = &$template_vars['constants'];
        foreach ($constants as $constant_name => $constant_value) {
            $constant_info = $this->_getConstantDatas($constant_name, $constant_value);
            if (empty($constant_info)) {
                continue;
            }
            $constant_infos[] = $constant_info;
        }
        unset($constant_infos, $function_name, $function_info);

        $this->template_variables = $template_vars;

        $this->_generate();
    }

    /**
     * Get informations about a class
     *
     * @param \ReflectionClass $class_reflection
     * @return array
     */
    private function _getClassDatas(\ReflectionClass $class_reflection)
    {
        $class_info = array();

        $reflection = &$class_reflection;

        $class_info['name'] = $reflection->getName();
        $class_info['short_name'] = $reflection->getShortName();
        $class_info['namespace'] = $reflection->getNamespaceName();

        $doccomment = $reflection->getDocComment();
        if (!empty($doccomment)) {
            $class_info['doccomment'] = $reflection->getDocComment();
        }

        $class_info['class_keyword'] = 'class';
        if ($reflection->isInterface()) {
            $class_info['class_keyword'] = 'interface';
        } elseif ($reflection->isTrait()) {
            $class_info['class_keyword'] = 'trait';
        }

        
        // Interfaces return true for isAbstract
        if (!$reflection->isInterface() && $reflection->isAbstract()) {
           $class_info['abstract'] = $reflection->isAbstract();
        }

        if ($reflection->isFinal()) {
            $class_info['final'] = $reflection->isFinal();
        }

        $extends = $reflection->getParentClass();
        if (!empty($extends) && $extends instanceof \ReflectionClass) {
            $class_info['extends'] = '\\' . $extends->getName();
        }

        $implements = $reflection->getInterfaceNames();
        if (!empty($implements) && is_array($implements)) {
            $class_info['implements'] = array();
            foreach ($implements as $implement) {
                $class_info['implements'][] = '\\' . $implement;
            }
            $class_info['implements_string'] = implode(', ', $class_info['implements']);
        }

        $constants = $reflection->getConstants() ;
        ksort($constants);
        if (!empty($constants)) {
            $class_info['constants2'] = array();
            foreach ($constants as $constant_name => $constant_value) {
                $class_info['constants2'][] = array(
                    'name' => $constant_name,
                    'value' => var_export($constant_value, true),
                );
            }
        }

        $properties = array();
        foreach ($reflection->getProperties() as $property) {
            $properties[$property->getName()] = $property;
        }
        ksort($properties);
        foreach ($properties as $property) {

            $property_info = array();
            $property_info['name'] = $property->getName();

            if ($property->isDefault() && $property->isStatic()) {
                $property->setAccessible(true);
                $property_info['value'] = var_export($property->getValue(), true);
            }

            $scope = array();

            if ($property->isPrivate()) {
                $property_info['private'] = $property->isPrivate();
                $scope[] = 'private';
            }
            elseif ($property->isProtected()) {
                $property_info['protected'] = $property->isProtected();
                $scope[] = 'protected';
            }
            elseif ($property->isPublic()) {
                $property_info['public'] = $property->isPublic();
                $scope[] = 'public';
            }

            if ($property->isStatic()) {
                $property_info['static'] = $property->isStatic();
                $scope[] = 'static';
            }

            $scope = implode(' ', $scope);
            if (empty($scope)) {
                $property_info['scope'] = 'public';
            } else {
                $property_info['scope'] = $scope;
            }

            $doccoment = $property->getDocComment();
            if ($doccoment !== false) {
                $property_info['doccomment'] = $property->getDocComment();
            }

            $class_info['properties'][] = $property_info;
        }

        $methods = array();
        foreach ($reflection->getMethods() as $method) {
            $methods[$method->getName()] = $method;
        }
        ksort($methods);
        foreach ($methods as $method) {
            $method_info = array();

            $method_info['interface'] = $class_info['class_keyword'] === 'interface';
            $method_info['endWithSemicolon'] = $method_info['interface'];
            $method_info['name'] = $method->getName();

            $doccomment = $method->getDocComment();
            if ($doccomment !== false) {
                $method_info['doccomment'] = $doccomment;
            }

            $scope = array();

            if ($method->isPrivate()) {
                $method_info['private'] = $method->isPrivate();
                $scope[] = 'private';
            }
            elseif ($method->isProtected()) {
                $method_info['protected'] = $method->isProtected();
                $scope[] = 'protected';
            }
            elseif ($method->isPublic()) {
                $method_info['public'] = $method->isPublic();
                $scope[] = 'public';
            }

            if ($method->isStatic()) {
                $method_info['static'] = $method->isStatic();
                $scope[] = 'static';
            }

            if (!$method_info['interface'] && $method->isAbstract()) {
                $method_info['abstractMethod'] = $method->isAbstract();
                $method_info['endWithSemicolon'] = $method->isAbstract();
            }

            $scope = implode(' ', $scope);
            if (!empty($scope)) {
                $method_info['scope'] = $scope;
            }

            if ($method->returnsReference()) {
                $method_info['returnsReference'] = true;
            }

            $method_info['parameters'] = $this->_getParametersData($method->getParameters());

            $class_info['methods'][] = $method_info;
        }

        return $class_info;
    }

    /**
     * Get informations about a function
     *
     * @param \ReflectionFunction $function_reflection
     * @return array
     */
    private function _getFunctionDatas(\ReflectionFunction $function_reflection)
    {
        $function_info = array();

        $reflection = &$function_reflection;

        $function_info['name'] = $reflection->getName();
        $function_info['short_name'] = $reflection->getShortName();
        $function_info['namespace'] = $reflection->getNamespaceName();

        if ($reflection->returnsReference()) {
            $function_info['returnsReference'] = true;
        }

        $doccomment = $reflection->getDocComment();
        if ($doccomment !== false) {
            $function_info['doccomment'] = $doccomment;
        }

        $function_info['parameters'] = $this->_getParametersData($reflection->getParameters());

        return $function_info;
    }

    /**
     * Get information about the parameters of a method or function
     *
     * @param array $parameters
     */
    private function _getParametersData(array $parameters)
    {
        $parameters_info = array();

        foreach ($parameters as $i => $parameter) {
            $parameter_info = $this->_getParameterDatas($parameter);
            if ($i < count($parameters)-1) {
                $parameter_info['comma'] = true;
            }
            $parameters_info[] = $parameter_info;
        }

        return $parameters_info;
    }

    /**
     * Get informations about the parameter of a method or function
     *
     * @param \ReflectionParameter $parameter_reflection
     * @return array
     */
    private function _getParameterDatas(\ReflectionParameter $parameter_reflection)
    {
        $parameter_info = array();

        $reflection = &$parameter_reflection;

        $parameter_info['name'] = $reflection->getName();

        if ($reflection->isArray()) {
            $parameter_info['typeHint'] = 'array';
        } else{

            try {
                $typeHint = $reflection->getClass();
                if ($typeHint) {
                    $parameter_info['typeHint'] = '\\' . $typeHint->getName();
                }
            } catch (\ReflectionException $e) {
                //
                // @todo relfectionexception event fire!
                //
                $parse = explode(' ', $e->getMessage());
                $parameter_info['typeHint'] = '\\' /* namespace separator */ . $parse[1];
            }

            if ($reflection->isDefaultValueAvailable()) {
                if ($reflection->isDefaultValueConstant()) {
                    $parameter_info['defaultValue'] = $reflection->getDefaultValueConstantName();
                } else {
                    $parameter_info['defaultValue'] = $reflection->getDefaultValue();
                }
                $parameter_info['defaultValue'] = preg_replace('#\n|\r\n|\r#', ' ', var_export($parameter_info['defaultValue'], true));
            }
        }

        return $parameter_info;
    }

    /**
     * Get information about a constant
     *
     * @param string $constant_name
     * @param mixed $constant_value
     * @return array
     */
    private function _getConstantDatas($constant_name, $constant_value)
    {
        $constant_info = array();

        $constant_name = var_export((string)$constant_name, true);

        switch (gettype($constant_value))
        {
            case 'boolean':
                $constant_value = (bool)$constant_value ? 'true' : 'false';
                break;
            case 'integer':
                $constant_value = (int)$constant_value;
                break;
            case 'double':
            case 'float':
                $constant_value = (float)$constant_value;
                break;
            case 'string':
                $constant_value = var_export((string)$constant_value, true);
                break;
            default:
                return array();
        }

        $constant_info['name'] = $constant_name;
        $constant_info['value'] = $constant_value;

        return $constant_info;
    }

    /**
     * Create and return an Mustache Engine instance
     *
     * @return \Mustache_Engine
     */
    protected function createMustacheEngine()
    {
        $indent_tab = function($str, $tab_count) {
            // Each line indent with x TAB:
            $lines = explode("\n", $str);
            foreach ($lines as $key => $line)
                $lines[$key] = str_repeat("\t", (int)$tab_count) . $line;
            $str = implode("\n", $lines);
            return $str;
        };

        $m = new \Mustache_Engine();
        $m->addHelper('indent', [
            'tab_1' => function($value) use ($indent_tab) {
                return $indent_tab($value, 1);
            },
            'tab_2' => function($value) use ($indent_tab) {
                return $indent_tab($value, 2);
            },
            'tab_3' => function($value) use ($indent_tab) {
                return $indent_tab($value, 3);
            },
        ]);
        return $m;
    }

    /**
     * Individual generation methodology
     */
    abstract protected function _generate();
}
