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

    public function test_creerGroupeSuccess() {
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $nom = "GroupeTest";
        $admin = "FB123456u1";
        $photo = "http://www.adresse-longue.fr/lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo";

        $expectedValue = true;
        $value = $this->service->_creerGroupe($nom, $admin, $photo);
        $this->assertEquals($expectedValue, $value, "La creation de groupe aurait du reussir");
    }
    public function test_creerGroupeFailureAdminInexistant() {
        $nom = "GroupeTest";
        $admin = "UtilisateurInexistant";
        $photo = "http://www.adresse-longue.fr/lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo";

        $expectedValue = false;
        $value = $this->service->_creerGroupe($nom, $admin, $photo);
        $this->assertEquals($expectedValue, $value, "La creation de groupe aurait du echoue pour admin inexistant");
    }
    public function test_creerGroupeNomGroupeExistant() {
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $nom = "GroupeTest";
        $admin = "FB123456u1";
        $photo = "http://www.adresse-longue.fr/lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo";

        $expectedValue = false;
        $this->service->_creerGroupe($nom, $admin, $photo);
        $value = $this->service->_creerGroupe($nom, $admin, $photo);
        $this->assertEquals($expectedValue, $value, "La creation de groupe aurait du echoue pour admin inexistant");
    }

    public function test_creerCommunauteSuccess() {
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $nom = "CommunauteTest";
        $admin = "FB123456u1";
        $photo = "http://www.adresse-longue.fr/lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo";
        $type = "default";

        $expectedValue = true;
        $value = $this->service->_creerCommunaute($nom, $admin, $photo, $type);
        $this->assertEquals($expectedValue, $value, "La creation de communaute aurait du reussir");
    }
    public function test_creerCommunauteFailureAdminInexistant() {
        $nom = "CommunauteTest";
        $admin = "UtilisateurInexistant";
        $photo = "http://www.adresse-longue.fr/lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo";
        $type = "default";

        $expectedValue = false;
        $value = $this->service->_creerCommunaute($nom, $admin, $photo, $type);
        $this->assertEquals($expectedValue, $value, "La creation de groupe aurait du echoue pour admin inexistant");
    }
    public function test_creerCommunauteNomCommunauteExistant() {
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $nom = "CommunauteTest";
        $admin = "FB123456u1";
        $photo = "http://www.adresse-longue.fr/lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo_lien_vers_la_photo";
        $type = "default";

        $expectedValue = false;
        $this->service->_creerCommunaute($nom, $admin, $photo, $type);
        $value = $this->service->_creerCommunaute($nom, $admin, $photo, $type);
        $this->assertEquals($expectedValue, $value, "La creation de communaute aurait du reussir");
    }

    public function test_creerMatchSuccess() {
        $equipe1 = "France";
        $equipe2 = "Portugal";
        $date_match = "01-07-2016 20:00:00";

        $expectedValue = true;
        $value = $this->service->_creerMatch($equipe1, $equipe2, $date_match);
        $this->assertEquals($expectedValue, $value, "La creation de match aurait du reussir");
    }
    public function test_creerMatchFailureDuplique() {
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

    public function test_creerPronosticSuccessGroupe() {
        $groupe = "NomGroupe";
        $id_facebook = "FB123456uAdmin";
        $equipe1 = "France";
        $equipe2 = "Portugal";
        $score1 = "";
        $score2 = "";
        $resultat = "1";
        $date_match = "01-07-2015 10:00:00";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", $id_facebook);
        $this->service->_creerGroupe($groupe, $id_facebook, "Photo1");
        $this->service->_creerMatch($equipe1, $equipe2, $date_match);

        $expectedValue = true;
        $value = $this->service->_creerPronostic($id_facebook, $equipe1, $equipe2, $date_match, $score1, $score2, $resultat, $groupe, '');
        $this->assertEquals($expectedValue, $value, "L'ajout de pronostic aurait du reussir");
    }
    public function test_creerPronosticSuccessCommunaute() {
        $communaute = "NomCommunaute";
        $id_facebook = "FB123456uAdmin";
        $equipe1 = "France";
        $equipe2 = "Portugal";
        $score1 = "";
        $score2 = "";
        $resultat = "1";
        $date_match = "01-07-2015 10:00:00";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", $id_facebook);
        $this->service->_creerCommunaute($communaute, $id_facebook, "Photo1", "default");
        $this->service->_creerMatch($equipe1, $equipe2, $date_match);

        $expectedValue = true;
        $value = $this->service->_creerPronostic($id_facebook, $equipe1, $equipe2, $date_match, $score1, $score2, $resultat, '', $communaute);
        $this->assertEquals($expectedValue, $value, "L'ajout de pronostic aurait du reussir");
    }
    public function test_creerPronosticSuccessGlobal() {
        $id_facebook = "FB123456uAdmin";
        $equipe1 = "France";
        $equipe2 = "Portugal";
        $score1 = "";
        $score2 = "";
        $resultat = "1";
        $date_match = "01-07-2015 10:00:00";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", $id_facebook);
        $this->service->_creerMatch($equipe1, $equipe2, $date_match);

        $expectedValue = true;
        $value = $this->service->_creerPronostic($id_facebook, $equipe1, $equipe2, $date_match, $score1, $score2, $resultat, '', '');
        $this->assertEquals($expectedValue, $value, "L'ajout de pronostic aurait du reussir");
    }
    public function test_creerPronosticFailureUtilisateurInexistant() {
        $id_facebook = "FB123456uAdmin";
        $equipe1 = "France";
        $equipe2 = "Portugal";
        $score1 = "";
        $score2 = "";
        $resultat = "1";
        $date_match = "01-07-2015 10:00:00";
        $this->service->_creerMatch($equipe1, $equipe2, $date_match);

        $expectedValue = false;
        $value = $this->service->_creerPronostic($id_facebook, $equipe1, $equipe2, $date_match, $score1, $score2, $resultat, '', '');
        $this->assertEquals($expectedValue, $value, "L'ajout de pronostic aurait du echoue pour utilisateur inexistant");
    }
    public function test_creerPronosticFailureMatchInexistant() {
        $id_facebook = "FB123456uAdmin";
        $equipe1 = "France";
        $equipe2 = "Portugal";
        $score1 = "";
        $score2 = "";
        $resultat = "1";
        $date_match = "01-07-2015 10:00:00";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", $id_facebook);

        $expectedValue = false;
        $value = $this->service->_creerPronostic($id_facebook, $equipe1, $equipe2, $date_match, $score1, $score2, $resultat, '', '');
        $this->assertEquals($expectedValue, $value, "L'ajout de pronostic aurait du echoue pour match inexistant");
    }
    public function test_creerPronosticFailureResultatIncorrect() {
        $id_facebook = "FB123456uAdmin";
        $equipe1 = "France";
        $equipe2 = "Portugal";
        $score1 = "";
        $score2 = "";
        $resultat = "B";
        $date_match = "01-07-2015 10:00:00";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", $id_facebook);
        $this->service->_creerMatch($equipe1, $equipe2, $date_match);

        $expectedValue = false;
        $value = $this->service->_creerPronostic($id_facebook, $equipe1, $equipe2, $date_match, $score1, $score2, $resultat, '', '');
        $this->assertEquals($expectedValue, $value, "L'ajout de pronostic aurait du echoue pour resultat incorrect");
    }

    public function test_ajouterUtilisateurGroupeSuccess() {
        $groupe = "NomGroupe";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerGroupe($groupe, "FB123456uAdmin", "Photo1");

        $id_facebook = "FB123456789EXEMPLE";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", $id_facebook);

        $expectedValue = true;
        $value = $this->service->_ajouterUtilisateurGroupe($id_facebook, $groupe);
        $this->assertEquals($expectedValue, $value, "L'ajout d'utilisateur au groupe aurait du reussir");
    }
    public function test_ajouterUtilisateurGroupeFailureDuplique() {
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
    public function test_ajouterUtilisateurGroupeFailureUtilisateurInexistant() {
        $groupe = "NomGroupe";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerGroupe($groupe, "FB123456uAdmin", "Photo1");

        $expectedValue = false;
        $value = $this->service->_ajouterUtilisateurGroupe("ID_INEXISTANT", $groupe);
        $this->assertEquals($expectedValue, $value, "L'ajout d'utilisateur au groupe aurait du echouer pour utilisateur inexistant");
    }
    public function test_ajouterUtilisateurGroupeFailureGroupeInexistant() {
        $id_facebook = "FB123456789EXEMPLE";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", $id_facebook);

        $expectedValue = false;
        $value = $this->service->_ajouterUtilisateurGroupe($id_facebook, "NOM_INEXISTANT");
        $this->assertEquals($expectedValue, $value, "L'ajout d'utilisateur au groupe aurait du echouer pour groupe inexistant");
    }

    public function test_ajouterUtilisateurCommunauteSuccess() {
        $communaute = "NomCommunaute";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerCommunaute($communaute, "FB123456uAdmin", "Photo1", "default");

        $id_facebook = "FB123456789EXEMPLE";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", $id_facebook);

        $expectedValue = true;
        $value = $this->service->_ajouterUtilisateurCommunaute($id_facebook, $communaute);
        $this->assertEquals($expectedValue, $value, "L'ajout d'utilisateur a la communaute aurait du reussir");
    }
    public function test_ajouterUtilisateurCommunauteFailureDuplique() {
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
    public function test_ajouterUtilisateurCommunauteFailureUtilisateurInexistant() {
        $groupe = "NomGroupe";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerCommunaute($groupe, "FB123456uAdmin", "Photo1", "default");

        $expectedValue = false;
        $value = $this->service->_ajouterUtilisateurCommunaute("ID_INEXISTANT", $groupe);
        $this->assertEquals($expectedValue, $value, "L'ajout d'utilisateur a la communaute aurait du echouer pour utilisateur inexistant");
    }
    public function test_ajouterUtilisateurCommunauteFailureCommunauteInexistante() {
        $id_facebook = "FB123456789EXEMPLE";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", $id_facebook);

        $expectedValue = false;
        $value = $this->service->_ajouterUtilisateurCommunaute($id_facebook, "NOM_INEXISTANT");
        $this->assertEquals($expectedValue, $value, "L'ajout d'utilisateur a la communaute aurait du echouer pour communaute inexistante");
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

        $expectedSize = 3;
        $value = $this->service->_getUtilisateurs();

        $this->assertTrue(in_array($expectedValue1, $value), "Le resultat devrait contenir les utilisateurs qui viennent d'etre crees");
        $this->assertTrue(in_array($expectedValue2, $value), "Le resultat devrait contenir les utilisateurs qui viennent d'etre crees");
        $this->assertTrue(in_array($expectedValue3, $value), "Le resultat devrait contenir les utilisateurs qui viennent d'etre crees");
        $this->assertEquals($expectedSize, sizeOf($value), "Le resultat devrait contenir 3 groupes");
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

    public function test_getGroupesVide() {
        $this->assertTrue(empty($this->service->_getGroupes()), "La liste de groupes devrait etre vide");
    }
    public function test_getGroupesSuccess() {
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerGroupe("Groupe1", "FB123456u1", "PhotoGroupe1");
        $this->service->_creerGroupe("Groupe2", "FB123456u1", "PhotoGroupe2");
        $this->service->_creerGroupe("Groupe3", "FB123456u1", "PhotoGroupe3");

        $expectedValue1 =
            array("NomGrp" => "Groupe1",
                "AdminGrp" => "FB123456u1",
                "PhotoGrp" => "PhotoGroupe1"
            );
        $expectedValue2 =
            array("NomGrp" => "Groupe2",
                "AdminGrp" => "FB123456u1",
                "PhotoGrp" => "PhotoGroupe2"
            );
        $expectedValue3 =
            array("NomGrp" => "Groupe3",
                "AdminGrp" => "FB123456u1",
                "PhotoGrp" => "PhotoGroupe3"
            );
        $expectedSize = 3;
        $value = $this->service->_getGroupes();

        $this->assertTrue(in_array($expectedValue1, $value), "Le resultat devrait contenir les groupes qui viennent d'etre crees");
        $this->assertTrue(in_array($expectedValue2, $value), "Le resultat devrait contenir les groupes qui viennent d'etre crees");
        $this->assertTrue(in_array($expectedValue3, $value), "Le resultat devrait contenir les groupes qui viennent d'etre crees");
        $this->assertEquals($expectedSize, sizeOf($value), "Le resultat devrait contenir 3 groupes");
    }

    public function test_getCommunautesVide() {
        $this->assertTrue(empty($this->service->_getCommunautes()), "La liste de communautes devrait etre vide");
    }
    public function test_getCommunautesSuccess() {
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerCommunaute("Communaute1", "FB123456u1", "PhotoCommunaute1", "default");
        $this->service->_creerCommunaute("Communaute2", "FB123456u1", "PhotoCommunaute2", "default");
        $this->service->_creerCommunaute("Communaute3", "FB123456u1", "PhotoCommunaute3", "default");

        $expectedValue1 =
            array("NomCom" => "Communaute1",
                "AdminCom" => "FB123456u1",
                "PhotoCom" => "PhotoCommunaute1",
                "TypeCom" => "default"
            );
        $expectedValue2 =
            array("NomCom" => "Communaute2",
                "AdminCom" => "FB123456u1",
                "PhotoCom" => "PhotoCommunaute2",
                "TypeCom" => "default"
            );
        $expectedValue3 =
            array("NomCom" => "Communaute3",
                "AdminCom" => "FB123456u1",
                "PhotoCom" => "PhotoCommunaute3",
                "TypeCom" => "default"
            );
        $expectedSize = 3;
        $value = $this->service->_getCommunautes();

        $this->assertTrue(in_array($expectedValue1, $value), "Le resultat devrait contenir les communautes qui viennent d'etre creees");
        $this->assertTrue(in_array($expectedValue2, $value), "Le resultat devrait contenir les communautes qui viennent d'etre creees");
        $this->assertTrue(in_array($expectedValue3, $value), "Le resultat devrait contenir les communautes qui viennent d'etre creees");
        $this->assertEquals($expectedSize, sizeOf($value), "Le resultat devrait contenir 3 communautes");
    }

    public function test_getUtilisateurSuccess() {
        $id_facebook = "FB123456u1";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", $id_facebook);
        $expectedValue =
            array("NomUti" => "Nom1",
                "PrenomUti" => "Prenom1",
                "PhotoUti" => "Photo1",
                "ID_Facebook" => "FB123456u1");

        $value = $this->service->_getUtilisateur($id_facebook);

        $this->assertEquals($expectedValue, $value, "Le resultat devrait contenir l'utilisateur qui vient d'etre cree");
    }
    public function test_getUtilisateurInexistant() {
        $this->assertTrue(empty($this->service->_getUtilisateur("ID_INEXISTANT")), "Le resultat ne devrait rien contenir");
    }

    public function test_getGroupeSuccess() {
        $groupe = "NomGroupe";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerGroupe($groupe, "FB123456u1", "PhotoGroupe1");
        $expectedValue =
            array("NomGrp" => "NomGroupe",
                "AdminGrp" => "FB123456u1",
                "PhotoGrp" => "PhotoGroupe1"
            );

        $value = $this->service->_getGroupe($groupe);

        $this->assertEquals($expectedValue, $value, "Le resultat devrait contenir le groupe qui vient d'etre cree");
    }
    public function test_getGroupeInexistant() {
        $this->assertTrue(empty($this->service->_getGroupe("ID_INEXISTANT")), "Le resultat ne devrait rien contenir");
    }

    public function test_getCommunauteSuccess() {
        $communaute = "NomCommunaute";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerCommunaute($communaute, "FB123456u1", "PhotoCommunaute1", "default");
        $expectedValue =
            array("NomCom" => "NomCommunaute",
                "AdminCom" => "FB123456u1",
                "PhotoCom" => "PhotoCommunaute1",
                "TypeCom" => "default"
            );

        $value = $this->service->_getCommunaute($communaute);

        $this->assertEquals($expectedValue, $value, "Le resultat devrait contenir la communaute qui vient d'etre creee");
    }
    public function test_getCommunauteInexistante() {
        $this->assertTrue(empty($this->service->_getCommunaute("ID_INEXISTANT")), "Le resultat ne devrait rien contenir");
    }

    public function test_getUtilisateursGroupeSuccess() {
        $groupe = "NomGroupe";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerUtilisateur("Nom2", "Prenom2", "Photo2", "FB123456u2");
        $this->service->_creerGroupe($groupe, "FB123456uAdmin", "Photo1");
        $this->service->_ajouterUtilisateurGroupe("FB123456u1", $groupe);
        $this->service->_ajouterUtilisateurGroupe("FB123456u2", $groupe);

        $expectedValue = array(
            array("NomUti" => "Nom1",
                "PrenomUti" => "Prenom1",
                "PhotoUti" => "Photo1",
                "ID_Facebook" => "FB123456u1"
            ),
            array("NomUti" => "Nom2",
                "PrenomUti" => "Prenom2",
                "PhotoUti" => "Photo2",
                "ID_Facebook" => "FB123456u2"
            ),
            array("NomUti" => "NomAdmin",
                "PrenomUti" => "PrenomAdmin",
                "PhotoUti" => "PhotoAdmin",
                "ID_Facebook" => "FB123456uAdmin"
            )
        );
        $value = $this->service->_getUtilisateursGroupe($groupe);
        $this->assertEquals($expectedValue, $value, "Le resultat devrait retourner les utilisateurs du groupe");
    }
    public function test_getUtilisateursGroupeInexistante() {
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerGroupe("NomGroupe", "FB123456uAdmin", "Photo1");

        $expectedValue = false;
        $value = $this->service->_getUtilisateursGroupe("NomGroupeInexistant");
        $this->assertEquals($expectedValue, $value, "Le resultat ne devrait retourner aucun utilisateur du groupe");
    }

    public function test_getUtilisateursCommunauteSuccess() {
        $communaute = "NomCommunaute";
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerUtilisateur("Nom2", "Prenom2", "Photo2", "FB123456u2");
        $this->service->_creerCommunaute($communaute, "FB123456uAdmin", "PhotoCommunaute1", "default");
        $this->service->_ajouterUtilisateurCommunaute("FB123456u1", $communaute);
        $this->service->_ajouterUtilisateurCommunaute("FB123456u2", $communaute);

        $expectedValue = array(
            array("NomUti" => "Nom1",
                "PrenomUti" => "Prenom1",
                "PhotoUti" => "Photo1",
                "ID_Facebook" => "FB123456u1"
            ),
            array("NomUti" => "Nom2",
                "PrenomUti" => "Prenom2",
                "PhotoUti" => "Photo2",
                "ID_Facebook" => "FB123456u2"
            ),
            array("NomUti" => "NomAdmin",
                "PrenomUti" => "PrenomAdmin",
                "PhotoUti" => "PhotoAdmin",
                "ID_Facebook" => "FB123456uAdmin"
            )
        );
        $value = $this->service->_getUtilisateursCommunaute($communaute);
        $this->assertEquals($expectedValue, $value, "Le resultat devrait retourner les utilisateurs de la communaute");
    }
    public function test_getUtilisateursCommunauteInexistante() {
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerCommunaute("NomCommunaute", "FB123456uAdmin", "PhotoCommunaute1", "default");

        $expectedValue = false;
        $value = $this->service->_getUtilisateursCommunaute("NomCommunauteInexistant");
        $this->assertEquals($expectedValue, $value, "Le resultat ne devrait retourner aucun utilisateur de la communaute");
    }

    public function test_updateUtilisateurSuccess() {
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");

        $this->assertTrue($this->service->_updateUtilisateur("Nom2", "Prenom2", "Photo2", "FB123456u1"), "L'update d'utilisateur aurait du reussir");
    }
    public function test_updateUtilisateurIdVide() {
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");

        $this->assertFalse($this->service->_updateUtilisateur("Nom2", "Prenom2", "Photo2", ""), "L'update d'utilisateur n'aurait pas du reussir");
    }
    public function test_updateUtilisateurIdInexistant() {
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");

        $this->assertFalse($this->service->_updateUtilisateur("Nom2", "Prenom2", "Photo2", "FB123456u2"), "L'update d'utilisateur n'aurait pas du reussir");
    }

    public function test_updateGroupeSuccess() {
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerGroupe("NomGroupe", "FB123456uAdmin", "PhotoGroupe");

        $this->assertTrue($this->service->_updateGroupe("NomGroupe", "NomGroupe2", "FB123456uAdmin2", "PhotoGroupe2"), "L'update du groupe aurait du reussir");
    }
    public function test_updateGroupeNomVide() {
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerGroupe("NomGroupe", "FB123456uAdmin", "PhotoGroupe");

        $this->assertFalse($this->service->_updateGroupe("", "NomGroupe2", "FB123456uAdmin2", "PhotoGroupe2"), "L'update du groupe aurait du reussir");
    }
    public function test_updateGroupeNomInexistant() {
        $this->service->_creerUtilisateur("NomAdmin", "PrenomAdmin", "PhotoAdmin", "FB123456uAdmin");
        $this->service->_creerGroupe("NomGroupe", "FB123456uAdmin", "PhotoGroupe");

        $this->assertFalse($this->service->_updateGroupe("NomGroupeInexistant", "NomGroupe2", "FB123456uAdmin2", "PhotoGroupe2"), "L'update du groupe aurait du reussir");
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

    public function test_deleteGroupeSuccess() {
        $nom = "NomGroupe";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerGroupe($nom, "FB123456u1", "PhotoGroupe");

        $expectedValue = 1;
        $value = $this->service->_deleteGroupe($nom);
        $this->assertEquals($expectedValue, $value, "La suppression de groupe aurait du reussir");
        // TODO Peut-etre verifier que le "ON DELETE CASCADE" est respectee? ou alors garder ce cas pour les tests d'integration
    }
    public function test_deleteGroupeInexistant() {
        $nom = "NomGroupe";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerGroupe($nom, "FB123456u1", "PhotoGroupe");

        $expectedValue = 0;
        $value = $this->service->_deleteGroupe("NomInconnu");
        $this->assertEquals($expectedValue, $value, "La suppression de groupe aurait du retourner 0 car l'id est inexistant");
        // TODO Peut-etre verifier que le "ON DELETE CASCADE" est respectee? ou alors garder ce cas pour les tests d'integration
    }

    public function test_deleteCommunauteSuccess() {
        $nom = "NomCommunaute";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerCommunaute($nom, "FB123456u1", "PhotoCommunaute", "default");

        $expectedValue = 1;
        $value = $this->service->_deleteCommunaute($nom);
        $this->assertEquals($expectedValue, $value, "La suppression de communaute aurait du reussir");
        // TODO Peut-etre verifier que le "ON DELETE CASCADE" est respectee? ou alors garder ce cas pour les tests d'integration
    }
    public function test_deleteCommunauteInexistante() {
        $nom = "NomCommunaute";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerCommunaute($nom, "FB123456u1", "PhotoCommunaute", "default");

        $expectedValue = 0;
        $value = $this->service->_deleteCommunaute("NomInconnu");
        $this->assertEquals($expectedValue, $value, "La suppression de communaute aurait du retourner 0 car l'id est inexistant");
        // TODO Peut-etre verifier que le "ON DELETE CASCADE" est respectee? ou alors garder ce cas pour les tests d'integration
    }

    public function test_deleteUtilisateurGroupeSuccess() {
        $nom = "NomGroupe";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerUtilisateur("Nom2", "Prenom2", "Photo2", "FB123456u2");
        $this->service->_creerGroupe($nom, "FB123456u1", "PhotoGroupe");
        $this->service->_ajouterUtilisateurGroupe("FB123456u2", $nom);

        $expectedValue = 1;
        $value = $this->service->_deleteUtilisateurGroupe("FB123456u2", $nom);
        $this->assertEquals($expectedValue, $value, "L'utilisateur aurait du etre supprime du groupe");
    }
    public function test_deleteUtilisateurGroupeFailureSuppressionAdmin() {
        $nom = "NomGroupe";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerGroupe($nom, "FB123456u1", "PhotoGroupe");

        $expectedValue = -1;
        $value = $this->service->_deleteUtilisateurGroupe("FB123456u1", $nom);
        $this->assertEquals($expectedValue, $value, "L'utilisateur n'aurait pas du etre supprime du groupe car il en est l'admin");
    }

    public function test_deleteUtilisateurCommunauteSuccess() {
        $nom = "NomCommunaute";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerUtilisateur("Nom2", "Prenom2", "Photo2", "FB123456u2");
        $this->service->_creerCommunaute($nom, "FB123456u1", "PhotoCommunaute", "default");
        $this->service->_ajouterUtilisateurCommunaute("FB123456u2", $nom);

        $expectedValue = 1;
        $value = $this->service->_deleteUtilisateurCommunaute("FB123456u2", $nom);
        $this->assertEquals($expectedValue, $value, "L'utilisateur aurait du etre supprime de la communaute");
    }
    public function test_deleteUtilisateurCommunauteFailureSuppressionAdmin() {
        $nom = "NomCommunaute";
        $this->service->_creerUtilisateur("Nom1", "Prenom1", "Photo1", "FB123456u1");
        $this->service->_creerCommunaute($nom, "FB123456u1", "PhotoCommunaute", "default");

        $expectedValue = -1;
        $value = $this->service->_deleteUtilisateurCommunaute("FB123456u1", $nom);
        $this->assertEquals($expectedValue, $value, "L'utilisateur n'aurait pas du etre supprime de la communaute car il en est l'admin");
    }

}

?>