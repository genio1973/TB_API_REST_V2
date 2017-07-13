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
        $stmt = $this->pdo->prepare("SELECT id_user, email, mot_de_passe, token, token_expire, id_role, nom_user, prenom_user, status FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        if ($stmt->execute())
        {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $user;
        }
        return NULL;
    }

    /**
     *Obtention des utilisateurs
     */
    public function getUsers() {
        $stmt = $this->pdo->prepare("SELECT * FROM users");       
        if ($stmt->execute())
        {
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
        $stmt = $this->pdo->prepare("SELECT token FROM user WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $stmt->bind_result($api_key);
            $stmt->close();
            return $api_key;
        } else {
            return NULL;
        }
    }

    /**
     * Obtention de l'identifiant de l'utilisateur par clé API
     * @param String $api_key
     */
    public function getUserId($api_key) {
        $stmt = $this->pdo->prepare("SELECT id FROM user WHERE token = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $stmt->bind_result($user_id);
            $stmt->fetch();

            $stmt->close();
            return $user_id;
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
        $stmt = $this->pdo->prepare("SELECT token_expire from user WHERE token = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $stmt->bind_result($tokenExpiration);
            $stmt->fetch();
            $stmt->close();
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
        $stmt = $this->pdo->prepare("SELECT token_expire from user WHERE token = ? AND id = ?");
        $stmt->bind_param("si", $api_key, $id);
        if ($stmt->execute()) {
            $stmt->bind_result($tokenExpiration);
            $stmt->fetch();
            $stmt->close();
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
        $stmt = $this->pdo->prepare("SELECT token_expire, id_role from user WHERE token = ? AND id = ?");
        $stmt->bind_param("si", $api_key, $id);
        if ($stmt->execute()) {
            $stmt->bind_result($tokenExpiration, $id_role);
            $stmt->fetch();
            $stmt->close();
            //API Key expiration ?
            if( $id_role <= $role && $tokenExpiration > date('Y-m-d H:i:s')){
                return true;
            } 
        }
        return false;
    }


    /**
     * Génération aléatoire unique MD5 String pour utilisateur clé Api
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }



/************************************************************************************
------------- méthode de la table`tournois` ------------------
*************************************************************************************/

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
        /*
        $stmt = $this->pdo->prepare("SELECT *
                                    FROM users u
                                    WHERE u.email = :email");  
                                    */
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        if ($stmt->execute())
        {
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $tournois;
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
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $tournois;
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
            $tournoi = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $tournoi;
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
                                        INNER JOIN personnes p ON p.id_personne = e.id_personne
                                        WHERE t.id_tournoi LIKE :id_tournoi
                                        AND t.id_user = :id_user");
                                   



        $stmt->bindParam(":id_tournoi", $id_tournoi, PDO::PARAM_INT);
        $stmt->bindParam(":id_user", $id_user, PDO::PARAM_INT);
        if ($stmt->execute())
        {
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $tournois;
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
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $tournois;
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
                                        INNER JOIN personnes p ON p.id_personne = e.id_personne
                                        WHERE g.id_groupe LIKE :id_groupe");
                                   
        $stmt->bindParam(":id_groupe", $id_groupe, PDO::PARAM_INT);
        if ($stmt->execute())
        {
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $tournois;
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
        $stmt = $this->pdo->prepare("SELECT t.nom_tournoi,u.id_user,u.nom_user, e.id_equipe, e.nom_equipe, g.id_groupe, g.nom_groupe,
                                        p.id_personne, p.nom, p.prenom, p.courriel, p.tel, p.tel_mobile, p.adresse, p.localite, p.pays 
                                        FROM users u
                                        INNER JOIN tournois t ON t.id_user = u.id_user
                                        INNER JOIN groupes g ON t.id_tournoi = g.id_tournoi
                                        INNER JOIN equipes e ON g.id_groupe = e.id_groupe
                                        INNER JOIN personnes p ON p.id_personne = e.id_personne
                                        WHERE t.id_tournoi LIKE :id_tournoi                                        
                                        AND t.id_user LIKE :id_user
                                        AND g.id_groupe LIKE :id_groupe");

        $stmt->bindParam(":id_tournoi", $id_tournoi, PDO::PARAM_INT);
        $stmt->bindParam(":id_user", $id_user, PDO::PARAM_INT);
        $stmt->bindParam(":id_groupe", $id_groupe, PDO::PARAM_INT);
        if ($stmt->execute())
        {
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $tournois;
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
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $tournois;
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
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $tournois;
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
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $tournois;
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
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $tournois;
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
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $tournois;
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
            $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->pdo = NULL;
            return $tournois;
        }
        return NULL;
    }




    /* ------------- méthodes table`tasks` ------------------ */

    /**
     * Creation nouvelle tache
     * @param String $user_id id de l'utilisateur à qui la tâche appartient
     * @param String $task texte de la tache
     */
/*
    public function createTask($user_id, $task) {
        $stmt = $this->pdo->prepare("INSERT INTO tasks(task) VALUES(?)");
        $stmt->bind_param("s", $task);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            // ligne de tâche créé
            // maintenant assigner la tâche à l'utilisateur
            $new_task_id = $this->pdo->insert_id;
            $res = $this->createUserTask($user_id, $new_task_id);
            if ($res) {
                // tâche créée avec succès
                return $new_task_id;
            } else {
                //tâche n'a pas pu être créé
                return NULL;
            }
        } else {
            //tâche n'a pas pu être créé
            return NULL;
        }
    }
*/
    /**
     * Obtention d'une seule tâche
     * @param String $task_id id de la tâche
     */
/*
    public function getTask($task_id, $user_id) {
        $stmt = $this->pdo->prepare("SELECT t.id, t.task, t.status, t.created_at from tasks t, user_tasks ut WHERE t.id = ? AND ut.task_id = t.id AND ut.user_id = ?");
        $stmt->bind_param("ii", $task_id, $user_id);
        if ($stmt->execute()) {
            $res = array();
            $stmt->bind_result($id, $task, $token, $tokenExpiration, $id_role);
            $stmt->fetch();
            $res["id"] = $id;
            $res["task"] = $task;
            $res["status"] = $token;
            $res["created_at"] = $tokenExpiration, $id_role;
            $stmt->close();
            return $res;
        } else {
            return NULL;
        }
    }
*/

    /**
     *Obtention de  tous les  tâches de l'utilisateur
     * @param String $user_id id de l'utilisateur
     */
/*
    public function getAllUserTasks($user_id) {
        $stmt = $this->pdo->prepare("SELECT t.* FROM tasks t, user_tasks ut WHERE t.id = ut.task_id AND ut.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
*/
    /**
     * Mise à jour de la tâche
     * @param String $task_id id de la tâche
     * @param String $task Le texte de la tâche
     * @param String $token le statut de la tâche
     */
  /*
    public function updateTask($user_id, $task_id, $task, $token) {
        $stmt = $this->pdo->prepare("UPDATE tasks t, user_tasks ut set t.task = ?, t.status = ? WHERE t.id = ? AND t.id = ut.task_id AND ut.user_id = ?");
        $stmt->bind_param("siii", $task, $token, $task_id, $user_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
*/
    /**
     * Suppression d'une tâche
     * @param String $task_id id de la tâche à supprimer
     */
  /*
    public function deleteTask($user_id, $task_id) {
        $stmt = $this->pdo->prepare("DELETE t FROM tasks t, user_tasks ut WHERE t.id = ? AND ut.task_id = t.id AND ut.user_id = ?");
        $stmt->bind_param("ii", $task_id, $user_id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
*/
    /* ------------- méthode de la table`user_tasks` ------------------ */

    /**
     * Fonction d'assigner une tâche à l'utilisateur
     * @param String $user_id id de l'utilisateur
     * @param String $task_id id de la tâche
     */
  /*
    public function createUserTask($user_id, $task_id) {
        $stmt = $this->pdo->prepare("INSERT INTO user_tasks(user_id, task_id) values(?, ?)");
        $stmt->bind_param("ii", $user_id, $task_id);
        $result = $stmt->execute();

        if (false === $result) {
            die('execute() failed: ' . htmlspecialchars($stmt->error));
        }
        $stmt->close();
        return $result;
    }
*/






/* ------------- méthodes de la table `role` ------------------ */

    /**
     * Creation nouveau role
     * @param String $id
     * @param String $droits nom tel que : Admin, responsable, arbitre
     */
    public function createRole($id, $droits) {

            // requete d'insertion
            $stmt = $this->pdo->prepare("INSERT INTO role(id, droits) values (?, ?)");
            $stmt->bind_param('is', $id, $droits);
            $result = $stmt->execute();
            $stmt->close();

            //Vérifiez pour une insertion réussie
            if ($result) {
                // Utilisateur inséré avec succès
                return ROLE_CREATED_SUCCESSFULLY;
            } else {
                //Échec de la création de l'utilisateur
                return ROLE_CREATE_FAILED;
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
