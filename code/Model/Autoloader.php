<?php

use Go\Instrument\Transformer\FilterInjectorTransformer;

class Danslo_Aop_Model_Autoloader
    extends Varien_Autoload
{

    /**
     * We don't care much for what our parent does, since it's already being
     * done by the original autoloader.
     *
     * @return void
     */
    public function __construct()
    {
        return;
    }

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
     * Attempt to load the given class.
     *
     * @param string $className
     *
     * @return void
     */
    public function loadClass($className)
    {
        // Don't do anything if we haven't initialized the aspect kernel yet.
        if (!Danslo_Aop_Model_Observer::$initialized) {
            return;
        }

        $classFile = stream_resolve_include_path($this->_getClassFile($className));
        if (file_exists($classFile)) {
            include FilterInjectorTransformer::rewrite($classFile);
        }
    }

}