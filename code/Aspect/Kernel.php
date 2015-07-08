<?php

use Go\Core\AspectKernel;
use Go\Core\AspectContainer;

class Danslo_Aop_Aspect_Kernel
    extends AspectKernel
{

    /**
     * Configuration path to aspects.
     */
    const CONFIG_PATH_AOP_ASPECTS = 'global/aspects';

    /**
     * Gets aspects from config.
     *
     * @return array
     */
    protected function _getAspects()
    {
        return (array)Mage::getConfig()->getNode(self::CONFIG_PATH_AOP_ASPECTS);
    }

    /**
     * Registers all aspects from magento configuration in the aspect kernel.
     *
     * @param AspectContainer $container
     * @return void
     */
    public function configureAop(AspectContainer $container)
    {
        foreach ($this->_getAspects() as $class) {
            if ($class) {
                $aspect = new $class;
                $container->registerAspect($aspect);
            }
        }
    }

}