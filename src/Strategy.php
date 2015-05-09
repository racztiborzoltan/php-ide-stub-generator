<?php
namespace Z\IdeStubGenerator;

abstract class Strategy
{

    /**
     * constant for directory separator character
     * @var string
     */
    const DS = DIRECTORY_SEPARATOR;

    /**
     * Namespace speratator character
     * @var string
     */
    const NS = '\\';

    /**
     * Constant for new line character
     * @var string
     */
    const NL = "\n";

    /**
     * Constant for TAB character
     * @var string
     */
    const TAB = "\t";

    /**
     * Individual generation methodology
     * @param array $classes
     * @param array $functions
     * @param array $constants
     */
    abstract public function generate(array $classes, array $functions, array $constants);

    /**
     * Return begin of PHP files
     * @return string
     */
    protected function getPHPBegin()
    {
        return '<?php' . self::NL;
    }

    /**
     * Return namespace of given class name
     * @param string $class_name
     * @return string
     */
    protected function getNamespaceOfClassName($class_name)
    {
        return substr($class_name, 0, strrpos($class_name, self::NS));
    }

    /**
     * Return generated stub string by class_name
     * @param string $class_name
     * @return string
     */
    protected function getClassStubString($class_name)
    {
        $php = '';
        $refl = new \ReflectionClass($class_name);

        $class_name = $refl->getName();
        $namespace = $this->getNamespaceOfClassName($class_name);
        $just_class_name = str_replace($namespace . self::NS, '', $class_name);
        $doccomment = $refl->getDocComment();
        $php .= self::NL . $doccomment . self::NL;
        $php .= 'class ' . $just_class_name;
        if ($parent = $refl->getParentClass()) {
            $php .= ' extends ' . self::NS . $parent->getName();
        }
        $php .= self::NL . '{' . self::NL;

        $constants = $refl->getConstants() ;
        ksort($constants);
        foreach ($constants as $constant_name => $constant_value) {
            $php .= self::TAB . 'const ' . $constant_name . ' = '.var_export($constant_value, true).';' . self::NL;
        }

        $properties = array();
        foreach ($refl->getProperties() as $property) {
            $properties[$property->getName()] = $property;
        }
        ksort($properties);
        foreach ($properties as $property) {
            $property_name = str_replace('$', '', $property->getName());
            $php .= self::TAB;
            if ($property->isPrivate())
                $php .= 'private ';
            if ($property->isProtected())
                $php .= 'protected ';
            if ($property->isPublic())
                $php .= 'public ';
            if ($property->isStatic())
                $php .= 'static ';
            $php .= '$' . $property_name . ';' . self::NL;
        }

        $methods = array();
        foreach ($refl->getMethods() as $method) {
            $methods[$method->getName()] = $method;
        }
        ksort($methods);
        foreach ($methods as $method) {
            if ($method->isPublic()) {
                if ($method->getDocComment()) {
                    $php .= self::TAB . $method->getDocComment() . self::NL;
                }
                $php .= self::TAB . 'public function ';
                if ($method->returnsReference()) {
                    $php .= '&';
                }
                $php .= $method->getName() . '(';
                foreach ($method->getParameters() as $i => $parameter) {
                    $parameter_name = $parameter->getName();
                    if ($parameter_name === '...') {
                        if ($i === 0) {
                            $parameter_name = '/*'.$parameter_name.'*/';
                        } else {
                            $parameter_name = ' /*, '.$parameter_name.'*/';
                        }
                    } else {
                        $parameter_name = '$' . $parameter_name;
                        if ($i >= 1) {
                            $php .= ', ';
                        }
                    }
                    if ($parameter->isArray()) {
                        $php .= 'array ';
                    }
                    try {
                        if ($typehint = $parameter->getClass()) {
                            $php .= self::NS . $typehint->getName() . ' ';
                        }
                    } catch (\ReflectionException $e) {
                        $parse = explode(' ', $e->getMessage());
                        $php .= self::NS . $parse[1] . ' ';
                    }
                    $php .= $parameter_name;
                    if ($parameter->isDefaultValueAvailable()) {
                        if ($parameter->isDefaultValueConstant()) {
                            $defaultValue = preg_replace('#\n|\r\n|\r#', ' ', var_export($parameter->getDefaultValueConstantName(), true));
                        } else {
                            $defaultValue = preg_replace('#\n|\r\n|\r#', ' ', var_export($parameter->getDefaultValue(), true));
                        }
                        $php .= ' = ' . $defaultValue;
                    }
                }
                $php .= ') {}' . self::NL;
            }
        }
        // Close class ... { ...
        $php .= '}';

        return $php;
    }

    /**
     * Get an namespace block with content
     *
     * @param string $namespace_name
     * @param string $block_content
     *            Content of namespace block
     * @return string generated string
     */
    protected function getNamespaceBlock($namespace_name, $block_content)
    {
        // Each line indent with a TAB in the block content:
        $lines = explode(self::NL, $block_content);
        foreach ($lines as $key => $line)
            $lines[$key] = self::TAB . $line;
        $block_content = implode(self::NL, $lines);

        $php = 'namespace ';
        if ($namespace_name != '') {
            $php .= $namespace_name;
        }
        $php .= '{' . $block_content . self::NL . '}' . self::NL;

        return $php;
    }

    /**
     * Get stub string of a function name
     *
     * @param string $function_name
     * @param bool $without_namespace
     * @return string
     */
    protected function getFunctionStubString($function_name, $without_namespace = true)
    {
        $refl = new \ReflectionFunction($function_name);
        if ($without_namespace)
            $function_name = $refl->getShortName();
        $php = '';
        $php .= self::NL . $refl->getDocComment() . self::NL;
        $php .= 'function ' . $function_name . '(';
        foreach ($refl->getParameters() as $i => $parameter) {
            if ($i >= 1) {
                $php .= ', ';
            }
            if ($typehint = $parameter->getClass()) {
                $php .= $typehint->getName() . ' ';
            }
            $php .= '$' . $parameter->getName();
            if ($parameter->isDefaultValueAvailable()) {
                if ($parameter->isDefaultValueConstant()) {
                    $defaultValue = preg_replace('#\n|\r\n|\r#', ' ', var_export($parameter->getDefaultValueConstantName(), true));
                } else {
                    $defaultValue = preg_replace('#\n|\r\n|\r#', ' ', var_export($parameter->getDefaultValue(), true));
                }
                $php .= ' = ' . $defaultValue;
            }
        }
        $php .= ') {}' . self::NL;

        return $php;
    }

    /**
     * Return generated stub string by constant_name and constant value
     * @param string $constant_name
     * @param bool|int|float|string $constant_value
     * @return string
     */
    protected function getConstantStubString($constant_name, $constant_value)
    {
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
        	default: return '';
        }

        $php = '';
        $php .= 'define(' . $constant_name . ', ' . $constant_value . ');' . self::NL;

        return $php;
    }
}
