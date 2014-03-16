# PHP Ide Stub file Generator

## Examples

See the "example.php"!!!

### Z\IdeStubGenerator\Strategy\PSR0

```php
$stubgenerator_strategy = new Z\IdeStubGenerator\Strategy\PSR0();
// $stubgenerator_strategy->setBaseDir(...); 
// $stubgenerator_strategy->setFunctionsStubFileName(...);
// $stubgenerator_strategy->setConstantsStubFileName(...);

$generator = new Z\IdeStubGenerator\Generator($stubgenerator_strategy);
$generator->addClasses(array('class_name', ...));
$generator->addFunctions(array('function_name', ...));
$generator->addConstants(array('constant_name'=>constant_value, ...));
$generator->generate();
```

### Z\IdeStubGenerator\Strategy\OneFile
```php
$stubgenerator_strategy = new Z\IdeStubGenerator\Strategy\OneFile();
$stubgenerator_strategy->setFilePath(...);


$generator = new Z\IdeStubGenerator\Generator($stubgenerator_strategy);
$generator->addClasses(array('class_name', ...));
$generator->addFunctions(array('function_name', ...));
$generator->addConstants(array('constant_name'=>constant_value, ...));
$generator->generate();
```

## License

Copyright (c) 2014, Rácz Tibor Zoltán <racztiborzoltan+github@gmail.com>

Permission to use, copy, modify, and/or distribute this software for any purpose with or without fee is hereby granted, provided that the above copyright notice and this permission notice appear in all copies.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.

#### Personal notes
  - Sorry for my bad english in the source code! :)
