<?php

require_once dirname(__FILE__) . "/../../src/Controller.php";
require_once dirname(__FILE__) . "/../../src/Service.php";

$GLOBALS['api_url'] = 'http://localhost:8080/index.php?rquest=';

class ControllerTest extends PHPUnit_Framework_TestCase {

    public function testStupid() {
        $this->assertTrue(true);
    }
    private $controller;

    /**
     * @before
     */
    public function setup() {
        $this->controller = new Controller("test");
        $this->controller->service = $this->getMockBuilder('Service')->getMock();
    }

    /*public function testGetUtilisateursSuccess() {
        $this->controller->_method = "GET";

        $this->controller->service->expects($this->once())->method('_getUtilisateurs');

        // Mock la methode service->_getUtilisateurs pour qu'elle retourne 10
        $this->controller->service->method('_getUtilisateurs')->willReturn(10);

        $expectedValue = "200";
        $this->assertEquals($expectedValue, $this->controller->_code);
    }*/


}

?>