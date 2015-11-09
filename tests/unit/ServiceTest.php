<?php

require_once dirname(__FILE__) . "/../../src/Service.php";

class ServiceTest extends PHPUnit_Framework_TestCase {

    private $service;

    /**
     * @before
     */
    public function setup() {
        $this->service = new Service("test");
        $this->service->mysqli->begin_transaction();
    }

    /**
     * @after
     */
    public function clean() {
        $this->service->mysqli->rollback();
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

    public function test_getUtilisateursVide() {
        // assertion basee sur le fait que mysql->query retourne false a l'interieur de la fonction appellee
        $this->assertTrue(empty($this->service->_getUtilisateurs()), "La liste d'utilisateurs devrait etre vide");
    }

    public function test_getUtilisateursSuccess() {
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerUtilisateur("Nom2", "Prenom2", "Photo2", "FB123456u2");
        $this->service->_creerUtilisateur("Nom3", "Prenom3", "Photo3", "FB123456u3");
        $expectedValue1 =
            array("NomUti" => "Nom1",
                "PrenomUti" => "Prenom1",
                "PhotoUti" => "Photo1",
                "ID_Facebook" => "FB123456u1");
        $expectedValue2 =
            array("NomUti" => "Nom2",
                "PrenomUti" => "Prenom2",
                "PhotoUti" => "Photo2",
                "ID_Facebook" => "FB123456u2");
        $expectedValue3 =
            array("NomUti" => "Nom3",
                "PrenomUti" => "Prenom3",
                "PhotoUti" => "Photo3",
                "ID_Facebook" => "FB123456u3");

        $value = $this->service->_getUtilisateurs();

        $this->assertTrue(in_array($expectedValue1, $value), "Le resultat devrait contenir les utilisateurs qui viennent d'etre crees");
        $this->assertTrue(in_array($expectedValue2, $value), "Le resultat devrait contenir les utilisateurs qui viennent d'etre crees");
        $this->assertTrue(in_array($expectedValue3, $value), "Le resultat devrait contenir les utilisateurs qui viennent d'etre crees");
    }

    public function test_getUtilisateursFailure() {
        // sauvegarde de l'objet original
        $original_mysql = $this->service->mysqli;

        // creation du mock
        $mysqliMock = $this->getMockBuilder('mysqli')->setMethods(array('query'))->getMock();

        // creation du stub : si query est appele je veux qu'il retourne false
        $mysqliMock->expects($this->any())
            ->method('query')
            ->willReturn(false);

        // affectation de l'objet mock
        $this->service->mysqli = $mysqliMock;

        // assertion basee sur le fait que mysql->query retourne false a l'interieur de la fonction appellee
        $this->assertEquals(false, $this->service->_getUtilisateurs(), "La methode aurait du echoue");

        // reset mock pour le rollback
        $this->service->mysqli = $original_mysql;
    }

    public function test_deleteUtilisateurSuccess() {
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->assertEquals(1, $this->service->_deleteUtilisateur("FB123456u1"));
    }

    public function test_deleteUtilisateurInexistant() {
        $this->assertEquals(0, $this->service->_deleteUtilisateur("ID_INEXISTANT"));
    }

    public function test_deleteUtilisateurFailureIdVide() {
        $this->assertEquals(-1, $this->service->_deleteUtilisateur(""));
    }
}

?>