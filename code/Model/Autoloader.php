<?php

use Go\Instrument\Transformer\FilterInjectorTransformer;

class Danslo_Aop_Model_Autoloader
{

    /**
     * Gets the class file from class name.
     *
     * @param string $className
     * @return string
     */
    protected function _getClassFile($className)
    {
        return str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $className))) . '.php';
    }

    /**
     * Determines if this is a test class.
     *
     * @param string $className
     * @return boolean
     */
    protected function _isTestClass($className)
    {
        return strpos($className, 'Danslo_Aop_Test_') === 0;
    }

    /**
     * Attempt to load the given class.
     *
     * @param string $className
     * @return void
     */
    public function loadClass($className)
    {
        // Don't do anything if we haven't initialized the aspect kernel yet.
        if (!Danslo_Aop_Model_Observer::$initialized) {
            return;
        }

        // Don't process classes that are part of the test suite.
        // The reason for this is that phpunit and doctrine (used by Go! AOP)
        // annotation checks interfere with eachother.
        // Let's assume we don't want to use AOP for the testcase classes ;)
        if ($this->_isTestClass($className)) {
            return;
        }

        $classFile = stream_resolve_include_path($this->_getClassFile($className));
        if (file_exists($classFile)) {
            include FilterInjectorTransformer::rewrite($classFile);
        }
    }

}
