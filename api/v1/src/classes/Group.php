<?php

class Group {
    // liste des équipes
    private $teams = array();

    function __construct($pdo, $id_group) {
        //récupère tous les id des équipes dans le groupe spécifié
        $stmt = $pdo->prepare("SELECT e.id_equipe 
                                FROM equipes e
                                INNER JOIN groupes g ON e.id_groupe = g.id_groupe
                                WHERE g.id_groupe LIKE :id_group");

        $stmt->bindParam(":id_group", $id_group, PDO::PARAM_INT);
        $this->teams = NULL;
        if ($stmt->execute())
        {
            $teamsID = $stmt->fetchAll(PDO::FETCH_ASSOC);            
            
            // pour chaque équipe récupère les informations des résultats qui permettront de réaliser le classement
            // en créant un objet Team contenant les données...
            foreach($teamsID as $equipe){
                $this->teams[] = new Team($pdo, $equipe['id_equipe']);
            }
        }
    }

    /**
     * Renvoi le classement du groupe. 
     * Les équipes à égalité de points sont départagées selon les critères suivants, appliqués successivement:
     *    a. Le nombre de matchs gagnés (pour autant que le nombre de matchs disputés sont égal, ou, à défaut);
     *    b. le quotient le plus élevé des sets sur l’ensemble des matchs (le nombre de sets gagnés divisé par le nombre de sets perdus);
     *    c. le quotient le plus élevé des points marqués sur l’ensemble des matchs (le nombre de points gagnés divisés par le nombre de points perdus);
     *    d. les rencontres directes en application de la let. b;
     *    e. les rencontres directes en application de la let. c;
     * @return array : classement des équipes
     */
    public function getRanking(){
        if(!$this->teams) 
            return NULL;
        $ranking = $this->teams;
        usort($ranking, array('Group','sortTeamsByPoints'));
        //return $ranking[1]->getPoints();
        foreach($ranking as $equipe){
            $teams[] = $equipe->getTeam();
        }
        return arrayCopy($teams);
    }


    /**
     * Renvoi les informations complètes sur chaque équipes du groupe. 
     * @return array : Liste des équipes avec détails dans l'état actuel
     */
    public function getTeams(){
        if(!$this->teams) 
            return NULL;

        foreach($this->teams as $equipe){
            $teams[] = $equipe->getTeam();
        }
        return $teams;
    }

    private static function sortTeamsByPoints($a, $b) {
	    if($a->getPoints() == $b->getPoints()){ // même nb de points
            if($a->getSetsRatio() == $b->getSetsRatio()){ // même ratio des sets
                if($a->getPointsRatio() == $b->getPointsRatio()){ // même ratio des pts
                    return 0;
                }
                return ($a->getPointsRatio() < $b->getPointsRatio()) ? 1 : -1; // qui a le meilleur ratio de points dans les sets ?
            }
            return ($a->getSetsRatio() < $b->getSetsRatio()) ? 1 : -1; // qui a le meilleure ratio dans sets gagnés/perdus ?
        }
	    return ($a->getPoints() < $b->getPoints()) ? 1 : -1; // qui a le plus de points ?
    }




}
?>