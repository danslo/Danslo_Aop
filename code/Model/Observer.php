<?php

class Danslo_Aop_Model_Observer
    extends Mage_Core_Model_Observer
{

    /**
     * The AOP cache directory.
     */
    const AOP_CACHE_DIR = 'aop';

    /**
     * Whether or not our autoloader has already been registered.
     *
     * @var boolean
     */
    public static $registered = false;

    /**
     * Whether or not we have initialized our aspect kernel.
     *
     * @var boolean
     */
    public static $initialized = false;

    /**
     * Registers the AOP autoloader.
     *
     * @return void
     */
    public function registerAutoloader()
    {
        if (self::$registered) {
            return;
        }
        spl_autoload_register(array(Mage::getModel('aop/autoloader'), 'loadClass'), true, true);
        self::$registered = true;
    }

    /**
     * Initializes the aspect kernel.
     *
     * @return void
     */
    public function initializeAspectKernel()
    {
        $aspectKernel = Danslo_Aop_Aspect_Kernel::getInstance();
        $aspectKernel->init(array(
            'debug'    => Mage::getIsDeveloperMode(),
            'cacheDir' => $this->_getCacheDir()
        ));
        self::$initialized = true;
    }

    /**
     * Gets the AOP cache directory.
     *
     * @return string
     */
    protected function _getCacheDir()
    {
        return Mage::getBaseDir('cache') . DS . self::AOP_CACHE_DIR;
    }

    /**
     * Clears the AOP cache.
     *
     * Our metadata will be cleared by the magento backend, we don't have to
     * do it here.
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function clearAopCache($observer)
    {
        $type = $observer->getType();
        if ($type && $type !== 'aop') {
            return;
        }

        // Recursively clean up the cache directory.
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->_getCacheDir(),
                RecursiveDirectoryIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            $path = $file->getRealPath();
            if ($file->isDir()) {
                rmdir($path);
            } else {
                unlink($path);
            }
        }
    }

}