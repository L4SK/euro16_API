<?php

require_once('../../vendor/autoload.php');
require_once('../../config.php');

$GLOBALS['api_url'] = 'http://localhost/euro16_API/index.php?rquest=';

class ControllerEndpointsTest extends PHPUnit_Framework_TestCase {

    private $client;

    /**
     * @before
     */
    public function setup() {
        $this->client = new GuzzleHttp\Client();
    }
    /**
     * @after
     */
    public function clean() {
        $requete = $this->client->delete($GLOBALS['api_url'] . 'deleteAll&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
    }

    /**
     * Methodes utilisables pour executer les requetes disponibles ici (Rubrique : "Requests Methods") : https://guzzle.readthedocs.org/en/guzzle4/http-messages.html
     */

    /**
     * 1er test :
     * - post utilisateur
     * - get utilisateur
     * - put utilisateur
     * - get utilisateur
     * - delete utilisateur
     * - get utilisateur
     */

    public function testEndpointGestionUtilisateur() {
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $id_facebook = "IdFacebookTest";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "photo" => $photo,
                    "id_facebook" => $id_facebook
                )
            )
        );
        $this->assertEquals(201, $requete->getStatusCode());
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());
        $nom = "NomModif";
        $prenom = "PrenomModif";
        $photo = "PhotoModif";
        $requete = $this->client->put($GLOBALS['api_url'] . 'updateUtilisateur&cle=' . $GLOBALS["cle"],
            [ 'form_params' => [
                'nom' => $nom,
                'prenom' => $prenom,
                'photo' => $photo,
                "id_facebook" => $id_facebook
            ]]
        );
        $this->assertEquals(200, $requete->getStatusCode());
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());
        $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateur&cle=' . $GLOBALS["cle"] . '&id_facebook=' . $id_facebook);
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(204, $requete->getStatusCode());
        $this->assertEquals('', (string)$requete->getBody());
    }

    /**
     * 2e test :
     * - Création d'un utilisateur
     * - On récupère la liste des utilisateurs
     * - On créer un match
     * - On récupère les matchs
     * - On modifie le match
     * - On récupère les matchs
     * - On créer un groupe
     * - On récupère les groupes
     * - On modifie le groupe
     * - On récupère les groupes
     * - On créer un nouvel utilisateur pour ajouter dans le groupe
     * - On ajoute l'utilisateur au groupe
     * - On récupère les utilisateurs du groupe
     * - On créer un pronostic d'un utilisateur dans un groupe
     * - On récupère un pronostic d'un utilisateur dans un groupe
     * - On modifie un pronostic d'un utilisateur dans un groupe
     * - On récupère un pronostic d'un utilisateur dans un groupe
     * - On supprime un utilisateur d'un groupe
     * - On récupère les utilisateurs d'un groupe
     * - On suprime un groupe
     * - On récupère les groupes
     * - On supprime les utilisateurs
     * - On vérifie qu'il n'y a plus d'utilisateurs
     */

    public function testEndpointGestionUtilisateurGroupePronostic() {
        // Creation d'un utilisateur
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $id_facebook = "IdFacebookTest";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "photo" => $photo,
                    "id_facebook" => $id_facebook
                )
            )
        );
        $this->assertEquals(201, $requete->getStatusCode());

        // On recupere la liste des utilisateurs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());

        // On creer un match
        $equipe1 = "France" ;
        $equipe2 = "Roumanie";
        $date_match = "03-07-2016 20:00:00";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerMatch&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "equipe1" => $equipe1,
                    "equipe2" => $equipe2,
                    "date_match" => $date_match
                )
            )
        );
        $this->assertEquals(201, $requete->getStatusCode());

        // On recupere les matchs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getMatchs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("Equipe1" => $equipe1, "Equipe2" => $equipe2, "Score1" => NULL, "Score2" => NULL, "DateMatch" => strtotime($date_match)))), (string)$requete->getBody());

        // On modifie le match
        $equipe1_new = "Espagne";
        $equipe2_new = "Angleterre";
        $date_match_new = "05-07-2016 20:00:00";
        $requete = $this->client->put($GLOBALS['api_url'] . 'updateMatch&cle=' . $GLOBALS["cle"],
            [ 'form_params' => [
                "equipe1_old" => $equipe1,
                "equipe1_new" => $equipe1_new,
                "equipe2_old" => $equipe2,
                "equipe2_new" => $equipe2_new,
                "score1" => NULL,
                "score2" => NULL,
                "dateMatch_old" => $date_match,
                "dateMatch_new" => $date_match_new
            ]]
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On recupere les matchs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getMatchs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("Equipe1" => $equipe1_new, "Equipe2" => $equipe2_new, "Score1" => NULL, "Score2" => NULL, "DateMatch" => strtotime($date_match_new)))), (string)$requete->getBody());

        // On creer un groupe
        $nomGroupe = "NomGroupe";
        $photoGroupe = "PhotoGroupe";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerGroupe&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "nom" => $nomGroupe,
                    "admin" => $id_facebook,
                    "photo" => $photoGroupe
                )
            )
        );
        $this->assertEquals(201, $requete->getStatusCode());

        // On recupere les groupes
        $requete = $this->client->get($GLOBALS['api_url'] . 'getGroupes&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomGrp" => $nomGroupe, "AdminGrp" => $id_facebook, "PhotoGrp" => $photoGroupe))), (string)$requete->getBody());

        // On modifie le groupe
        $nomGroupeModif = "NomGroupeModif";
        $photoGroupe = "PhotoGroupeModif";
        $requete = $this->client->put($GLOBALS['api_url'] . 'updateGroupe&cle=' . $GLOBALS["cle"],
            [ 'form_params' => [
                "old_nom" => $nomGroupe,
                "new_nom" => $nomGroupeModif,
                "admin" => $id_facebook,
                "photo" => $photoGroupe
            ]]
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On recupere les groupes
        $requete = $this->client->get($GLOBALS['api_url'] . 'getGroupes&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomGrp" => $nomGroupeModif, "AdminGrp" => $id_facebook, "PhotoGrp" => $photoGroupe))), (string)$requete->getBody());

        // On creer un new utilisateur pour ajouter dans le groupe
        $nomUser = "NomUser";
        $prenomUser = "PrenomUser";
        $photoUser = "PhotoUser";
        $idFacebookUser = "IdFacebookUser";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "nom" => $nomUser,
                    "prenom" => $prenomUser,
                    "photo" => $photoUser,
                    "id_facebook" => $idFacebookUser
                )
            )
        );
        $this->assertEquals(201, $requete->getStatusCode());

        // On ajoute l'utilisateur dans le groupe
        $requete = $this->client->post($GLOBALS['api_url'] . 'ajouterUtilisateurGroupe&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "id_facebook" => $idFacebookUser,
                    "groupe" => $nomGroupeModif
                )
            )
        );
        $this->assertEquals(201, $requete->getStatusCode());

        // On recupere les utilisateurs du groupe
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateursGroupe&cle=' . $GLOBALS["cle"] . '&groupe=' . $nomGroupeModif);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "ID_Facebook" => $id_facebook), array("NomUti" => $nomUser, "PrenomUti" => $prenomUser, "PhotoUti" => $photoUser, "ID_Facebook" => $idFacebookUser))), (string)$requete->getBody());

        // On creer un pronostic d'un utilisateur dans un groupe
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerPronostic&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "id_facebook" => $idFacebookUser,
                    "equipe1" => $equipe1_new,
                    "equipe2" => $equipe2_new,
                    "date_match" => $date_match_new,
                    "score1" => NULL,
                    "score2" => NULL,
                    "resultat" => "1",
                    "groupe" => $nomGroupeModif,
                    "communaute" => ""
                )
            )
        );
        $this->assertEquals(201, $requete->getStatusCode());

        // On recupere les pronostics d'un utilisateur dans un groupe
        $requete = $this->client->get($GLOBALS['api_url'] . 'getPronostic&cle=' . $GLOBALS["cle"]
            . '&utilisateur=' . $idFacebookUser
            . '&groupe=' . $nomGroupeModif
            . '&communaute=' . ''
            . '&equipe1=' . $equipe1_new
            . '&equipe2=' . $equipe2_new
            . '&date_match=' . $date_match_new
        );
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array("Utilisateur" => $idFacebookUser, "Score1" => NULL, "Score2" => NULL, "Resultat" => "1")), (string)$requete->getBody());

        // On modifie les pronostics d'un utilisateur dans un groupe
        $requete = $this->client->put($GLOBALS['api_url'] . 'updatePronostic&cle=' . $GLOBALS["cle"],
            [ 'form_params' => [
                "id_facebook" => $idFacebookUser,
                "equipe1" => $equipe1_new,
                "equipe2" => $equipe2_new,
                "date_match" => $date_match_new,
                "score1" => "2",
                "score2" => "3",
                "resultat" => "2",
                "groupe" => $nomGroupeModif,
                "communaute" => ""
            ]]
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On recupere les pronostics d'un utilisateur dans un groupe
        $requete = $this->client->get($GLOBALS['api_url'] . 'getPronostic&cle=' . $GLOBALS["cle"]
            . '&utilisateur=' . $idFacebookUser
            . '&groupe=' . $nomGroupeModif
            . '&communaute=' . ''
            . '&equipe1=' . $equipe1_new
            . '&equipe2=' . $equipe2_new
            . '&date_match=' . $date_match_new
        );
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array("Utilisateur" => $idFacebookUser, "Score1" => "2", "Score2" => "3", "Resultat" => "2")), (string)$requete->getBody());

        // On supprime un utilisateur d'un groupe
        $requete = $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateurGroupe&cle=' . $GLOBALS["cle"]
            . '&id_facebook=' . $idFacebookUser
            . '&groupe=' . $nomGroupeModif
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On recupere les utilisateurs du groupe
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateursGroupe&cle=' . $GLOBALS["cle"]
            . '&utilisateur=' . $idFacebookUser
            . '&groupe=' . $nomGroupeModif
        );
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());

        // On supprime le groupe
        $requete = $this->client->delete($GLOBALS['api_url'] . 'deleteGroupe&cle=' . $GLOBALS["cle"]
            . '&id_facebook=' . $id_facebook
            . '&groupe=' . $nomGroupeModif
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On récupère les groupes
        $requete = $this->client->get($GLOBALS['api_url'] . 'getGroupes&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(204, $requete->getStatusCode());

        // On supprime les utilisateurs
        $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateur&cle=' . $GLOBALS["cle"]
            . '&id_facebook=' . $idFacebookUser);
        $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateur&cle=' . $GLOBALS["cle"]
            . '&id_facebook=' . $id_facebook);
        $this->assertEquals(204, $requete->getStatusCode());
        $this->assertEquals('', (string)$requete->getBody());

        // On vérifie qu'il n'y a plus d'utilisateurs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(204, $requete->getStatusCode());
        $this->assertEquals('', (string)$requete->getBody());
    }

    /**
     * 3e test :
     * - post util
     * - get util
     * - post match
     * - get match
     * - put match
     * - get match
     * - post dans communaute
     * - get communaute
     * - put communaute
     * - get communaute
     * - post pronostic
     * - get pronostic
     * - put pronostic
     * - get pronostic
     * - delete pronostic
     * - get pronostic
     * - delete communaute
     * - get communaute
     * - delete match
     * - get match
     */

    public function testEndpointCreerUtilisateurSuccess() {
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $id_facebook = "IdFacebookTest";
        $data = array(
            "nom" => $nom,
            "prenom" => $prenom,
            "photo" => $photo,
            "id_facebook" => $id_facebook
        );
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"],
            array(
                "json" => $data
            )
        );
        $this->assertEquals(201, $requete->getStatusCode());
    }

    public function testEndpointGetUtilisateursSuccess() {
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $id_facebook = "IdFacebookTest";
        $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "photo" => $photo,
                    "id_facebook" => $id_facebook
                )
            )
        );
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());
    }

    public function testEndpointUpdateUtilisateursSuccess() {
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $id_facebook = "IdFacebookTest";
        $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "photo" => $photo,
                    "id_facebook" => $id_facebook
                )
            )
        );
        $nom = "NomModif";
        $prenom = "PrenomModif";
        $photo = "PhotoModif";
        $id_facebook = "IdFacebookTest";
        $this->client->put($GLOBALS['api_url'] . 'updateUtilisateur&cle=' . $GLOBALS["cle"],
            [ 'form_params' => [
                'nom' => $nom,
                'prenom' => $prenom,
                'photo' => $photo,
                "id_facebook" => $id_facebook
            ]]
        );
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());
    }

    public function testEndpointDeleteUtilisateursSuccess() {
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $id_facebook = "IdFacebookTest";
        $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "photo" => $photo,
                    "id_facebook" => $id_facebook
                )
            )
        );
        $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateur&cle=' . $GLOBALS["cle"] . '&id_facebook=' . $id_facebook);
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(204, $requete->getStatusCode());
        $this->assertEquals('', (string)$requete->getBody());
    }
}

?>