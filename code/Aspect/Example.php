<?php

use Go\Aop\Aspect;
use Go\Lang\Annotation\Before;
use Go\Aop\Intercept\MethodInvocation;

class Danslo_Aop_Aspect_Example
    implements Aspect
{

    /**
     * Intercept any public method and store them on the object.
     *
     * @param MethodInvocation $invocation Invocation
     * @Before("execution(public Mage_Cms_Block_Page->*(*))")
     */
    public function storeInterceptedPublicMethods(MethodInvocation $invocation)
    {
        $obj = $invocation->getThis();
        $method = $invocation->getMethod()->getName();

        $interceptedMethods = $obj->getInterceptedMethods();
        if ($interceptedMethods === null) {
            $interceptedMethods = array();
        }
        if (!in_array($method, $interceptedMethods)) {
            $interceptedMethods[] = $method;
        }

        $obj->setInterceptedMethods($interceptedMethods);
    }
}