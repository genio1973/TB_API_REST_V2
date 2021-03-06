 <?php

/**
 * Classe pour gérer toutes les opérations de db
 * Cette classe aura les méthodes CRUD pour les tables de base de données
 *

 */
class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        //Ouverture connexion db
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    /* ------------- méthodes de la table `user` ------------------ */

    /**
     * Creation nouvel utilisateur
     * @param String $role
     * @param String $email email de connexion
     * @param String $password mot de passe de connexion
     */
    public function createUser($email, $password, $id_role) {
        require_once 'PassHash.php';


        // Vérifiez d'abord si l'utilisateur existe déjà dans db
        if (!$this->isUserExists($email)) {
            //Générer un hash de mot de passe
            $mot_de_passe = PassHash::hash($password);

            // Générer API key
            $api_key = $this->generateApiKey();

            // requete d'insertion
            $tokenExpiration = date('Y-m-d H:i:s', strtotime('+2 hour'));//the expiration date will be in two hour from the current moment

            $stmt = $this->conn->prepare("INSERT INTO user(email, mot_de_passe, token, token_expire, id_role) values (?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssi', $email, $mot_de_passe, $api_key, $tokenExpiration, $id_role);

            $result = $stmt->execute();

            $stmt->close();

            //Vérifiez pour une insertion réussie
            if ($result) {
                // Utilisateur inséré avec succès
                return USER_CREATED_SUCCESSFULLY;
            } else {
                //Échec de la création de l'utilisateur
                return USER_CREATE_FAILED;
            }
        } else {
            //Utilisateur avec la même email existait déjà dans la db
            return USER_ALREADY_EXISTED;
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
        $stmt = $this->conn->prepare("SELECT mot_de_passe FROM user WHERE email = ?");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $stmt->bind_result($mot_de_passe);

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Utilisateur trouvé avec l'e-mail
            // Maintenant, vérifier le mot de passe

            $stmt->fetch();

            $stmt->close();

            if (PassHash::check_password($mot_de_passe, $password)) {
                // Mot de passe utilisateur est correcte
                return TRUE;
            } else {
                // mot de passe utilisateur est incorrect
                return FALSE;
            }
        } else {
            $stmt->close();

            // utilisateur n'existe pas avec l'e-mail
            return FALSE;
        }
    }

    /**
     * Vérification de l'utilisateur en double par adresse e-mail
     * @param String $email email à vérifier dans la db
     * @return boolean
     */
    private function isUserExists($email) {
        $stmt = $this->conn->prepare("SELECT id from user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     *Obtention de l'utilisateur par email
     * @param String $email
     */
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT name, email, mot_de_passe, token, token_expire, id_role FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $stmt->bind_result($name, $email, $api_key, $token, $tokenExpiration, $id_role);
            $stmt->fetch();
            $user = array();
            $user["name"] = $name;
            $user["email"] = $email;
            $user["token"] = $api_key;
            //$user["token_expire"] = $token;
            $user["token_expire"] = $tokenExpiration;
            $user["id_role"] = $id_role;
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Obtention de la clé API de l'utilisateur
     * @param String $user_id clé primaire de l'utilisateur
     */
    public function getApiKeyById($user_id) {
        $stmt = $this->conn->prepare("SELECT token FROM user WHERE id = ?");
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
        $stmt = $this->conn->prepare("SELECT id FROM user WHERE token = ?");
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
        $stmt = $this->conn->prepare("SELECT token_expire from user WHERE token = ?");
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
        $stmt = $this->conn->prepare("SELECT token_expire from user WHERE token = ? AND id = ?");
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
        $stmt = $this->conn->prepare("SELECT token_expire, id_role from user WHERE token = ? AND id = ?");
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

    /* ------------- méthodes table`tasks` ------------------ */

    /**
     * Creation nouvelle tache
     * @param String $user_id id de l'utilisateur à qui la tâche appartient
     * @param String $task texte de la tache
     */
/*
    public function createTask($user_id, $task) {
        $stmt = $this->conn->prepare("INSERT INTO tasks(task) VALUES(?)");
        $stmt->bind_param("s", $task);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            // ligne de tâche créé
            // maintenant assigner la tâche à l'utilisateur
            $new_task_id = $this->conn->insert_id;
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
        $stmt = $this->conn->prepare("SELECT t.id, t.task, t.status, t.created_at from tasks t, user_tasks ut WHERE t.id = ? AND ut.task_id = t.id AND ut.user_id = ?");
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
        $stmt = $this->conn->prepare("SELECT t.* FROM tasks t, user_tasks ut WHERE t.id = ut.task_id AND ut.user_id = ?");
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
        $stmt = $this->conn->prepare("UPDATE tasks t, user_tasks ut set t.task = ?, t.status = ? WHERE t.id = ? AND t.id = ut.task_id AND ut.user_id = ?");
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
        $stmt = $this->conn->prepare("DELETE t FROM tasks t, user_tasks ut WHERE t.id = ? AND ut.task_id = t.id AND ut.user_id = ?");
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
        $stmt = $this->conn->prepare("INSERT INTO user_tasks(user_id, task_id) values(?, ?)");
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
            $stmt = $this->conn->prepare("INSERT INTO role(id, droits) values (?, ?)");
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
        $stmt = $this->conn->prepare("SELECT * FROM role WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $stmt->bind_result($id, $droits);
            $stmt->fetch();
            $role = array();
            $role["id"] = $id;
            $role["droits"] = $droits;
            $stmt->close();
            return $role;
        } else {
            return NULL;
        }
    }








}
?>
