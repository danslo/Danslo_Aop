<?php

class Danslo_Aop_Test_Model_Autoloader
    extends Danslo_Aop_Test_Case
{

    /**
     * @return array
     */
    protected function _getAutoloaders()
    {
        $autoloaders = [];
        foreach (spl_autoload_functions() as $autoloader) {
            if (is_array($autoloader)) {
                $autoloaders[] = get_class($autoloader[0]);
            }
        }
        return $autoloaders;
    }

    /**
     * See if we have managed to register our autoloader.
     */
    public function testAutoloaderRegistered()
    {
        $this->assertContains('Danslo_Aop_Model_Autoloader', $this->_getAutoloaders());
    }

    /**
     * @depends testAutoloaderRegistered
     */
    public function testAutoloaderOrder()
    {
        $autoloaders = $this->_getAutoloaders();
        $varienPosition = array_search('Varien_Autoload', $autoloaders);
        $aopPosition = array_search('Danslo_Aop_Model_Autoloader', $autoloaders);
        $this->assertTrue($varienPosition === false || $varienPosition > $aopPosition);
    }

}