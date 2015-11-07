<?php

require_once('vendor/autoload.php');

$GLOBALS['api_url'] = 'http://localhost:8080/index.php?rquest=';

class ControllerEndpointsTest extends PHPUnit_Framework_TestCase {

    private $client;

    /**
     * @before
     */
    public function setup() {
        $this->client = new GuzzleHttp\Client([
            'base_url' => $GLOBALS['api_url'],
            'defaults' => ['exceptions' => false]
        ]);
    }

//    public function testEndpointGetUtilisateursSuccess() {
//        $response = $this->client->request('GET', 'getUtilisateurs');
//
//        $this->assertEquals(200, $response->getStatusCode());
//
//        $data = json_decode($response->getBody());
//
//        $this->assertArrayHasKey('NomUti', $data);
//        $this->assertArrayHasKey('PrenomUti', $data);
//        $this->assertArrayHasKey('PhotoUti', $data);
//        $this->assertArrayHasKey('ID_Facebook', $data);
//    }
    public function testStupid() {
        $this->assertTrue(true);
    }
}

?>