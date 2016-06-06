<html><body>
<?php
    require_once dirname(__FILE__) . "/../config.php";
    // dictionnaire parsing
    $equipes_dict = array(
        "France" => "France",
        "Romania" => "Roumanie",
        "Albania" => "Albanie",
        "Switzerland" => "Suisse",
        "Wales" => "Galles",
        "Slovakia" => "Slovaquie",
        "England" => "Angleterre",
        "Russia" => "Russie",
        "Poland" => "Pologne",
        "Northern Ireland" => "Irlande du Nord",
        "Germany" => "Allemagne",
        "Ukraine" => "Ukraine",
        "Turkey" => "Turquie",
        "Croatia" => "Croatie",
        "Spain" => "Espagne",
        "Czech Republic" => "Rep. Tcheque",
        "Republic of Ireland" => "Irlande",
        "Sweden" => "Suede",
        "Belgium" => "Belgique",
        "Italy" => "Italie",
        "Austria" => "Autriche",
        "Hungary" => "Hongrie",
        "Portugal" => "Portugal",
        "Iceland" => "Islande"
        );
    // url de la liste des matchs de l'euro2016
    //$url = "http://api.football-data.org/v1/soccerseasons/424/fixtures";
    $url = "http://miscusi-family.fr/test_api.html";

    $json = file_get_contents($url);
    $parsed_json = json_decode($json);
    $liste_matchs = $parsed_json->fixtures;

    // connexion DB
    $conn = mysqli_connect($GLOBALS['db_host_prod'], $GLOBALS['db_user_prod'], $GLOBALS['db_password_prod'], $GLOBALS['database_prod']);

    if (mysqli_connect_errno()) {
        echo "Échec de la connexion : ".mysqli_connect_error()."\n";
        exit();
    }
    foreach($liste_matchs as $match){
        $date = $match->date;
        $equipe1_en = $match->homeTeamName;
        $equipe2_en = $match->awayTeamName;
        $score1 = $match->result->goalsHomeTeam;
        $score2 = $match->result->goalsAwayTeam;
        $equipe1_fr = $equipes_dict[$equipe1_en];
        $equipe2_fr = $equipes_dict[$equipe2_en];
        // Check if match déjà terminé en BDD
        $req = "SELECT Score1
                FROM Match_Euro16
                WHERE Equipe1='$equipe1_fr'
                  AND Equipe2='$equipe2_fr'
                  AND DateMatch=DATE_ADD(STR_TO_DATE('$date','%Y-%m-%dT%T'), INTERVAL 2 HOUR)";
        $result = mysqli_query($conn, $req);
        $is_score_null = false;
        if ($row = mysqli_fetch_assoc($result)) {
            if(is_null($row["Score1"])) {
                $is_score_null = true;
            }
        }
        if($match->status === "FINISHED" && $is_score_null){
            // Maj match
            $req = "UPDATE Match_Euro16
                    SET Score1='$score1', Score2='$score2'
                    WHERE Equipe1='$equipe1_fr'
                      AND Equipe2='$equipe2_fr'
                      AND DateMatch=DATE_ADD(STR_TO_DATE('$date','%Y-%m-%dT%T'), INTERVAL 2 HOUR)";
            mysqli_query($conn, $req);
            switch (true){
                case (($score1 - $score2)>0):
                    $resultat = '1';
                    break;
                case (($score1 - $score2)===0):
                    $resultat = 'N';
                    break;
                case (($score1 - $score2)<0):
                    $resultat = '2';
                    break;
                default:
                    break;
            }

            // Select joueurs ayant le bon pronostic
            $req = "SELECT pro.Utilisateur
                    FROM Pronostic pro
                        JOIN Match_Euro16 mch ON pro.ID_Mch = mch.ID_Mch
                    WHERE mch.Equipe1='$equipe1_fr'
                        AND mch.Equipe2='$equipe2_fr'
                        AND mch.DateMatch=DATE_ADD(STR_TO_DATE('$date','%Y-%m-%dT%T'), INTERVAL 2 HOUR)
                        AND pro.Resultat='$resultat'";

            $liste_utlisateurs_corrects = array();
            $result2 = mysqli_query($conn, $req);
            while ($row = mysqli_fetch_array($result2)) {
                array_push($liste_utlisateurs_corrects, $row["Utilisateur"]);
            }
            $utilisateurs_to_update = implode(",", $liste_utlisateurs_corrects);

            // Maj scores
            $req = "UPDATE Participe
                    SET Points=Points+1
                    WHERE Utilisateur IN ($utilisateurs_to_update)";
            mysqli_query($conn, $req);
        }
    }

    mysqli_close($link);
?>
</body></html>