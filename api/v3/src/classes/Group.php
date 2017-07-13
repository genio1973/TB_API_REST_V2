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

    public function getTeams(){
        if(!$this->teams) 
            return NULL;

        foreach($this->teams as $equipe){
            $teams[] = $equipe->getTeam();
        }
        return $teams;
    }

    

}

?>