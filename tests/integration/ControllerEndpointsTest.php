<?php

require_once('../../vendor/autoload.php');
require_once('../../config.php');

class ControllerEndpointsTest extends PHPUnit_Framework_TestCase {

    private $client;

    /**
     * @before
     */
    public function setup() {
        $this->client = new GuzzleHttp\Client();
        date_default_timezone_set('Europe/Paris');
    }
    /**
     * @after
     */
    public function clean() {
        $id_facebook = "IdFacebookTest";
        $requete = $this->client->delete($GLOBALS['api_url'] . 'deleteAll&cle=' . $GLOBALS["cle"]. '&id=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
    }

    /**
     * Methodes utilisables pour executer les requetes disponibles ici (Rubrique : "Requests Methods") : https://guzzle.readthedocs.org/en/guzzle4/http-messages.html
     */

    /**
     * 1er test :
     * - Creation d'un utilisateur
     * - On recupere les utilisateurs
     * - On modifie un utilisateur
     * - On recupere les utilisateurs
     * - On supprime un utilisateur
     * - On recupere les utilisateurs
     */

    public function testEndpointGestionUtilisateur() {
        // Creation d'un utilisateur
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $email = "toto@toto.fr";
        $id_facebook = "IdFacebookTest";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            array(
                'json' => array(
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'photo' => $photo,
                    'email' => $email,
                    'id_facebook' => $id_facebook
                )
            )
        );
        //$this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(201, $requete->getStatusCode());

        // On recupere les utilisateurs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "Email" => $email, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());

        // On modifie un utilisateur
        $nom = "NomModif";
        $prenom = "PrenomModif";
        $photo = "PhotoModif";
        $email = "toto2@toto2.fr";
        $requete = $this->client->put($GLOBALS['api_url'] . 'updateUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            [ 'json' => [
                'nom' => $nom,
                'prenom' => $prenom,
                'photo' => $photo,
                'email' => $email,
                "id_facebook" => $id_facebook
            ]]
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On recupere les utilisateurs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "Email" => $email, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());

        // On supprime un utilisateur
        $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook . '&id_facebook=' . $id_facebook);

        // On recupere les utilisateurs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(204, $requete->getStatusCode());
        $this->assertEquals('', (string)$requete->getBody());
    }

    /**
     * 2e test :
     * - Cr�ation d'un utilisateur
     * - On r�cup�re la liste des utilisateurs
     * - On cr�er un match
     * - On r�cup�re les matchs
     * - On modifie le match
     * - On r�cup�re les matchs
     * - On récupère les groupes de l'utilisateur
     * - On cr�er un groupe
     * - On récupère les groupes de l'utilisateur
     * - On r�cup�re les groupes
     * - On modifie le groupe
     * - On modifie le statut de l'utilisateur pour le groupe
     * - On r�cup�re les groupes
     * - On cr�er un nouvel utilisateur pour ajouter dans le groupe
     * - On ajoute l'utilisateur au groupe
     * - On r�cup�re les utilisateurs du groupe
     * - On cr�er un pronostic d'un utilisateur dans un groupe
     * - On r�cup�re un pronostic d'un utilisateur dans un groupe
     * - On modifie un pronostic d'un utilisateur dans un groupe
     * - On r�cup�re un pronostic d'un utilisateur dans un groupe
     * - On supprime un utilisateur d'un groupe
     * - On r�cup�re les utilisateurs d'un groupe
     * - On supprime un groupe
     * - On r�cup�re les groupes
     * - On supprime les utilisateurs
     * - On v�rifie qu'il n'y a plus d'utilisateurs
     */

    public function testEndpointGestionUtilisateurGroupePronostic() {
        // Creation d'un utilisateur
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $email = "toto@toto.fr";
        $id_facebook = "IdFacebookTest";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            array(
                "json" => array(
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "photo" => $photo,
                    "email" => $email,
                    "id_facebook" => $id_facebook
                )
            )
        );
        //$this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(201, $requete->getStatusCode());

        // On recupere la liste des utilisateurs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "Email" => $email, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());

        // On creer un match
        $equipe1 = "France" ;
        $equipe2 = "Roumanie";
        $date_match = "03-07-2016 20:00:00";
        $groupe = "A";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerMatch&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            array(
                "json" => array(
                    "equipe1" => $equipe1,
                    "equipe2" => $equipe2,
                    "date_match" => $date_match,
                    "groupe" => $groupe
                )
            )
        );
        //$this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(201, $requete->getStatusCode());

        // On recupere les matchs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getMatchs&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("Equipe1" => $equipe1, "Equipe2" => $equipe2, "Score1" => NULL, "Score2" => NULL, "DateMatch" => strtotime($date_match), "Groupe" => $groupe))), (string)$requete->getBody());

        // On modifie le match
        $equipe1_new = "Espagne";
        $equipe2_new = "Angleterre";
        $date_match_new = "05-07-2016 20:00:00";
        $requete = $this->client->put($GLOBALS['api_url'] . 'updateMatch&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            [ 'json' => [
                "equipe1_old" => $equipe1,
                "equipe1_new" => $equipe1_new,
                "equipe2_old" => $equipe2,
                "equipe2_new" => $equipe2_new,
                "score1" => NULL,
                "score2" => NULL,
                "dateMatch_old" => $date_match,
                "dateMatch_new" => $date_match_new,
                "groupe" => "B"
            ]]
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On recupere les matchs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getMatchs&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("Equipe1" => $equipe1_new, "Equipe2" => $equipe2_new, "Score1" => NULL, "Score2" => NULL, "DateMatch" => strtotime($date_match_new), "Groupe" => "B"))), (string)$requete->getBody());

        // On récupère les groupes de l'utilisateur
        $requete = $this->client->get($GLOBALS['api_url'] . 'getGroupesUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook . '&id_facebook=' . $id_facebook);
        $this->assertEquals(204, $requete->getStatusCode());
        $this->assertEquals('', (string)$requete->getBody());

        // On creer un groupe
        $nomGroupe = "NomGroupe";
        $photoGroupe = "PhotoGroupe";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerGroupe&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            array(
                "json" => array(
                    "nom" => $nomGroupe,
                    "admin" => $id_facebook,
                    "photo" => $photoGroupe
                )
            )
        );
        //$this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(201, $requete->getStatusCode());

        // On récupère les groupes de l'utilisateur
        $requete = $this->client->get($GLOBALS['api_url'] . 'getGroupesUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook . '&id_facebook=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomGrp" => $nomGroupe, "AdminGrp" => $id_facebook, "PhotoGrp" => $photoGroupe, "Statut" => "1"))), (string)$requete->getBody());

        // On recupere les groupes
        $requete = $this->client->get($GLOBALS['api_url'] . 'getGroupes&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomGrp" => $nomGroupe, "AdminGrp" => $id_facebook, "PhotoGrp" => $photoGroupe))), (string)$requete->getBody());

        // On modifie le groupe
        $nomGroupeModif = "NomGroupeModif";
        $photoGroupe = "PhotoGroupeModif";
        $requete = $this->client->put($GLOBALS['api_url'] . 'updateGroupe&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            [ 'json' => [
                "old_nom" => $nomGroupe,
                "new_nom" => $nomGroupeModif,
                "admin" => $id_facebook,
                "photo" => $photoGroupe
            ]]
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On modifie le statut de l'utilisateur dans le groupe
        $requete = $this->client->put($GLOBALS['api_url'] . 'updateStatutUtilisateurGroupe&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            [ 'json' => [
                "nom_groupe" => $nomGroupeModif,
                "id_facebook" => $id_facebook,
                "new_statut" => 2
            ]]
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On recupere les utilisateurs du groupe
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateursGroupe&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook . '&groupe=' . $nomGroupeModif);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "Email" => $email, "ID_Facebook" => $id_facebook, "Statut" => "2"))), (string)$requete->getBody());

        // On recupere les groupes
        $requete = $this->client->get($GLOBALS['api_url'] . 'getGroupes&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomGrp" => $nomGroupeModif, "AdminGrp" => $id_facebook, "PhotoGrp" => $photoGroupe))), (string)$requete->getBody());

        // On creer un new utilisateur pour ajouter dans le groupe
        $nomUser = "NomUser";
        $prenomUser = "PrenomUser";
        $photoUser = "PhotoUser";
        $emailUser = "totoUser@toto.fr";
        $idFacebookUser = "IdFacebookUser";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            array(
                "json" => array(
                    "nom" => $nomUser,
                    "prenom" => $prenomUser,
                    "photo" => $photoUser,
                    "email" => $emailUser,
                    "id_facebook" => $idFacebookUser
                )
            )
        );
        //$this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(201, $requete->getStatusCode());

        // On ajoute l'utilisateur dans le groupe
        $requete = $this->client->post($GLOBALS['api_url'] . 'ajouterUtilisateurGroupe&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            array(
                "json" => array(
                    "id_facebook" => $idFacebookUser,
                    "groupe" => $nomGroupeModif,
                    "statut" => 2
                )
            )
        );
        //$this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(201, $requete->getStatusCode());

        // On recupere les utilisateurs du groupe
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateursGroupe&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook . '&groupe=' . $nomGroupeModif);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "Email" => $email, "ID_Facebook" => $id_facebook, "Statut" => "2"), array("NomUti" => $nomUser, "PrenomUti" => $prenomUser, "PhotoUti" => $photoUser, "Email" => $emailUser, "ID_Facebook" => $idFacebookUser, "Statut" => "2"))), (string)$requete->getBody());

        // On creer un pronostic d'un utilisateur dans un groupe
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerPronostic&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            array(
                "json" => array(
                    "id_facebook" => $idFacebookUser,
                    "equipe1" => $equipe1_new,
                    "equipe2" => $equipe2_new,
                    "date_match" => $date_match_new,
                    "score1" => NULL,
                    "score2" => NULL,
                    "resultat" => "1"
                )
            )
        );
        //$this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(201, $requete->getStatusCode());

        // On recupere les pronostics d'un utilisateur dans un groupe
        $requete = $this->client->get($GLOBALS['api_url'] . 'getPronostic&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook
            . '&utilisateur=' . $idFacebookUser
            . '&equipe1=' . $equipe1_new
            . '&equipe2=' . $equipe2_new
            . '&date_match=' . $date_match_new
        );
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array("Utilisateur" => $idFacebookUser, "Score1" => NULL, "Score2" => NULL, "Resultat" => "1")), (string)$requete->getBody());

        // On modifie les pronostics d'un utilisateur dans un groupe
        $requete = $this->client->put($GLOBALS['api_url'] . 'updatePronostic&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            [ 'json' => [
                "id_facebook" => $idFacebookUser,
                "equipe1" => $equipe1_new,
                "equipe2" => $equipe2_new,
                "date_match" => $date_match_new,
                "score1" => "2",
                "score2" => "3",
                "resultat" => "2"
            ]]
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On recupere les pronostics d'un utilisateur dans un groupe
        $requete = $this->client->get($GLOBALS['api_url'] . 'getPronostic&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook
            . '&utilisateur=' . $idFacebookUser
            . '&equipe1=' . $equipe1_new
            . '&equipe2=' . $equipe2_new
            . '&date_match=' . $date_match_new
        );
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array("Utilisateur" => $idFacebookUser, "Score1" => "2", "Score2" => "3", "Resultat" => "2")), (string)$requete->getBody());

        // On supprime un utilisateur d'un groupe
        $requete = $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateurGroupe&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook
            . '&id_facebook=' . $idFacebookUser
            . '&groupe=' . $nomGroupeModif
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On recupere les utilisateurs du groupe
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateursGroupe&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook . '&groupe=' . $nomGroupeModif);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "Email" => $email, "ID_Facebook" => $id_facebook, "Statut" => "2"))), (string)$requete->getBody());

        // On supprime le groupe
        $requete = $this->client->delete($GLOBALS['api_url'] . 'deleteGroupe&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook
            . '&id_facebook=' . $id_facebook
            . '&groupe=' . $nomGroupeModif
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On r�cup�re les groupes
        $requete = $this->client->get($GLOBALS['api_url'] . 'getGroupes&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(204, $requete->getStatusCode());

        // On supprime les utilisateurs
        $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook
            . '&id_facebook=' . $idFacebookUser);
        $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook
            . '&id_facebook=' . $id_facebook);
        $this->assertEquals(204, $requete->getStatusCode());
        $this->assertEquals('', (string)$requete->getBody());

        // On v�rifie qu'il n'y a plus d'utilisateurs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(204, $requete->getStatusCode());
        $this->assertEquals('', (string)$requete->getBody());
    }

    /**
     * 3e test :
     * - Cr�ation d'un utilisateur
     * - On r�cup�re la liste des utilisateurs
     * - On cr�er un match
     * - On r�cup�re les matchs
     * - On modifie le match
     * - On r�cup�re les matchs
     * - On récupère les communautes de l'utilisateur
     * - On cr�er une communaute
     * - On récupère les communautes de l'utilisateur
     * - On r�cup�re les communautes
     * - On modifie la communaute
     * - On modifie le statut de l'utilisateur dans la communauté
     * - On r�cup�re les communautes
     * - On cr�er un nouvel utilisateur pour ajouter dans la communaute
     * - On ajoute l'utilisateur a la communaute
     * - On r�cup�re les utilisateurs de la communaute
     * - On cr�er un pronostic d'un utilisateur dans une communaute
     * - On r�cup�re un pronostic d'un utilisateur dans une communaute
     * - On modifie un pronostic d'un utilisateur dans une communaute
     * - On r�cup�re un pronostic d'un utilisateur dans une communaute
     * - On supprime un utilisateur d'une communaute
     * - On r�cup�re les utilisateurs d'une communaute
     * - On supprime une communaute
     * - On r�cup�re les communautes
     * - On supprime les utilisateurs
     * - On v�rifie qu'il n'y a plus d'utilisateurs
     */

    public function testEndpointGestionUtilisateurCommunautePronostic() {
        // Creation d'un utilisateur
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $email = "toto@toto.fr";
        $id_facebook = "IdFacebookTest";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            array(
                "json" => array(
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "photo" => $photo,
                    "email" => $email,
                    "id_facebook" => $id_facebook
                )
            )
        );
        //$this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(201, $requete->getStatusCode());

        // On recupere la liste des utilisateurs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "Email" => $email, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());

        // On creer un match
        $equipe1 = "France" ;
        $equipe2 = "Roumanie";
        $date_match = "03-07-2016 20:00:00";
        $groupe = "A";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerMatch&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            array(
                "json" => array(
                    "equipe1" => $equipe1,
                    "equipe2" => $equipe2,
                    "date_match" => $date_match,
                    "groupe" => $groupe
                )
            )
        );
        //$this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(201, $requete->getStatusCode());

        // On recupere les matchs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getMatchs&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("Equipe1" => $equipe1, "Equipe2" => $equipe2, "Score1" => NULL, "Score2" => NULL, "DateMatch" => strtotime($date_match), "Groupe" => $groupe))), (string)$requete->getBody());

        // On modifie le match
        $equipe1_new = "Espagne";
        $equipe2_new = "Angleterre";
        $date_match_new = "05-07-2016 20:00:00";
        $requete = $this->client->put($GLOBALS['api_url'] . 'updateMatch&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            [ 'json' => [
                "equipe1_old" => $equipe1,
                "equipe1_new" => $equipe1_new,
                "equipe2_old" => $equipe2,
                "equipe2_new" => $equipe2_new,
                "score1" => NULL,
                "score2" => NULL,
                "dateMatch_old" => $date_match,
                "dateMatch_new" => $date_match_new,
                "groupe" => "B"
            ]]
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On recupere les matchs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getMatchs&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("Equipe1" => $equipe1_new, "Equipe2" => $equipe2_new, "Score1" => NULL, "Score2" => NULL, "DateMatch" => strtotime($date_match_new), "Groupe" => "B"))), (string)$requete->getBody());

        // On récupère les communautes de l'utilisateur
        $requete = $this->client->get($GLOBALS['api_url'] . 'getCommunautesUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook . '&id_facebook=' . $id_facebook);
        $this->assertEquals(204, $requete->getStatusCode());
        $this->assertEquals('', (string)$requete->getBody());

        // On creer une communaute
        $nomCommunaute = "NomCommunaute";
        $photoCommunaute = "PhotoCommunaute";
        $typeCom = "default";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerCommunaute&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            array(
                "json" => array(
                    "nom" => $nomCommunaute,
                    "admin" => $id_facebook,
                    "photo" => $photoCommunaute,
                    "type" => $typeCom
                )
            )
        );
        //$this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(201, $requete->getStatusCode());

        // On récupère les communautes de l'utilisateur
        $requete = $this->client->get($GLOBALS['api_url'] . 'getCommunautesUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook . '&id_facebook=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomCom" => $nomCommunaute, "AdminCom" => $id_facebook, "PhotoCom" => $photoCommunaute, "TypeCom" => $typeCom, "Statut" => "1"))), (string)$requete->getBody());

        // On recupere les communautes
        $requete = $this->client->get($GLOBALS['api_url'] . 'getCommunautes&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomCom" => $nomCommunaute, "AdminCom" => $id_facebook, "PhotoCom" => $photoCommunaute, "TypeCom" => $typeCom))), (string)$requete->getBody());

        // On modifie la communaute
        $nomCommunauteModif = "NomCommunauteModif";
        $photoCommunaute = "PhotoCommunauteModif";
        $requete = $this->client->put($GLOBALS['api_url'] . 'updateCommunaute&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            [ 'json' => [
                "old_nom" => $nomCommunaute,
                "new_nom" => $nomCommunauteModif,
                "admin" => $id_facebook,
                "photo" => $photoCommunaute,
                "type" => $typeCom
            ]]
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On modifie le statut de l'utilisateur dans la communauté
        $requete = $this->client->put($GLOBALS['api_url'] . 'updateStatutUtilisateurCommunaute&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            [ 'json' => [
                "nom_communaute" => $nomCommunauteModif,
                "id_facebook" => $id_facebook,
                "new_statut" => 2
            ]]
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On recupere les communautes
        $requete = $this->client->get($GLOBALS['api_url'] . 'getCommunautes&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomCom" => $nomCommunauteModif, "AdminCom" => $id_facebook, "PhotoCom" => $photoCommunaute, "TypeCom" => $typeCom))), (string)$requete->getBody());

        // On creer un new utilisateur pour ajouter dans la communaute
        $nomUser = "NomUser";
        $prenomUser = "PrenomUser";
        $photoUser = "PhotoUser";
        $emailUser = "totoUser@toto.fr";
        $idFacebookUser = "IdFacebookUser";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            array(
                "json" => array(
                    "nom" => $nomUser,
                    "prenom" => $prenomUser,
                    "photo" => $photoUser,
                    "email" => $emailUser,
                    "id_facebook" => $idFacebookUser
                )
            )
        );
        //$this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(201, $requete->getStatusCode());

        // On ajoute l'utilisateur dans la communaute
        $requete = $this->client->post($GLOBALS['api_url'] . 'ajouterUtilisateurCommunaute&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            array(
                "json" => array(
                    "id_facebook" => $idFacebookUser,
                    "communaute" => $nomCommunauteModif,
                    "statut" => 2
                )
            )
        );
        //$this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(201, $requete->getStatusCode());

        // On recupere les utilisateurs de la communaute
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateursCommunaute&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook . '&communaute=' . $nomCommunauteModif);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "Email" => $email, "ID_Facebook" => $id_facebook, "Statut" => "2"), array("NomUti" => $nomUser, "PrenomUti" => $prenomUser, "PhotoUti" => $photoUser, "Email" => $emailUser, "ID_Facebook" => $idFacebookUser, "Statut" => "2"))), (string)$requete->getBody());

        // On creer un pronostic d'un utilisateur dans une communaute
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerPronostic&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            array(
                "json" => array(
                    "id_facebook" => $idFacebookUser,
                    "equipe1" => $equipe1_new,
                    "equipe2" => $equipe2_new,
                    "date_match" => $date_match_new,
                    "score1" => NULL,
                    "score2" => NULL,
                    "resultat" => "1"
                )
            )
        );
        //$this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(201, $requete->getStatusCode());

        // On recupere les pronostics d'un utilisateur dans une communaute
        $requete = $this->client->get($GLOBALS['api_url'] . 'getPronostic&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook
            . '&utilisateur=' . $idFacebookUser
            . '&equipe1=' . $equipe1_new
            . '&equipe2=' . $equipe2_new
            . '&date_match=' . $date_match_new
        );
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array("Utilisateur" => $idFacebookUser, "Score1" => NULL, "Score2" => NULL, "Resultat" => "1")), (string)$requete->getBody());

        // On modifie les pronostics d'un utilisateur dans une communaute
        $requete = $this->client->put($GLOBALS['api_url'] . 'updatePronostic&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook,
            [ 'json' => [
                "id_facebook" => $idFacebookUser,
                "equipe1" => $equipe1_new,
                "equipe2" => $equipe2_new,
                "date_match" => $date_match_new,
                "score1" => "2",
                "score2" => "3",
                "resultat" => "2"
            ]]
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On recupere les pronostics d'un utilisateur dans une communaute
        $requete = $this->client->get($GLOBALS['api_url'] . 'getPronostic&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook
            . '&utilisateur=' . $idFacebookUser
            . '&equipe1=' . $equipe1_new
            . '&equipe2=' . $equipe2_new
            . '&date_match=' . $date_match_new
        );
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array("Utilisateur" => $idFacebookUser, "Score1" => "2", "Score2" => "3", "Resultat" => "2")), (string)$requete->getBody());

        // On supprime un utilisateur d'une communaute
        $requete = $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateurCommunaute&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook
            . '&id_facebook=' . $idFacebookUser
            . '&communaute=' . $nomCommunauteModif
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On recupere les utilisateurs de la communaute
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateursCommunaute&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook . '&communaute=' . $nomCommunauteModif);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "Email" => $email, "ID_Facebook" => $id_facebook, "Statut" => "2"))), (string)$requete->getBody());

        // On supprime la communaute
        $requete = $this->client->delete($GLOBALS['api_url'] . 'deleteCommunaute&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook
            . '&id_facebook=' . $id_facebook
            . '&communaute=' . $nomCommunauteModif
        );
        $this->assertEquals(200, $requete->getStatusCode());

        // On r�cup�re les communaute
        $requete = $this->client->get($GLOBALS['api_url'] . 'getGroupes&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(204, $requete->getStatusCode());

        // On supprime les utilisateurs
        $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook
            . '&id_facebook=' . $idFacebookUser);
        $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateur&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook
            . '&id_facebook=' . $id_facebook);
        $this->assertEquals(204, $requete->getStatusCode());
        $this->assertEquals('', (string)$requete->getBody());

        // On v�rifie qu'il n'y a plus d'utilisateurs
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"] . '&id=' . $id_facebook);
        $this->assertEquals(204, $requete->getStatusCode());
        $this->assertEquals('', (string)$requete->getBody());
    }
}

?>