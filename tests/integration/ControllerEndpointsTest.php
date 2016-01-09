<?php

require_once('../../vendor/autoload.php');

$GLOBALS['api_url'] = 'http://localhost:63342/euro16_API/index.php?rquest=';

class ControllerEndpointsTest extends PHPUnit_Framework_TestCase {

    private $client;

    /**
     * @before
     */
    public function setup() {
        $this->client = new GuzzleHttp\Client();
    }

    /**
     * Methodes utilisables pour executer les requetes disponibles ici (Rubrique : "Requests Methods") : https://guzzle.readthedocs.org/en/guzzle4/http-messages.html
     */

    public function testEndpointGetUtilisateursSuccess() {
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=e5abee460e9afa6f5dbbd2978df1be82');
        $this->assertEquals(200, $requete->getStatusCode());
    }
}

?>