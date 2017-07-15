<?php

class Team {
    private $teamInfo = array();
    private $pdo;
    
    function __construct($pdo, $id_team) {
        // Initialisation des attributs de classe
        $this->initialize();
        $this->pdo = $pdo;

        //Récupère l'équipes
        $stmt = $this->pdo->prepare("SELECT id_equipe, nom_equipe 
                                FROM equipes e
                                WHERE e.id_equipe LIKE :id_team");

        $stmt->bindParam(":id_team", $id_team, PDO::PARAM_INT);

        
        if ($stmt->execute()){
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res){
                $this->teamInfo['id_equipe'] = $res['id_equipe'];
                $this->teamInfo['nom_equipe'] = $res['nom_equipe'];
            }

            // Mettre à jour  les données des résultats pour l'équipe
            $this->updateInfo($pdo);

        }
    }

    /**
     * Initialise les valeurs/attributs de la classe. 
     * Les victoires à 3 pts ou à 2pts et les défaites à 1pt ou 0pt
     * @param -
     * @return -
     */
     private function initialize(){
        $this->teamInfo['id_equipe'] = FALSE;
        $this->teamInfo['nom_equipe'] = FALSE;
        $this->teamInfo['points_actuels'] = 0;
        $this->teamInfo['matchs_gagnes_3pts'] = 0;
        $this->teamInfo['matchs_gagnes_2pts'] = 0; 
        $this->teamInfo['matchs_perdus_1pt'] = 0;
        $this->teamInfo['matchs_perdus_0pt'] = 0;
        $this->teamInfo['matchs_joues'] = 0;
        $this->teamInfo['sets_joues'] = 0;
        $this->teamInfo['sets_gagnes'] = 0;
        $this->teamInfo['sets_perdus'] = 0;
        $this->teamInfo['matchs_joues'] = 0;
        $this->teamInfo['points_actuels'] = 0;
        $this->teamInfo['sets_ratio'] = 1;
        $this->teamInfo['points_sets_realises'] = 1;
        $this->teamInfo['points_sets_encaisses'] = 1;
        $this->teamInfo['adversersaires_joues'] =array();
     }

    /**
     * Renvoi les détails de l'équipe, points, victoire défaites.... 
     * @return array : détails des résultats de l'équipe
     */
    public function getTeam(){
        return $this->teamInfo;
    }

     /*
     * Mise à jours des données concerant les matchs joués. 
     */
    private function updateInfo(){
        // Compter les matchs joués
        $this->matchsAndSetsPlayed();

        // Compter les sets gagnés
        $this->setsWin();
        
        // Compter les sets perdus
        $this->teamInfo['sets_perdus'] = $this->teamInfo['sets_joues'] - $this->teamInfo['sets_gagnes'];
        
        // Compter les points des sets gagnés et compter les points des sets perdus
        $this->setsPointsWin();
        // Et en ressortir les ratio
         $this->teamInfo['sets_ratio'] = $this->teamInfo['points_sets_realises'] / $this->teamInfo['points_sets_encaisses'];
    }


    /**
     * Compter les matchs et sets joués
     */
    private function matchsAndSetsPlayed(){
        
        $stmt = $this->pdo->prepare("SELECT m.id_match, m.id_equipe_visiteur as 'adversaire', e2.nom_equipe, s.score_home as 'set_team', s.score_visiteur as 'set_oppenent'
                                        FROM matchs m
                                        INNER JOIN equipes e1 ON m.id_equipe_home = e1.id_equipe
                                        INNER JOIN equipes e2 ON m.id_equipe_visiteur = e2.id_equipe
                                        INNER JOIN terrains t ON m.id_terrain = t.id_terrain
                                        INNER JOIN sets s ON s.id_match = m.id_match
                                        WHERE m.id_equipe_home LIKE :id_team
                                    UNION ALL
                                    SELECT m.id_match, m.id_equipe_home as 'adversaire', e1.nom_equipe, s.score_visiteur as 'set_team', s.score_home as 'set_oppenent'
                                        FROM matchs m
                                        INNER JOIN equipes e1 ON m.id_equipe_home = e1.id_equipe
                                        INNER JOIN equipes e2 ON m.id_equipe_visiteur = e2.id_equipe
                                        INNER JOIN terrains t ON m.id_terrain = t.id_terrain
                                        INNER JOIN sets s ON s.id_match = m.id_match
                                        WHERE m.id_equipe_visiteur LIKE :id_team");

        $stmt->bindParam(":id_team", $this->teamInfo['id_equipe'], PDO::PARAM_INT);

        if ($stmt->execute()){
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if($res){
                $this->teamInfo['sets_joues'] = count($res);
                $equipes_adverses_matchs_joues =  array_unique(array_column($res, 'id_match', 'id_match'));
                $this->teamInfo['matchs_joues'] = count($equipes_adverses_matchs_joues);
                $this->teamInfo['adversersaires_joues'] = $res;
                $this->teamInfo['points_actuels'] = 0;
                //$this->teamInfo['test'] =  $equipes_adverses_matchs_joues;

                // Compte le nombre de points obtenus lors des matchs joués et les comptes les victoires, défaites
                foreach($equipes_adverses_matchs_joues as $id_match => $id_adversaire){
                    $points = $this->matchResult($id_match);
                    $this->teamInfo['points_actuels'] += $points;
                    switch($points){
                        case 0 : $this->teamInfo['matchs_perdus_0pt'] ++; break;
                        case 1 : $this->teamInfo['matchs_perdus_1pt'] ++; break;
                        case 2 : $this->teamInfo['matchs_gagnes_2pts'] ++; break;
                        case 3 : $this->teamInfo['matchs_gagnes_3pts'] ++; break;
                        default : 
                            break;
                    }
                }
            }
        }
    }

    /**
     * Renvoi nombre de points obtenu contre une équipe. 
     * Les victoires à 3 pts ou à 2pts et les défaites à 1pt ou 0pt
     * @param int id_equipe_oppenent :  id de l'équipe adverse
     * @return int : nombre de points obtenu lors du match
     */
    private function matchResult($id_match){
        // Différences de sets dans le match n°1 entre l'équipe courante et l'équipe adversaire
        // si >0 alors home a gagné sinon c'est l'adveraire
        $stmt = $this->pdo->prepare("SELECT 
                                    (SELECT COUNT(s.id_set)
                                                        FROM matchs m
                                                        INNER JOIN sets s ON m.id_match = s.id_match
                                                        WHERE m.id_equipe_home LIKE :id_team
                                                        AND m.id_match LIKE :id_match
                                                        AND s.score_home>s.score_visiteur)
                                    +
                                    (SELECT COUNT(s.id_set)
                                                        FROM matchs m
                                                        INNER JOIN sets s ON m.id_match = s.id_match
                                                        WHERE m.id_equipe_visiteur LIKE :id_team
                                                        AND m.id_match LIKE :id_match                         
                                                        AND s.score_home<s.score_visiteur)
                                    -
                                    (SELECT COUNT(s.id_set)
                                                        FROM matchs m
                                                        INNER JOIN sets s ON m.id_match = s.id_match
                                                        WHERE m.id_equipe_home LIKE :id_team
                                                        AND m.id_match LIKE :id_match                         
                                                        AND s.score_home<s.score_visiteur)
                                    -
                                    (SELECT COUNT(s.id_set)
                                                        FROM matchs m
                                                        INNER JOIN sets s ON m.id_match = s.id_match
                                                        WHERE m.id_equipe_visiteur LIKE :id_team
                                                        AND m.id_match LIKE :id_match                         
                                                        AND s.score_home>s.score_visiteur)
                                    
                                    AS 'difference'");

        $stmt->bindParam(":id_team", $this->teamInfo['id_equipe'], PDO::PARAM_INT);
        $stmt->bindParam(":id_match", $id_match, PDO::PARAM_INT);

        if ($stmt->execute()){
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            // Compter le nombre de points obtenus
            // 3 pts si victoire avec plus d'un set d'écart
            // 2 pts si victoire avec un set d'écart
            // 1 pt si défaite avec un set d'écart
            // 0 pt si défaite avec plus d'un set d'écart
            if($res){
                if($res['difference'] >= 2){ return 3;}
                if($res['difference'] == 1){ return 2;}
                if($res['difference'] == 0){ return 1;}
                if($res['difference'] == -1){ return 1;}
                if($res['difference'] <= -2){ return 0;}
            }
            return 0;
        }
    }


    /**
     * Compter les sets gangnés. 
     */
    private function setsWin(){
        $stmt = $this->pdo->prepare("SELECT COUNT(s.id_set) as 'sets_gagnes'
                                        FROM sets s WHERE s.id_set IN
                                                (
                                                    SELECT s.id_set
                                                                    FROM matchs m
                                                                    INNER JOIN equipes e1 ON m.id_equipe_home = e1.id_equipe
                                                                    INNER JOIN sets s ON s.id_match = m.id_match
                                                                    WHERE m.id_equipe_home LIKE :id_team
                                                                    AND s.score_home > s.score_visiteur
                                                    UNION ALL
                                                    SELECT s.id_set
                                                                    FROM matchs m
                                                                    INNER JOIN equipes e2 ON m.id_equipe_visiteur = e2.id_equipe
                                                                    INNER JOIN sets s ON s.id_match = m.id_match
                                                                    WHERE m.id_equipe_visiteur LIKE :id_team
                                                                    AND s.score_home < s.score_visiteur
                                                    )");

        $stmt->bindParam(":id_team", $this->teamInfo['id_equipe'], PDO::PARAM_INT);

        if ($stmt->execute()){
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res){
                $this->teamInfo['sets_gagnes'] = $res['sets_gagnes'];
            }

        }
    }

    /**
     * Compter les points dans les sets. 
     */
    private function setsPointsWin(){
        $stmt = $this->pdo->prepare("SELECT  SUM(s.score_home) as 'points_sets_realises', SUM(s.score_visiteur) as 'points_sets_encaisses'
                                                                    FROM matchs m
                                                                    INNER JOIN equipes e1 ON m.id_equipe_home = e1.id_equipe
                                                                    INNER JOIN sets s ON s.id_match = m.id_match
                                                                    WHERE m.id_equipe_home LIKE :id_team
                                    UNION ALL 
                                    SELECT  SUM(s.score_visiteur) as 'points_sets_realises', SUM(s.score_home) as 'points_sets_encaisses'
                                                                    FROM matchs m
                                                                    INNER JOIN equipes e2 ON m.id_equipe_visiteur = e2.id_equipe
                                                                    INNER JOIN sets s ON s.id_match = m.id_match
                                                                    WHERE m.id_equipe_visiteur LIKE :id_team");

        $stmt->bindParam(":id_team", $this->teamInfo['id_equipe'], PDO::PARAM_INT);

        if ($stmt->execute()){
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res){
                $this->teamInfo['points_sets_realises'] = $res['points_sets_realises'];
                if($res['points_sets_encaisses'])
                    $this->teamInfo['points_sets_encaisses'] = $res['points_sets_encaisses'];
            }

        }
    }
}

?>