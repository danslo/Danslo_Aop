<?php

class Danslo_Aop_Test_Aspect_Example
    extends Danslo_Aop_Test_Case
{

    /**
     * Test method before interception.
     *
     * @loadFixture
     */
    public function testBeforeMethodInterception()
    {
        // Create the intercepted block and call a method on it.
        $page = Mage::app()->getLayout()->createBlock('cms/page');
        $page->getPage();

        // Now verify that it was in the list of intercepted methods.
        $this->assertContains('getPage', $page->getInterceptedMethods());
    }

}
