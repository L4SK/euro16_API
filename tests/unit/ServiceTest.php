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
        $this->assertEquals($expectedValue, $value, "La creation d'utilisateur aurait du reussir");
    }
    // Test trop precis, ne pas reproduire
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

        $this->assertEquals($expectedValue, $value, "La creation d'utilisateur aurait du echouer pour doublon");
    }


    public function test_getUtilisateursVide() {
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
    // Test trop precis, ne pas reproduire
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

        // assertion basee sur le fait que mysqli->query retourne false a l'interieur de la fonction appellee
        $this->assertEquals(false, $this->service->_getUtilisateurs(), "La recuperation d'utilisateur aurait du echoue a cause d'une erreur de BDD (mock)");

        // reset mock pour le rollback
        $this->service->mysqli = $original_mysql;
    }


    public function test_deleteUtilisateurSuccess() {
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->assertEquals(1, $this->service->_deleteUtilisateur("FB123456u1"), "La suppression d'utilisateur aurait du reussir");
    }
    public function test_deleteUtilisateurInexistant() {
        $this->assertEquals(0, $this->service->_deleteUtilisateur("ID_INEXISTANT"), "La suppression d'utilisateur aurait du retourner 0 car l'id est inexistant");
    }
    // Test trop precis, ne pas reproduire
    public function test_deleteUtilisateurFailureIdVide() {
        $this->assertEquals(-1, $this->service->_deleteUtilisateur(""), "La suppresion d'utilisateur aurait du echouer pour id vide");
    }


    public function test_creerGroupeSuccess(){
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $nom = "GroupeTest";
        $admin = "FB123456u1";
        $photo = "http://www.adresse-longue.fr/lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo";

        $expectedValue = true;
        $value = $this->service->_creerGroupe($nom, $admin, $photo);
        $this->assertEquals($expectedValue, $value, "La creation de groupe aurait du reussir");
    }
    public function test_creerGroupeFailureAdminInexistant(){
        $nom = "GroupeTest";
        $admin = "UtilisateurInexistant";
        $photo = "http://www.adresse-longue.fr/lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo";

        $expectedValue = false;
        $value = $this->service->_creerGroupe($nom, $admin, $photo);
        $this->assertEquals($expectedValue, $value, "La creation de groupe aurait du echoue pour admin inexistant");
    }


    public function test_creerCommunauteSuccess(){
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $nom = "CommunauteTest";
        $admin = "FB123456u1";
        $photo = "http://www.adresse-longue.fr/lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo";
        $type = "default";

        $expectedValue = true;
        $value = $this->service->_creerCommunaute($nom, $admin, $photo, $type);
        $this->assertEquals($expectedValue, $value, "La creation de communaute aurait du reussir");
    }
    public function test_creerCommunauteFailureAdminInexistant(){
        $nom = "CommunauteTest";
        $admin = "UtilisateurInexistant";
        $photo = "http://www.adresse-longue.fr/lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo";
        $type = "default";

        $expectedValue = false;
        $value = $this->service->_creerCommunaute($nom, $admin, $photo, $type);
        $this->assertEquals($expectedValue, $value, "La creation de groupe aurait du echoue pour admin inexistant");
    }


    public function test_creerMatchSuccess(){
        $equipe1 = "France";
        $equipe2 = "Portugal";
        $date_match = "01-07-2016 20:00:00";

        $expectedValue = true;
        $value = $this->service->_creerMatch($equipe1, $equipe2, $date_match);
        $this->assertEquals($expectedValue, $value, "La creation de match aurait du reussir");
    }
    public function test_creerMatchFailureDuplique(){
        $equipe1 = "France";
        $equipe2 = "Portugal";
        $date_match = "01-07-2016 20:00:00";

        $expectedValue = false;

        // Premiere insertion
        $this->service->_creerMatch($equipe1, $equipe2, $date_match);
        // Deuxieme insertion
        $value = $this->service->_creerMatch($equipe1, $equipe2, $date_match);

        $this->assertEquals($expectedValue, $value, "La creation de match aurait du echouer pour doublon");
    }


    public function test_ajouterUtilisateurGroupeSuccess(){
        $groupe = "NomGroupe";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerGroupe($groupe, "FB123456uAdmin", "Photo1");

        $id_facebook = "FB123456789EXEMPLE";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", $id_facebook);

        $expectedValue = true;
        $value = $this->service->_ajouterUtilisateurGroupe($id_facebook, $groupe);
        $this->assertEquals($expectedValue, $value, "L'ajout d'utilisateur au groupe aurait du reussir");
    }
    public function test_ajouterUtilisateurGroupeFailureDuplique(){
        $groupe = "NomGroupe";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerGroupe($groupe, "FB123456uAdmin", "Photo1");

        $id_facebook = "FB123456789EXEMPLE";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", $id_facebook);

        $expectedValue = false;

        // Premiere insertion
        $this->service->_ajouterUtilisateurGroupe($id_facebook, $groupe);
        // Deuxieme insertion
        $value = $this->service->_ajouterUtilisateurGroupe($id_facebook, $groupe);
        $this->assertEquals($expectedValue, $value, "L'ajout d'utilisateur au groupe aurait du echoue pour doublon");
    }
    public function test_ajouterUtilisateurGroupeFailureUtilisateurInexistant(){
        $groupe = "NomGroupe";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerGroupe($groupe, "FB123456uAdmin", "Photo1");

        $expectedValue = false;
        $value = $this->service->_ajouterUtilisateurGroupe("ID_INEXISTANT", $groupe);
        $this->assertEquals($expectedValue, $value, "L'ajout d'utilisateur au groupe aurait du echouer pour utilisateur inexistant");
    }
    public function test_ajouterUtilisateurGroupeFailureGroupeInexistant(){
        $id_facebook = "FB123456789EXEMPLE";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", $id_facebook);

        $expectedValue = false;
        $value = $this->service->_ajouterUtilisateurGroupe($id_facebook, "NOM_INEXISTANT");
        $this->assertEquals($expectedValue, $value, "L'ajout d'utilisateur au groupe aurait du echouer pour groupe inexistant");
    }


    public function test_ajouterUtilisateurCommunauteSuccess(){
        $communaute = "NomCommunaute";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerCommunaute($communaute, "FB123456uAdmin", "Photo1", "default");

        $id_facebook = "FB123456789EXEMPLE";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", $id_facebook);

        $expectedValue = true;
        $value = $this->service->_ajouterUtilisateurCommunaute($id_facebook, $communaute);
        $this->assertEquals($expectedValue, $value, "L'ajout d'utilisateur a la communaute aurait du reussir");
    }
    public function test_ajouterUtilisateurCommunauteFailureDuplique(){
        $communaute = "NomCommunaute";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerCommunaute($communaute, "FB123456uAdmin", "Photo1", "default");

        $id_facebook = "FB123456789EXEMPLE";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", $id_facebook);

        $expectedValue = false;

        // Premiere insertion
        $this->service->_ajouterUtilisateurCommunaute($id_facebook, $communaute);
        // Deuxieme insertion
        $value = $this->service->_ajouterUtilisateurCommunaute($id_facebook, $communaute);
        $this->assertEquals($expectedValue, $value, "L'ajout d'utilisateur a la communaute aurait du echoue pour doublon");
    }
    public function test_ajouterUtilisateurCommunauteFailureUtilisateurInexistant(){
        $groupe = "NomGroupe";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerCommunaute($groupe, "FB123456uAdmin", "Photo1", "default");

        $expectedValue = false;
        $value = $this->service->_ajouterUtilisateurCommunaute("ID_INEXISTANT", $groupe);
        $this->assertEquals($expectedValue, $value, "L'ajout d'utilisateur a la communaute aurait du echouer pour utilisateur inexistant");
    }
    public function test_ajouterUtilisateurCommunauteFailureCommunauteInexistante(){
        $id_facebook = "FB123456789EXEMPLE";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", $id_facebook);

        $expectedValue = false;
        $value = $this->service->_ajouterUtilisateurCommunaute($id_facebook, "NOM_INEXISTANT");
        $this->assertEquals($expectedValue, $value, "L'ajout d'utilisateur a la communaute aurait du echouer pour communaute inexistante");
    }


    public function test_creerPronosticSuccessGroupe(){
        // TODO

//        $expectedValue = true;
//        $value = $this->service->_creerPronostic($id_facebook, $equipe1, $equipe2, $date_match, $score1, $score2, $resultat, $groupe, '');
//        $this->assertEquals($expectedValue, $value, "L'ajout de pronostic aurait du reussir");

        $this->assertTrue(false);
    }
    public function test_creerPronosticSuccessCommunaute(){
        // TODO

//        $expectedValue = true;
//        $value = $this->service->_creerPronostic($id_facebook, $equipe1, $equipe2, $date_match, $score1, $score2, $resultat, '', $communaute);
//        $this->assertEquals($expectedValue, $value, "L'ajout de pronostic aurait du reussir");

        $this->assertTrue(false);
    }
    public function test_creerPronosticSuccessGlobal(){
        // TODO

//        $expectedValue = true;
//        $value = $this->service->_creerPronostic($id_facebook, $equipe1, $equipe2, $date_match, $score1, $score2, $resultat, '', '');
//        $this->assertEquals($expectedValue, $value, "L'ajout de pronostic aurait du reussir");

        $this->assertTrue(false);
    }
    public function test_creerPronosticFailureUtilisateurInexistant(){
        // TODO
        $this->assertTrue(false);
    }
    public function test_creerPronosticFailureMatchIncorrect(){
        // TODO
        $this->assertTrue(false);
    }
    public function test_creerPronosticFailureResultatIncorrect(){
        // TODO
        $this->assertTrue(false);
    }
}

?>