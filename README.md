[![Build Status](https://travis-ci.org/danslo/Danslo_Aop.svg?branch=master)](https://travis-ci.org/danslo/Danslo_Aop)

# Danslo_Aop

When creating Magento modules, you will often want to change core behavior. Magento provides several ways to do this:
- Codepools: You simply place the entire class in a codepool that is loaded before the core. I don't need to explain why this is a bad idea.
- Rewrites: By registering some configuration XML, one can tell Magento to load your class which then extends from the core class. This becomes a serious problem when multiple modules try rewriting the same class.
- Observers: By far the cleanest solution, but you are limited to observing events that were implemented in the Magento core. No event, no observer.

The solution? AOP! 

## What is AOP?

Paraphrasing from the [Go! AOP](https://github.com/lisachenko/go-aop-php) documentation:
> AOP (Aspect-Oriented Programming) is an approach to cross-cutting concerns, where the concerns are designed and implemented in a "modular" way (that is, with appropriate encapsulation, lack of duplication, etc.), then integrated into all the relevant execution points in a succinct and robust way, e.g. through declarative or programmatic means.

What it comes down to is that we can register aspects that do method interception through things called [Advices](http://go.aopphp.com/docs/pointcuts-and-advices/). These can go after, before, or around method calls. You can also sort multiple advices which is very useful for multiple modules wanting to modify the same methods. It's actually really similar to how Magento 2 does [plugins / interception](https://wiki.magento.com/display/MAGE2DOC/Using+Interception).

More information will follow...

## How does it work?
- We register an additional autoloader that passes class paths to Go! AOP (``FilterInjectorTransformer``).
- We configure an observer that is triggered just after module configuration is loaded. It then;
    - Reads Magento configuration to find registered aspects.
    - Sets up an aspect kernel and registers those aspects.
- Go! AOP takes care of the rest for us...

## Example

After installing this module using [composer](https://getcomposer.org/), let's create a sample module:

``app/etc/modules/Danslo_TestModule.xml``:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<config>
    <modules>
        <Danslo_TestModule>
            <active>true</active>
            <codePool>local</codePool>
        </Danslo_TestModule>
    </modules>
</config>
```

``app/code/local/Danslo/TestModule/etc/config.xml``:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<config>
    <global>
        <aspects>
            <!-- The only requirement is that 'example' is unique in your installation. -->
            <example>Danslo_TestModule_Aspect_Example</example>
        </aspects>
    </global>
</config>
```

``app/code/local/Danslo/TestModule/Aspect/Example.php``:
```php
<?php

use Go\Aop\Aspect;
use Go\Lang\Annotation\Before;
use Go\Aop\Intercept\MethodInvocation;

class Danslo_TestModule_Aspect_Example
    implements Aspect
{

    /**
     * Intercept all public methods for Mage_Cms_Block_Page.
     *
     * @param MethodInvocation $invocation Invocation
     * @Before("execution(public Mage_Cms_Block_Page->*(*))")
     */
    public function beforeMethodExecution(MethodInvocation $invocation)
    {
        $obj = $invocation->getThis();
        echo 'Calling Before Interceptor for method: ',
             $invocation->getMethod()->getName(),
             ' with arguments: ',
             json_encode($invocation->getArguments());
    }

}
```

Flush the cache and reload the homepage.

You will notice that we are intercepting every method call for the ``Mage_Cms_Block_Page`` class without ever having modified it. Isn't that amazing?

## Running Tests

Because we rely on a specific autoloader setup and EcomDev_PHPUnit messes with that, we need our own bootstrapping for phpunit. The following should get you running:

```bash
ECOMDEV_PHPUNIT_CUSTOM_BOOTSTRAP=app/code/community/Danslo/Aop/bootstrap.php \
    phpunit -c ./vendor/ecomdev/ecomdev_phpunit/phpunit.xml.dist
```

## Limitations / Future Work
- We currently only support method interception by registering aspects, but the world of AOP is so much more. We should probably implement some of those other features.

## License

The MIT License (MIT)

Copyright (c) 2015 Daniel Sloof

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
