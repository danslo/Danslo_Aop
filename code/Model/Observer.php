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
            'debug' => Mage::getIsDeveloperMode(),

            /* TODO: Hopefully we can tie this into the mage caching system. */
            'cacheDir' => Mage::getBaseDir('cache') . DS . self::AOP_CACHE_DIR,
        ));
        self::$initialized = true;
    }

}