<?php
namespace Z\IdeStubGenerator;

abstract class Strategy
{

    /**
     * constant for directory separator character
     *
     * @var string
     */
    const DS = DIRECTORY_SEPARATOR;

    /**
     * Constant for new line character
     *
     * @var string
     */
    const NL = "\n";

    /**
     * Constant for TAB character
     *
     * @var string
     */
    const TAB = "\t";

    abstract public function generate(array $classes);

    protected function getPHPBegin()
    {
        return '<?php' . self::NL;
    }

    protected function getHeader()
    {
        return '';
        // $php = '';
        // $php .= '/**' . self::NL . ' * Generated stub file for code completion purposes' . self::NL . ' */';
        // $php .= self::NL . self::NL;
        // return $php;
    }

    protected function getNamespaceOfClassName($class_name)
    {
        return substr($class_name, 0, strrpos($class_name, '\\'));
    }

    protected function getClassStubString($class_name)
    {
        $php = '';
        $refl = new \ReflectionClass($class_name);
        $class_name = $refl->getName();
        $namespace = $this->getNamespaceOfClassName($class_name);
        $just_class_name = str_replace($namespace . '\\', '', $class_name);
        $doccomment = $refl->getDocComment();
        $php .= self::NL . $doccomment . self::NL;
        $php .= 'class ' . $just_class_name;
        if ($parent = $refl->getParentClass()) {
            $php .= ' extends \\' . $parent->getName();
        }
        $php .= self::NL . '{' . self::NL;
        foreach ($refl->getProperties() as $property) {
            $php .= self::TAB;
            if ($property->isPrivate())
                $php .= 'private ';
            if ($property->isProtected())
                $php .= 'protected ';
            if ($property->isPublic())
                $php .= 'public ';
            if ($property->isStatic())
                $php .= 'static ';
            $php .= '$' . $property->getName() . ';' . self::NL;
        }
        foreach ($refl->getMethods() as $method) {
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
                    if ($i >= 1) {
                        $php .= ', ';
                    }
                    if ($parameter->isArray()) {
                        $php .= 'array ';
                    }
                    try {
                        if ($typehint = $parameter->getClass()) {
                            $php .= '\\'.$typehint->getName() . ' ';
                        }
                    } catch (\ReflectionException $e) {
                        $parse = explode(' ', $e->getMessage());
                        $php .= '\\' . $parse[1] . ' ';
                    }
                    $php .= '$' . $parameter->getName();
                    if ($parameter->isDefaultValueAvailable()) {
                        if ($parameter->isDefaultValueConstant()) {
                            $defaultValue = preg_replace('#\n|\r\n|\r#', ' ', var_export($parameter->getDefaultValueConstantName(), true));
                        }
                        else {
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

        // Each line indent with a TAB:
        $lines = explode(self::NL, $php);
        foreach ($lines as $key => $line)
            $lines[$key] = self::TAB . $line;
        $php = implode(self::NL, $lines);

        // Namespace check:
        if ($namespace == '') {
            $php = 'namespace {' . $php . self::NL . '}' . self::NL;
        } else {
            $php = 'namespace ' . $namespace . '{' . $php . self::NL . '}' . self::NL;
        }

        return $php;
    }
}