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
     * Registers all aspects from magento configuration in the aspect kernel.
     *
     * @param AspectContainer $container
     * @return void
     */
    protected function configureAop(AspectContainer $container)
    {
        $config = (array)Mage::getConfig()->getNode(self::CONFIG_PATH_AOP_ASPECTS);
        foreach ($config as $class) {
            if ($class) {
                $aspect = new $class;
                $container->registerAspect($aspect);
            }
        }
    }

}