<?php

class Danslo_Aop_Test_Aspect_Example
    extends EcomDev_PHPUnit_Test_Case
{

    /**
     * Test method before interception.
     *
     * @test
     * @loadFixture
     * @doNotIndexAll
     */
    public function beforeMethodInterception()
    {
        // Create the intercepted block and call a method on it.
        $page = Mage::app()->getLayout()->createBlock('cms/page');
        $page->quoteEscape('foobar');

        // Now verify that it was in the list of intercepted methods.
        $this->assertContains('quoteEscape', $page->getInterceptedMethods());
    }

}