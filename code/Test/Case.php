<?php

class Danslo_Aop_Test_Case
    extends EcomDev_PHPUnit_Test_Case
{

    /**
     * Set up AOP tests.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        // Make sure the AspectKernel is always initialized.
        Danslo_Aop_Model_Observer::initializeAspectKernel();

        // Because fixtures can change for every test, reconfigure AOP
        // with possibly new aspects.
        $kernel = Danslo_Aop_Aspect_Kernel::getInstance();
        $kernel->configureAop($kernel->getContainer());
    }

}