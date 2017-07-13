<?php

class Team {
    private $id;
    private $name;
    private $points;
    private $matchPlayed;
    private $matchVictory;
    private $matchLoose;
    private $matchDraw;
    private $setVictory;
    private $setLoose;
    private $pointsSetVictory;
    private $pointsSetLoose;

    private $teamInfo;
    
    function __construct($pdo, $id_team) {

        //Récupère les équipes dans le groupe
        $stmt = $pdo->prepare("SELECT id_equipe, nom_equipe 
                                FROM equipes e
                                WHERE e.id_equipe LIKE :id_team");

        $stmt->bindParam(":id_team", $id_team, PDO::PARAM_INT);

        $this->teamInfo = array();
        if ($stmt->execute()){
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res){
                $this->teamInfo = $res;
                $this->updateInfo();
            }

        }
    }

    public function getTeam(){
        return $this->teamInfo;
    }


    private function updateInfo(){
        // Compter les victoires
        
        // Compter les défaites
        
        // Compter les match nul...en prévision de...
        
        // Compter les sets gagnés
        
        // Compter les sets perdus
        
        // Compter les points des sets gagnés
        // Compter les points des sets perdus
        // Et en ressortir les ratio

        // Compter le nombre de points obtenus
        // 3 pts si victoire avec plus d'un set d'écart
        // 2 pts si victoire avec un set d'écart
        // 1 pt si défaite avec un set d'écart
        // 0 pt si défaite avec plus d'un set d'écart

    }
}

?>