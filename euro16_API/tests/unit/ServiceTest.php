<?php

require_once dirname(__FILE__) . "/../../src/Service.php";

class ServiceTest extends PHPUnit_Framework_TestCase {

    private $service;

    /**
     * @before
     */
    public function setup() {
        $this->service = new Service();
        mysqli_begin_transaction($this->service->db);
    }

    /**
     * @after
     */
    public function clean() {
        mysqli_rollback($this->service->db);
    }

    public function test_creerUtilisateurSuccess() {
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "http://www.adresse-longue.fr/lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo";
        $id_facebook = "FB123456789EXEMPLE";

        $expectedValue = true;
        $value = $this->service->_creerUtilisateur($nom, $prenom, $photo, $id_facebook);
        $this->assertEquals($expectedValue, $value, "La creation aurait du reussir");
    }

    public function test_creerUtilisateurFailureIdVide() {
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "http://www.adresse-longue.fr/lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo";
        $id_facebook = "";

        $expectedValue = false;
        $value = $this->service->_creerUtilisateur($nom, $prenom, $photo, $id_facebook);

        $this->assertEquals($expectedValue, $value, "La creation aurait du echouer pour id_facebook vide");
    }

    public function test_creerUtilisateurFailureDuplique() {
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "http://www.adresse-longue.fr/lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo";
        $id_facebook = "FB123456789EXEMPLE";

        $expectedValue = false;

        // Premiere insertion
        $this->service->_creerUtilisateur($nom, $prenom, $photo, $id_facebook);
        // Deuxieme insertion
        $value = $this->service->_creerUtilisateur($nom, $prenom, $photo, $id_facebook);

        $this->assertEquals($expectedValue, $value, "La creation aurait du echouer pour doublon");
    }

    public function test_getUtilisateursSuccess() {
        // TODO
        $this->assertTrue(true);
    }

    public function test_getUtilisateursFailure() {
        // TODO
        $this->assertTrue(true);
    }

    public function test_getUtilisateursVide() {
        // TODO
        $this->assertTrue(true);
    }
}

?>