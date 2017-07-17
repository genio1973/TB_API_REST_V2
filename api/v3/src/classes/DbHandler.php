 <?php
/**
 * Classe pour gérer toutes les opérations de db
 * Cette classe aura les méthodes CRUD pour les tables de base de données
 *

 */
class DbHandler {

    private $pdo;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        //Ouverture connexion db
        $db = new DbConnect();
        $this->pdo = $db->connect();
    }

    /* ------------- méthodes de la table `users` ------------------ */

    /**
     * Creation nouvel utilisateur
     * @param String $role
     * @param String $email email de connexion
     * @param String $password mot de passe de connexion
     */
    public function createUser($email, $password, $id_role, $nom, $prenom) {
        require_once 'PassHash.php';
        require 'src/include/config.php';

        // Vérifiez d'abord si l'utilisateur existe déjà dans db
        
         if (!$this->isUserExists($email))
         {
             //$result =true;

            //Générer un hash de mot de passe
            $mot_de_passe = PassHash::hash($password);

            // Générer API key
            $api_key = $this->generateApiKey();
            //return $api_key;

            // requete d'insertion
            $tokenExpiration = date('Y-m-d H:i:s', strtotime('+2 hour'));//the expiration date will be in two hour from the current moment

            $stmt = $this->pdo->prepare("INSERT INTO users(email, mot_de_passe, token, token_expire, id_role, nom_user, prenom_user)
                                         values (:email, :mot_de_passe, :api_key, :tokenExpiration, :id_role, :nom, :prenom)");
            
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);   
            $stmt->bindParam(":mot_de_passe", $mot_de_passe, PDO::PARAM_STR);
            $stmt->bindParam(":api_key", $api_key, PDO::PARAM_STR);
            $stmt->bindParam(":tokenExpiration", $tokenExpiration, PDO::PARAM_STR);
            $stmt->bindParam(":id_role", $id_role, PDO::PARAM_STR);
            $stmt->bindParam(":nom", $nom, PDO::PARAM_STR);
            $stmt->bindParam(":prenom", $prenom, PDO::PARAM_STR);
           
            //Vérifiez pour une insertion réussie
            if ($stmt->execute()) {
                // Utilisateur inséré avec succès
                return $config['message']['USER_CREATED_SUCCESSFULLY'];
            } else {
                //Échec de la création de l'utilisateur
                return $config['message']['USER_CREATE_FAILED'];
            }
            
        } else {
            //Utilisateur avec la même email existait déjà dans la db
            return $config['message']['USER_ALREADY_EXISTED'];
        }
    }



    /**
     * Creation nouveau tournoi
     * @param String $nom_tournoi, nom du tournoi
     * @param Int $id_user, créateur du tournoi
     * @return Asso array :     {
     *                          "nombre_insert": 2,
     *                          "error": false,
     *                          "id_dernier_insert": 35,
     *                          "id_premier_insert": 33
     *                          }
     */
     public function createTournament($nom_tournoi, $id_user) {

         $res['error'] = FALSE;
         $res['error_mgs'] = NULL;
         $res['nombre_insert'] = 0;
         $res['id_premier_insert'] = 0;
         $res['id_dernier_insert'] = 0;
         try {  
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->beginTransaction();

                // préparation des la requêtes multiple
                $stmt = $this->pdo->prepare("INSERT INTO `tournois` (`id_tournoi`, `nom_tournoi`, `id_user`)
                                                    VALUES (NULL, :nom, :user)");

                $stmt->bindParam(":nom", $nom_tournoi, PDO::PARAM_STR);   
                $stmt->bindParam(":user", $id_user, PDO::PARAM_INT);
                $stmt->execute();
                //$this->pdo->exec($stmt);
                $res['nombre_insert'] = 1;
                $res['id_dernier_insert'] = (int) $this->pdo->lastInsertId();
                $res['id_premier_insert'] = $res['id_dernier_insert'] - $res['nombre_insert'] + 1;

                // enregistrement des requêtes
                $this->pdo->commit();
           } catch (Exception $e) {
                $this->pdo->rollBack(); // en cas d'erreur annule les transaction en cours
                $res['error'] = TRUE;
                $res['id_premier_insert'] = 0;
                $res['id_dernier_insert'] = 0;
                $res['nombre_insert'] = 0;
                $res['error_mgs'] = "Veuillez vérifier la requête ! ---> " . $e->getMessage();
        }
        return $res;        
    }


    
    /**
     * Creation nouveaux enregistrements
     * @param String : nom de la table pour les nouveaux enregistrements
     * @param Array : tableau de liste des enregistrements
     * @return Asso array :     {
     *                          "nombre_insert": 2,
     *                          "error": false,
     *                          "id_dernier_insert": 35,
     *                          "id_premier_insert": 33
     *                          }
     */
     public function createMultiple($table, $array) {
            $res['error'] = FALSE;
            $res['error_mgs'] = NULL;
            $res['nombre_insert'] = 0;
            $res['id_premier_insert'] = 0;
            $res['id_dernier_insert'] = 0;

            try {  
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->beginTransaction();

                // préparation des la requêtes multiple
                foreach($array as $a){
                    $res['nombre_insert']++;
                    $sql= "INSERT INTO $table (";
                    $values = " VALUES (";
                    foreach($a as $key=>$val){
                        $sql.= "$key,";
                        $values.="'$val',";
                    }
                    $sql = rtrim($sql,','). ")" . rtrim($values,',') ."); ";
                    $this->pdo->exec($sql);
                }
                $res['id_dernier_insert'] = (int) $this->pdo->lastInsertId();
                $res['id_premier_insert'] = $res['id_dernier_insert'] - $res['nombre_insert'];

                // enregistrement des requêtes
                $this->pdo->commit();
            } catch (Exception $e) {
                $this->pdo->rollBack(); // en cas d'erreur annule les transaction en cours
                $res['error'] = TRUE;
                $res['id_premier_insert'] = 0;
                $res['id_dernier_insert'] = 0;
                $res['nombre_insert'] = 0;
                $res['error_mgs'] = "Veuillez vérifier les requêtes, l'une ou plusieurs d'entre elles contiennent une erreur ! ---> " . $e->getMessage();
        }
        return $res;
    }

    /**
     * Mise à jour d'enregistrements
     * @param String : nom de la table pour les nouveaux enregistrements
     * @param Array : tableau de liste des enregistrements
     * @param Integer : id de l'élément à mettre à jour
     * @return Asso array :     {
     *                          "error": false,
     *                          "error_mgs": false
     *                          }
     */
     public function updateByID($table, $array, $id) {
            $res['error'] = FALSE;
            $res['error_mgs'] = NULL;

            try {  
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->beginTransaction();

                // préparation des la requêtes multiple
                    $sql= "UPDATE $table SET ";
                    
                    foreach($array as $key=>$val){
                        $sql.= " $key=";
                        $sql.="'$val',";
                    }
                    $sql = rtrim($sql,',') ." WHERE id_". rtrim($table,'s')."=$id; ";
                    //return $sql;

                    $this->pdo->exec($sql);

                // enregistrement des requêtes
                $this->pdo->commit();
            } catch (Exception $e) {
                $this->pdo->rollBack(); // en cas d'erreur annule les transaction en cours
                $res['error'] = TRUE;
                $res['error_mgs'] = "Veuillez vérifier la requête et ses champs ! ---> " . $e->getMessage();
        }
        return $res;
    }


/**
     * Suppression d'un enregistrement
     * @param String : nom de la table pour les nouveaux enregistrements
     * @param Integer : id de l'élément à supprimer
     * @param Integer : id_user, l'élément à supprimer doit appartenir à l'id_user
     * @return Asso array :     {
     *            "error": false,
     *            "error_mgs": null,
     *            "nombre_suppression": 1
     *           }
     */
     public function deleteByID($table, $id_to_delete) {
            $res['error'] = FALSE;
            $res['error_mgs'] = NULL;
            $res['nombre_suppression'] = 0;
            $res['id_supprimer'] = 0;
            $id_table = 'id_'.rtrim($table,'s');

            $stmt = $this->pdo->prepare("DELETE FROM $table WHERE $id_table = :id");
            $stmt->bindParam(":id", $id_to_delete, PDO::PARAM_INT);

            if($stmt->execute()){
                $res['nombre_suppression'] = 1;
                $res['id_supprimer'] = $id_to_delete;
            }
            else{
                $res['error'] = TRUE;
                $res['error_mgs'] = "Suppression non réussie !";
            }
            return $res;
        }

    
    /**
     * Suppression de tous les terrains pour un id de tournoi
     * @param Integer : id_tournament
     */
     public function deletePitchByTournamentID($id_tournament) {
            // Cherche tous les id des terrains du tournois
            $stmt = $this->pdo->prepare("SELECT DISTINCT m.id_terrain FROM tournois t
                                            INNER JOIN groupes g ON g.id_tournoi = t.id_tournoi
                                            INNER JOIN equipes e ON e.id_groupe = g.id_groupe
                                            INNER JOIN matchs m ON m.id_equipe_home = e.id_equipe
                                            INNER JOIN equipes e2 ON m.id_equipe_visiteur = e2.id_equipe
                                            WHERE t.id_tournoi LIKE :id");
            $stmt->bindParam(":id", $id_tournament, PDO::PARAM_INT);
            
            if ($stmt->execute()){
                $ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if($ids){
                    $idList ="";
                    foreach($ids as $id){
                        $idList .=$id['id_terrain'].","; 
                    }
                    $idList = rtrim($idList,',');
                    $stmt = $this->pdo->prepare("DELETE from terrains WHERE id_terrain IN ($idList)");
                    // $stmt->bindParam(":id", $id_tournament, PDO::PARAM_INT);
                    if ($stmt->execute()){
                        return TRUE;
                    }
                }
            }
            return FALSE;
        }


    /**
     * Suppression du score (sets liés à un match)
     * @param Integer : id_match
     */
     public function deleteScoreByMatchID($id_match) {
            $resultat['error'] = TRUE;
            $resultat['error_mgs'] = "Echec de la suppression des sets !";
            $resultat['nombre_suppression'] = 0;
            $resultat['id_supprimer'] = 0;

            // Cherche tous les id des sets du match
            $stmt = $this->pdo->prepare("SELECT DISTINCT s.id_set FROM sets s
                                            INNER JOIN matchs m ON m.id_match = s.id_match
                                            WHERE s.id_match LIKE :id");
            $stmt->bindParam(":id", $id_match, PDO::PARAM_INT);
            
            if ($stmt->execute()){
                $ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if($ids){
                    $idList ="";
                    foreach($ids as $id){
                        $idList .=$id['id_set'].","; 
                    }
                    $idList = rtrim($idList,',');

                    // Avec la liste des id des match on supprimer dans les sets
                    $stmt = $this->pdo->prepare("DELETE from sets WHERE id_set IN ($idList)");
                    if ($stmt->execute()){
                        $resultat['error'] = FALSE;
                        $resultat['error_mgs'] = "Suppression réussie !";
                        $resultat['nombre_suppression'] = 0;
                        $resultat['id_supprimer'] = $id_match;
                    }
                }
                else{
                    $resultat['error'] = FALSE;
                    $resultat['error_mgs'] = "Pas de score à supprimer !";
                }
            }
            return $resultat;
        }

    /**
     * Vérification de connexion de l'utilisateur
     * @param String $email
     * @param String $password
     * @return boolean Le statut de connexion utilisateur réussite / échec
     */
    public function checkLogin($email, $password) {
        // Obtention de l'utilisateur par email
        $stmt = $this->pdo->prepare("SELECT mot_de_passe FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);

   
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (PassHash::check_password($result['mot_de_passe'], $password)) {
            //if (PassHash::check_password_sha1($result['mot_de_passe'], $password)) {
                // Mot de passe utilisateur est correct

                // Création d'une nouvelle clé API
                // Générer API key
                $api_key = $this->generateApiKey();
                //return $api_key;

                // requete d'insertion
                $tokenExpiration = date('Y-m-d H:i:s', strtotime('+2 hour'));//the expiration date will be in two hour from the current moment
                
                $stmt = $this->pdo->prepare("UPDATE users u SET u.token = :api_key, u.token_expire = :token_exp  WHERE email = :email");
                $stmt->bindParam(":email", $email, PDO::PARAM_STR);
                $stmt->bindParam(":api_key", $api_key, PDO::PARAM_STR);
                $stmt->bindParam(":token_exp", $tokenExpiration, PDO::PARAM_STR);

                if ($stmt->execute())
                {
                    return TRUE;
                }
            }
        }            
         // mot de passe utilisateur est incorrect ou utilisateur non trouvé !
         return FALSE;
    }

    /**
     * Vérification de l'utilisateur en double par adresse e-mail
     * @param String $email email à vérifier dans la db
     * @return boolean
     */
    private function isUserExists($email) {
        $stmt = $this->pdo->prepare("SELECT id_user from users WHERE email = :email");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);

        if ($stmt->execute())
        {
            if($stmt->fetch(PDO::FETCH_ASSOC))
            {
                return true;
            }
        }
        return NULL;
    }

    /**
     *Obtention de l'utilisateur par email
     * @param String $email
     */
    public function getUserByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT u.id_user, u.email, u.token, u.token_expire, u.nom_user, u.prenom_user, u.status, u.id_role, r.droits FROM users u
                                        INNER JOIN roles r ON r.id_role = u.id_role
                                        WHERE email = :email");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        if ($stmt->execute()){
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user;
        }
        return NULL;
    }

    /**
     *Obtention des utilisateurs
     */
    public function getUsers() {
        $stmt = $this->pdo->prepare("SELECT u.id_user, u.email, u.token_expire, u.nom_user, u.prenom_user, u.status, u.id_role, r.droits FROM users u
                                        INNER JOIN roles r ON r.id_role = u.id_role");       
        if ($stmt->execute()){
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $users;
        }
        return NULL;
    }


    /**
     * Obtention de la clé API de l'utilisateur
     * @param String $user_id clé primaire de l'utilisateur
     */
    public function getApiKeyById($user_id) {
        $stmt = $this->pdo->prepare("SELECT token FROM users WHERE id_user = :id");
        $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return  $res['token'];
        } else {
            return NULL;
        }
    }

    /**
     * Obtention de l'identifiant de l'utilisateur par clé API
     * @param String $api_key
     */
    public function getUserId($api_key) {
        $stmt = $this->pdo->prepare("SELECT id_user FROM users WHERE token = :api_key");
        $stmt->bindParam(":api_key", $api_key, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['id_user'];
        } else {
            return NULL;
        }
    }

    /**
     * Validation de la clé API de l'utilisateur
     * Si la clé API est là dans db, elle est une clé valide
     * @param String $api_key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $stmt = $this->pdo->prepare("SELECT token_expire from users WHERE token = :api_key");
        $stmt->bindParam(":api_key", $api_key, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $tokenExpiration = $res['token_expire'];

            //API Key expiration ?
            if($tokenExpiration > date('Y-m-d H:i:s')){
                return true;
            } 
        }
        return false;
    }


    /**
     * Validation de la clé API d'un l'utilisateur défini
     * Si la clé API est là dans db et correspond à l'id du user
     * @param String $api_key
     * @param Integer $id
     * @return boolean
     */
    public function isValidApiKeyWithID($api_key, $id) {
        $stmt = $this->pdo->prepare("SELECT token_expire from users WHERE token = :api_key AND id_user = :id");
        $stmt->bindParam(":api_key", $api_key, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $tokenExpiration = $res['token_expire'];

            //API Key expiration ?
            if($tokenExpiration > date('Y-m-d H:i:s')){
                return true;
            } 
        }
        return false;
    }



    /**
     * Validation de la clé API d'un role défini
     * Si la clé API est là dans db et correspond à l'id du user
     * @param String $api_key
     * @param Integer $id
     * @return boolean
     */
    public function isValidRoleApiKeyWithID($api_key, $id, $role) {
        try{
            $stmt = $this->pdo->prepare("SELECT token_expire, id_role from users WHERE token = :api_key AND id_user = :id");
            $stmt->bindParam(":api_key", $api_key, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
           
            if ($stmt->execute()) {
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                $tokenExpiration = $res['token_expire'];
                $id_role = $res['id_role'];

                //API Key expiration ?
                if( $id_role <= $role && $tokenExpiration > date('Y-m-d H:i:s')){
                    return true;
                } 
            }
        } catch (Exception $e) {
            //return $e->getMessage();
            return false;
        }
        return false;
    }


    /**
     * Génération aléatoire unique MD5 String pour utilisateur clé Api
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }

    /**
     * Validation de la propriété d'un personne
     * @param Integer $id_current_user
     * @param Integer $id_personne
     * @return boolean
     */
    public function isPeopleOwner($id_current_user, $id_personne) {
        $stmt = $this->pdo->prepare("SELECT p.id_personne 
                                        FROM users u
                                        INNER JOIN tournois t ON t.id_user = u.id_user
                                        INNER JOIN groupes g ON g.id_tournoi = t.id_tournoi
                                        INNER JOIN equipes e ON e.id_groupe = g.id_groupe
                                        INNER JOIN personnes p ON p.id_equipe = e.id_equipe
                                        WHERE u.id_user = :id_user AND p.id_personne = :id_personne");
        $stmt->bindParam(":id_user", $id_current_user, PDO::PARAM_INT);
        $stmt->bindParam(":id_personne", $id_personne, PDO::PARAM_INT);
        if ($stmt->execute())
        {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res){
                return true;
            }
        }
        return false;
    }

    /**
     * Validation de la propriété du groupe pour un match
     * @param Integer $id_current_user
     * @param Integer $id_match
     * @return boolean
     */
    public function isMatchOwner($id_current_user, $id_match) {
        $stmt = $this->pdo->prepare("SELECT g.id_groupe, m.id_match 
                                        FROM users u
                                        INNER JOIN tournois t ON t.id_user = u.id_user
                                        INNER JOIN groupes g ON g.id_tournoi = t.id_tournoi
                                        INNER JOIN equipes e ON e.id_groupe = g.id_groupe
                                        INNER JOIN matchs m ON m.id_equipe_home = e.id_equipe
                                        WHERE u.id_user = :id_user AND m.id_match = :id_match");
        $stmt->bindParam(":id_user", $id_current_user, PDO::PARAM_INT);
        $stmt->bindParam(":id_match", $id_match, PDO::PARAM_INT);
        if ($stmt->execute())
        {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res){
                return true;
            }
        }
        return false;
    }

    /**
     * Validation de la propriété de l'équipe dans un groupe de l'utilsateur
     * @param Integer $id_current_user
     * @param Integer $id_team
     * @return boolean
     */
    public function isTeamOwner($id_current_user, $id_team) {
        $stmt = $this->pdo->prepare("SELECT g.id_groupe 
                                         FROM users u
                                         INNER JOIN tournois t ON t.id_user = u.id_user
                                         INNER JOIN groupes g ON g.id_tournoi = t.id_tournoi
                                         INNER JOIN equipes e ON e.id_groupe = g.id_groupe
                                         WHERE u.id_user = :id_user AND e.id_equipe = :id_team");
        $stmt->bindParam(":id_user", $id_current_user, PDO::PARAM_INT);
        $stmt->bindParam(":id_team", $id_team, PDO::PARAM_INT);
        if ($stmt->execute())
        {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res){
                return true;
            }
        }
        return false;
    }

    /**
     * Validation de la propriété du groupe pour un utilsateur
     * @param Integer $id_current_user
     * @param Integer $id_group
     * @return boolean
     */
    public function isGroupOwner($id_current_user, $id_group) {
        $stmt = $this->pdo->prepare("SELECT t.id_tournoi 
                                         FROM users u
                                         INNER JOIN tournois t ON t.id_user = u.id_user
                                         INNER JOIN groupes g ON g.id_tournoi = t.id_tournoi
                                         WHERE u.id_user = :id_user AND g.id_groupe = :id_group");
        $stmt->bindParam(":id_user", $id_current_user, PDO::PARAM_INT);
        $stmt->bindParam(":id_group", $id_group, PDO::PARAM_INT);
        if ($stmt->execute())
        {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res){
                return true;
            }
        }
        return false;
    }

    /**
     * Validation de la propriété du tournoi pour un utilsateur
     * @param Integer $id_current_user
     * @param Integer $id_tournoi
     * @return boolean
     */
    public function isTournamentOwner($id_current_user, $id_tournoi) {
        $stmt = $this->pdo->prepare("SELECT t.id_tournoi 
                                         FROM  tournois t
                                         WHERE t.id_tournoi = :id_tournoi
                                         AND t.id_user = :id_user ");
        $stmt->bindParam(":id_user", $id_current_user, PDO::PARAM_INT);
        $stmt->bindParam(":id_tournoi", $id_tournoi, PDO::PARAM_INT);
        if ($stmt->execute())
        {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if($res){
                return true;
            }
        }
        return false;
    }


    /**
     *Obtention des tournois créés par utilisateur
     * @param String $email
     */
    public function getTournamentCreatedUserByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT t.id_tournoi, t.nom_tournoi, t.id_statut as 'id_statut_tournoi', s.nom_statut as 'statut_tournoi',
                                            u.email, u.nom_user, u.prenom_user, u.id_user, u.id_role, r.droits, u.status as 'statut_utilisateur'
                                    FROM tournois t
                                    INNER JOIN users u ON u.id_user=t.id_user
                                    INNER JOIN roles r ON r.id_role=u.id_role
                                    INNER JOIN statuts s ON s.id_statut = t.id_statut  
                                    WHERE u.email = :email");

        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        if ($stmt->execute())
        {
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $response;
        }
        return NULL;
    }


    /**
     *Obtention des tournois créés par utilisateur (id)
     * @param String $email
     */
    public function getTournamentCreatedUserById($id_user) {
        $stmt = $this->pdo->prepare("SELECT t.id_tournoi, t.nom_tournoi, t.id_statut as 'id_statut_tournoi', s.nom_statut as 'statut_tournoi',
                                            u.email, u.nom_user, u.prenom_user, u.id_user, u.id_role, r.droits, u.status as 'statut_utilisateur'
                                    FROM tournois t
                                    INNER JOIN users u ON u.id_user=t.id_user
                                    INNER JOIN roles r ON r.id_role=u.id_role
                                    INNER JOIN statuts s ON s.id_statut = t.id_statut  
                                    WHERE u.id_user = :id_user");
        /*
        $stmt = $this->pdo->prepare("SELECT *
                                    FROM users u
                                    WHERE u.email = :email");  
                                    */
        $stmt->bindParam(":id_user", $id_user, PDO::PARAM_INT);
        if ($stmt->execute())
        {
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $response;
        }
        return NULL;
    }


    /**
     *Obtention d'un tournoi par id
     * @param Int $id_tournoi
     */
    public function getTournamentById($id_tournoi) {
        $stmt = $this->pdo->prepare("SELECT t.id_tournoi, t.nom_tournoi, t.id_statut as 'id_statut_tournoi', s.nom_statut as 'statut_tournoi',
                                            u.email, u.nom_user, u.prenom_user, u.id_user, u.id_role, r.droits, u.status as 'statut_utilisateur'
                                    FROM tournois t
                                    INNER JOIN users u ON u.id_user=t.id_user
                                    INNER JOIN roles r ON r.id_role=u.id_role
                                    INNER JOIN statuts s ON s.id_statut = t.id_statut  
                                    WHERE t.id_tournoi = :id_tournoi");
                                   
        $stmt->bindParam(":id_tournoi", $id_tournoi, PDO::PARAM_INT);
        if ($stmt->execute())
        {
            $response = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $response;
        }
        return NULL;
    }

    /**
     *Obtention d'un tournoi par id de l'utislisateur et de l'id du tournoi
     * @param Int $id_current_user
     * @param Int $id_tournoi
     */
    public function getTeamsTournamentByIdAndUserId($id_user, $id_tournoi) {
        $stmt = $this->pdo->prepare("SELECT t.nom_tournoi,u.id_user,u.nom_user, e.id_equipe, e.nom_equipe, g.id_groupe, g.nom_groupe,
                                        p.id_personne, p.nom, p.prenom, p.courriel, p.tel, p.tel_mobile, p.adresse, p.localite, p.pays
                                        FROM users u
                                        INNER JOIN tournois t ON t.id_user = u.id_user
                                        INNER JOIN groupes g ON t.id_tournoi = g.id_tournoi
                                        INNER JOIN equipes e ON g.id_groupe = e.id_groupe
                                        INNER JOIN personnes p ON p.id_equipe = e.id_equipe
                                        WHERE t.id_tournoi LIKE :id_tournoi
                                        AND t.id_user = :id_user");
                                   



        $stmt->bindParam(":id_tournoi", $id_tournoi, PDO::PARAM_INT);
        $stmt->bindParam(":id_user", $id_user, PDO::PARAM_INT);
        if ($stmt->execute())
        {
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $response;
        }
        return NULL;
    }
    /**
     *Obtention d'un tournoi par id de l'utislisateur et de l'id du tournoi
     * @param Int $id_tournoi
     */
    public function getTeamsTournamentById($id_tournoi) {
        $stmt = $this->pdo->prepare("SELECT t.nom_tournoi, e.id_equipe, e.nom_equipe, g.id_groupe, g.nom_groupe
                                        FROM tournois t
                                        INNER JOIN groupes g ON t.id_tournoi = g.id_tournoi
                                        INNER JOIN equipes e ON g.id_groupe = e.id_groupe
                                        WHERE t.id_tournoi LIKE :id_tournoi");

        $stmt->bindParam(":id_tournoi", $id_tournoi, PDO::PARAM_INT);
        if ($stmt->execute()){
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $response;
        }
        return NULL;
    }
/**
     *Obtention d'un tournoi par id de l'utislisateur et de l'id du tournoi
     * @param Int $id_tournoi
     * @param Int $id_groupe
     */
    public function getTeamsByGroupById($id_groupe) {
        $stmt = $this->pdo->prepare("SELECT t.nom_tournoi,u.id_user,u.nom_user, e.id_equipe, e.nom_equipe, g.id_groupe, g.nom_groupe,
                                        p.id_personne, p.nom, p.prenom, p.courriel, p.tel, p.tel_mobile, p.adresse, p.localite, p.pays 
                                        FROM users u
                                        INNER JOIN tournois t ON t.id_user = u.id_user
                                        INNER JOIN groupes g ON t.id_tournoi = g.id_tournoi
                                        INNER JOIN equipes e ON g.id_groupe = e.id_groupe
                                        INNER JOIN personnes p ON p.id_equipe = e.id_equipe
                                        WHERE g.id_groupe LIKE :id_groupe");
                                   
        $stmt->bindParam(":id_groupe", $id_groupe, PDO::PARAM_INT);
        if ($stmt->execute())
        {
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $response;
        }
        return NULL;
    }

/**
     *Obtention d'un tournoi par id de l'utislisateur et de l'id du tournoi
     * @param Int $id_current_user
     * @param Int $id_tournoi
     * @param Int $id_groupe
     */
    public function getTeamsByGroupTournamentByIdAndUserId($id_user, $id_tournoi, $id_groupe) {
        $stmt = $this->pdo->prepare("SELECT t.nom_tournoi, u.id_user, u.nom_user, e.id_equipe, e.nom_equipe, g.id_groupe, g.nom_groupe,
                                        p.id_personne, p.nom, p.prenom, p.courriel, p.tel, p.tel_mobile, p.adresse, p.localite, p.pays 
                                        FROM users u
                                        INNER JOIN tournois t ON t.id_user = u.id_user
                                        INNER JOIN groupes g ON t.id_tournoi = g.id_tournoi
                                        INNER JOIN equipes e ON g.id_groupe = e.id_groupe
                                        INNER JOIN personnes p ON p.id_equipe = e.id_equipe
                                        WHERE t.id_tournoi LIKE :id_tournoi                                        
                                        AND t.id_user LIKE :id_user
                                        AND g.id_groupe LIKE :id_groupe");

        $stmt->bindParam(":id_tournoi", $id_tournoi, PDO::PARAM_INT);
        $stmt->bindParam(":id_user", $id_user, PDO::PARAM_INT);
        $stmt->bindParam(":id_groupe", $id_groupe, PDO::PARAM_INT);
        if ($stmt->execute())
        {
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $response;
        }
        return NULL;
    }
    
/**
     *Obtention des matchs pour un tournoi selon son id du tournoi 
     * @param Int $id_tournoi
     */
    public function getMatchsByTournamentId( $id_tournoi) {
        $stmt = $this->pdo->prepare("SELECT g.nom_groupe, m.id_match,
                                        e.id_equipe as 'id_equipe_home', e.nom_equipe as 'equipe_home',
                                        e2.nom_equipe as 'equipe_visiteur',e.id_equipe as 'id_equipe_visiteur'
                                        FROM tournois t
                                        INNER JOIN groupes g ON g.id_tournoi = t.id_tournoi
                                        INNER JOIN equipes e ON e.id_groupe = g.id_groupe
                                        INNER JOIN matchs m ON m.id_equipe_home = e.id_equipe
                                        INNER JOIN equipes e2 ON m.id_equipe_visiteur = e2.id_equipe
                                        WHERE t.id_tournoi LIKE :id_tournoi");
                                       // AND t.id_user LIKE :id_user");
                                   
        $stmt->bindParam(":id_tournoi", $id_tournoi, PDO::PARAM_INT);
        //$stmt->bindParam(":id_user", $id_user, PDO::PARAM_INT);

        if ($stmt->execute())
        {
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $response;
        }
        return NULL;
    }

/**
     *Obtention des matchs pour une équipe selon son id du tournoi et de l'utilisateur
     * @param Int $id_current_user
     * @param Int $id_tournoi
     */
    public function getTeamMatchsById($id_equipe) {
        $stmt = $this->pdo->prepare("SELECT g.nom_groupe, m.id_match,
                                        e.id_equipe as 'id_equipe_home', e.nom_equipe as 'equipe_home',
                                        e2.nom_equipe as 'equipe_visiteur',e.id_equipe as 'id_equipe_visiteur'
                                        FROM tournois t
                                        INNER JOIN groupes g ON g.id_tournoi = t.id_tournoi
                                        INNER JOIN equipes e ON e.id_groupe = g.id_groupe
                                        INNER JOIN matchs m ON m.id_equipe_home = e.id_equipe
                                        INNER JOIN equipes e2 ON m.id_equipe_visiteur = e2.id_equipe
                                        WHERE (m.id_equipe_home LIKE :id_equipe 
                                                OR
                                                m.id_equipe_visiteur LIKE :id_equipe)");
                                   
        $stmt->bindParam(":id_equipe", $id_equipe, PDO::PARAM_INT);

        if ($stmt->execute())
        {
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $response;
        }
        return NULL;
    }

    /**
     *Obtention des matchs d'un groupe d'un tournoi selon son id du tournoi 
     * @param Int $id_groupe
     */
    public function getMatchsByGroup($id_groupe) {
        $stmt = $this->pdo->prepare("SELECT g.nom_groupe, m.id_match,
                                        e.id_equipe as 'id_equipe_home', e.nom_equipe as 'equipe_home',
                                        e2.nom_equipe as 'equipe_visiteur',e.id_equipe as 'id_equipe_visiteur'
                                        FROM tournois t
                                        INNER JOIN groupes g ON g.id_tournoi = t.id_tournoi
                                        INNER JOIN equipes e ON e.id_groupe = g.id_groupe
                                        INNER JOIN matchs m ON m.id_equipe_home = e.id_equipe
                                        INNER JOIN equipes e2 ON m.id_equipe_visiteur = e2.id_equipe
                                        WHERE g.id_groupe LIKE :id_groupe");
                                   
        $stmt->bindParam(":id_groupe", $id_groupe, PDO::PARAM_INT);

        if ($stmt->execute())
        {
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $response;
        }
        return NULL;
    }


    /**
     * Obtention des tournois
     */
    public function getTournaments() {
        $stmt = $this->pdo->prepare("SELECT * FROM tournois");       
        if ($stmt->execute())
        {
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $response;
        }
        return NULL;
    }


    /**
     * Obtention des matchs listés par terrain pour un tounoi spécifique i 
     * @param Int $id_tournoi
     */
    public function getMatchsPitchesByTournamentId($id_tournoi) {
        $stmt = $this->pdo->prepare("SELECT ter.id_terrain, ter.nom_terrain, g.nom_groupe, m.id_match, e.nom_equipe, e2.nom_equipe, arbitre.id_equipe, arbitre.nom_equipe, dirige.id_user, dirige.nom_user, dirige.prenom_user
                                        FROM tournois t
                                        INNER JOIN groupes g ON g.id_tournoi = t.id_tournoi
                                        INNER JOIN equipes e ON e.id_groupe = g.id_groupe
                                        INNER JOIN matchs m ON m.id_equipe_home = e.id_equipe
                                        INNER JOIN equipes e2 ON m.id_equipe_visiteur = e2.id_equipe
                                        INNER JOIN terrains ter ON ter.id_terrain = m.id_terrain
                                        LEFT JOIN equipes arbitre ON arbitre.id_equipe = m.id_equipe_arbitre
                                        LEFT JOIN users dirige ON dirige.id_user = m.id_user_dirige
                                        WHERE t.id_tournoi LIKE :id_tournoi
                                        GROUP BY id_terrain, ter.nom_terrain, m.id_match");
                                   
        $stmt->bindParam(":id_tournoi", $id_tournoi, PDO::PARAM_INT);

        if ($stmt->execute())
        {
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $response;
        }
        return NULL;
    }

    /**
     * Obtention des matchs pour un terrain spécifique 
     * @param Int $id_terrain
     */
    public function getMatchsByPitchId($id_terrain) {
        $stmt = $this->pdo->prepare("SELECT m.id_match, m.id_equipe_home, e1.nom_equipe, m.id_equipe_visiteur, e2.nom_equipe, t.nom_terrain, arbitre.id_equipe, arbitre.nom_equipe, dirige.id_user, dirige.nom_user, dirige.prenom_user
                                    FROM matchs m
                                    INNER JOIN equipes e1 ON m.id_equipe_home = e1.id_equipe
                                    INNER JOIN equipes e2 ON m.id_equipe_visiteur = e2.id_equipe
                                    INNER JOIN terrains t ON m.id_terrain = t.id_terrain
                                    LEFT JOIN equipes arbitre ON arbitre.id_equipe = m.id_equipe_arbitre
                                    LEFT JOIN users dirige ON dirige.id_user = m.id_user_dirige
                                    WHERE m.id_terrain LIKE :id_terrain");
                                   
        $stmt->bindParam(":id_terrain", $id_terrain, PDO::PARAM_INT);

        if ($stmt->execute())
        {
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $response;
        }
        return NULL;
    }


    /**
     * Obtention le classement d'un groupe par son id 
     * @param Int $id_groupe
     */
    public function getRankingByGroupID($id_groupe) {
        $group = new Group($this->pdo, $id_groupe);
        return $group->getRanking();
    }

    /**
     * Obtention du détail des équipes d'un groupe par l'id du groupe 
     * @param Int $id_groupe
     */
    public function getTeamDetailsgByGroupID($id_groupe) {
        $group = new Group($this->pdo, $id_groupe);
        return $group->getTeams();
    }


    /**
     * Creation nouveau role
     * @param String $id
     * @param String $droits nom tel que : Admin, responsable, arbitre
     */
    public function createRole($id, $droits) {
        try{
            // requete d'insertion
            $stmt = $this->pdo->prepare("INSERT INTO roles(id_role, droits) values (:id, :droits)");
            $stmt->bindParam(":droits", $droits, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            
            $result = $stmt->execute();

            //Vérifiez pour une insertion réussie
            if ($result) {
                // Utilisateur inséré avec succès
                return $id;
            } else {
                //Échec de la création de l'utilisateur
                return FALSE;;
            }
        }catch(EXCEPTION $e){
            return FALSE;
        }
            

    }

    /**
     *Obtention d'un role par id
     * @param Int $id
     */
    public function getRoleById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM roles WHERE id_role = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            $role = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $role;
        } else {
            return NULL;
        }
    }

    /**
     *Obtention de tous les rolesd
     */
    public function getRoles() {
        $stmt = $this->pdo->prepare("SELECT * FROM roles");
        
        if ($stmt->execute()) {
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $roles;
        } else {
            return NULL;
        }
    }


}
?>
